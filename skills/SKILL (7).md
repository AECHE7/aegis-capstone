# SKILL: API Gateway

## What It Is
An API Gateway is a single entry point that sits in front of all backend services and handles cross-cutting concerns — authentication, rate limiting, logging, request routing, and transformation — before requests reach your application logic. Think of it as the "front door" of your system that enforces rules uniformly.

## When to Apply This Concept
- You need consistent auth enforcement across all routes
- You want to add rate limiting to protect your API from abuse
- You have multiple backend services (API versioning, routing to microservices)
- You need request/response transformation or validation at the boundary
- You want centralized logging of all API traffic
- You're building a public API that third parties will consume

---

## Gateway Responsibilities

```
Client Request
    ↓
[API Gateway Layer]
    ├── 1. SSL Termination (HTTPS)
    ├── 2. Authentication (Sanctum token validation)
    ├── 3. Authorization (permission check)
    ├── 4. Rate Limiting (throttle by user/IP)
    ├── 5. Request Validation (schema, headers)
    ├── 6. Logging & Tracing (audit trail)
    ├── 7. Request Routing (v1 vs v2, public vs private)
    └── 8. Response Transformation (format, filtering)
    ↓
Application Controllers / Services
```

---

## Laravel as Its Own API Gateway

For a monolith Laravel app, the middleware pipeline IS the API Gateway. Structure it intentionally.

### Middleware Stack Architecture
```php
// bootstrap/app.php (Laravel 11) or app/Http/Kernel.php (Laravel 10)

// Global middleware (runs on every request)
protected $middleware = [
    \App\Http\Middleware\TrustProxies::class,        // Render/Cloudflare proxy headers
    \Illuminate\Http\Middleware\HandleCors::class,   // CORS
    \App\Http\Middleware\ForceHttpsInProduction::class,
];

// API route middleware group
protected $middlewareGroups = [
    'api' => [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',  // rate limit
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];

// Named middleware
protected $routeMiddleware = [
    'auth'             => \App\Http\Middleware\Authenticate::class,
    'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'role'             => \App\Http\Middleware\CheckRole::class,
    'api.version'      => \App\Http\Middleware\ApiVersion::class,
    'audit'            => \App\Http\Middleware\AuditLog::class,
];
```

---

## API Versioning (Route Routing)

```php
// routes/api.php

// V1 routes
Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public endpoints
    Route::prefix('public')->group(function () {
        Route::get('/scholarships', [ScholarshipController::class, 'index']);
        Route::get('/scholarships/{id}', [ScholarshipController::class, 'show']);
    });

    // Authenticated endpoints
    Route::middleware(['auth:sanctum', 'throttle:applicant'])->group(function () {
        Route::apiResource('applications', ApplicationController::class);
        Route::post('documents/upload', [DocumentController::class, 'upload']);
    });

    // Admin endpoints
    Route::middleware(['auth:sanctum', 'role:admin', 'throttle:admin'])->group(function () {
        Route::prefix('admin')->group(function () {
            Route::apiResource('scholarships', Admin\ScholarshipController::class);
            Route::get('analytics/dashboard', [Admin\AnalyticsController::class, 'dashboard']);
            Route::get('documents/review', [Admin\DocumentController::class, 'review']);
        });
    });
});

// V2 routes (future — new response shape or breaking changes)
Route::prefix('v2')->name('api.v2.')->group(function () {
    // ...
});
```

---

## Authentication Middleware (Sanctum)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),
```

```php
// Issuing tokens on login
public function login(Request $request): JsonResponse
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        throw ValidationException::withMessages([
            'email' => ['Invalid credentials.'],
        ]);
    }

    $user  = Auth::user();
    $token = $user->createToken('aegis-token', $user->getAbilities())->plainTextToken;

    return response()->json(['token' => $token, 'user' => $user]);
}
```

---

## Role & Permission Middleware

```php
// app/Http/Middleware/CheckRole.php
namespace App\Http\Middleware;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}
```

```php
// Usage in routes
Route::middleware(['auth:sanctum', 'role:admin,scholarship_officer'])->group(function () {
    Route::get('/admin/applications', [Admin\ApplicationController::class, 'index']);
});
```

---

## Rate Limiting (Per-Role, Per-Route)

```php
// app/Providers/AppServiceProvider.php (or RouteServiceProvider)
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    // Applicant: 60 req/min
    RateLimiter::for('applicant', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    // Admin: 300 req/min
    RateLimiter::for('admin', function (Request $request) {
        return Limit::perMinute(300)->by($request->user()?->id);
    });

    // AI document upload: 10/hour (expensive operation)
    RateLimiter::for('document-upload', function (Request $request) {
        return [
            Limit::perHour(10)->by($request->user()->id),
            Limit::perDay(30)->by($request->user()->id),
        ];
    });

    // Public API: 30 req/min by IP
    RateLimiter::for('public', function (Request $request) {
        return Limit::perMinute(30)->by($request->ip());
    });
}
```

```php
// Apply in routes
Route::middleware(['throttle:document-upload'])
    ->post('/documents/upload', [DocumentController::class, 'upload']);
```

---

## Audit Logging Middleware

```php
// app/Http/Middleware/AuditLog.php
class AuditLog
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log mutating operations
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            \App\Models\AuditLog::create([
                'user_id'     => $request->user()?->id,
                'action'      => $request->method() . ' ' . $request->path(),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'status_code' => $response->getStatusCode(),
                'payload'     => $this->sanitize($request->except(['password', 'token'])),
            ]);
        }

        return $response;
    }

    private function sanitize(array $data): array
    {
        // Remove sensitive fields before logging
        return array_diff_key($data, array_flip(['password', 'secret', 'token', 'credit_card']));
    }
}
```

---

## CORS Configuration (Important for Supabase + Frontend)

```php
// config/cors.php
return [
    'paths'                    => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods'          => ['*'],
    'allowed_origins'          => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        'https://yourdomain.com',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers'          => ['*'],
    'exposed_headers'          => [],
    'max_age'                  => 0,
    'supports_credentials'     => true, // required for Sanctum cookie auth
];
```

---

## External API Gateway Options (When You Outgrow Laravel Middleware)

| Tool            | Best For                                    | Notes                          |
|-----------------|---------------------------------------------|--------------------------------|
| **Nginx**       | Routing, SSL, basic rate limiting           | Free, self-managed             |
| **Cloudflare**  | DDoS, edge rate limiting, WAF               | Free tier available            |
| **Kong**        | Microservices, plugin ecosystem             | Complex to self-host           |
| **AWS API GW**  | AWS-hosted microservices                    | Pay-per-request                |
| **Traefik**     | Docker/Kubernetes service routing           | Auto-discovery                 |

For AEGIS on Render + Cloudflare: **Cloudflare handles edge** + **Laravel middleware handles app-level concerns**. No need for Kong or AWS API Gateway at this scale.

---

## AEGIS API Gateway Checklist
- [ ] All API routes under `/api/v1/` prefix
- [ ] Public routes separated from auth-required routes
- [ ] Admin routes gated with `role:admin` middleware
- [ ] Rate limits defined per role: public, applicant, admin
- [ ] Document upload endpoint has its own strict rate limit
- [ ] CORS configured to allow only your frontend domain
- [ ] Audit logging middleware on all mutating routes
- [ ] Sanctum token abilities scoped per user role
- [ ] Health check endpoint (`/api/health`) excluded from auth/rate-limit

---

## Anti-Patterns to Avoid
- ❌ Mixing auth logic in controllers — it belongs in middleware
- ❌ No rate limiting on public endpoints (open to DDoS/scraping)
- ❌ CORS set to `*` (allow all origins) in production
- ❌ Logging raw request bodies that contain passwords or tokens
- ❌ Exposing internal error messages (stack traces) in API responses
