# SKILL: Rate Limiting

## What It Is
Rate limiting controls how many requests a client (identified by IP, user ID, API key, etc.) can make to your API within a given time window. It prevents API abuse, DDoS attacks, resource exhaustion from a single bad actor, and ensures fair usage among all clients.

## When to Apply This Concept
- Public API endpoints (anyone can call them)
- Expensive operations: AI inference, file uploads, email sending
- Authentication endpoints (prevent brute-force attacks)
- Any endpoint that costs money per call (external APIs)
- Multi-tenant apps where one tenant shouldn't starve others

---

## Rate Limiting Algorithms

### 1. Fixed Window Counter
Count requests in a fixed time window (e.g., 00:00 → 01:00). Reset at the window boundary.
```
00:00–01:00: [request 1, request 2, ... , request 60] → 60/min limit hit
01:00–02:00: counter resets → fresh 60 requests allowed
```
**Problem:** A client can burst 60 at 00:59 + 60 at 01:01 = 120 requests in 2 seconds.

### 2. Sliding Window Log
Keep a log of all request timestamps. Count only requests within the last N seconds.
```
Current time: 12:05:30
Window: last 60 seconds (12:04:30 → 12:05:30)
Count requests in that range → enforce limit
```
**Pro:** No burst problem. **Con:** High memory usage (stores all timestamps).

### 3. Token Bucket ← Laravel's approach
A bucket holds N tokens. Each request consumes 1 token. Tokens refill at a fixed rate.
```
Bucket size: 60 tokens
Refill rate: 1 token/second
Request arrives: consume 1 token
No tokens left: reject request (429)
Tokens accumulate when idle: allows bursting up to bucket size
```
**Pro:** Allows short bursts up to bucket size. Good for APIs where some bursting is acceptable.

### 4. Leaky Bucket
Requests enter a queue and are processed at a fixed rate, regardless of burst.
```
Queue capacity: 60 requests
Drain rate: 1 request/second
```
**Pro:** Smooth, predictable processing rate.
**Con:** Added queue latency; requests wait even when there's capacity.

---

## Laravel Rate Limiting

### Basic Middleware
```php
// Apply to routes
Route::middleware('throttle:60,1')->group(function () {
    // 60 requests per 1 minute per user/IP
    Route::get('/scholarships', [ScholarshipController::class, 'index']);
});
```

### Custom Rate Limiters (Recommended)
```php
// app/Providers/AppServiceProvider.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    // ─── Public endpoints ───────────────────────────────────────────────
    RateLimiter::for('public-api', function (Request $request) {
        return Limit::perMinute(30)
            ->by($request->ip())
            ->response(function (Request $request, array $headers) {
                return response()->json([
                    'error'       => 'Too many requests.',
                    'retry_after' => $headers['Retry-After'],
                ], 429, $headers);
            });
    });

    // ─── Authenticated applicants ────────────────────────────────────────
    RateLimiter::for('applicant', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    // ─── Admin users (higher limit) ──────────────────────────────────────
    RateLimiter::for('admin', function (Request $request) {
        return Limit::perMinute(300)->by($request->user()?->id);
    });

    // ─── Document upload (expensive: calls Hugging Face) ─────────────────
    RateLimiter::for('document-upload', function (Request $request) {
        return [
            Limit::perHour(10)->by($request->user()->id),   // 10 uploads/hour
            Limit::perDay(20)->by($request->user()->id),    // 20 uploads/day
        ];
    });

    // ─── Authentication endpoints (brute-force protection) ───────────────
    RateLimiter::for('auth', function (Request $request) {
        return [
            Limit::perMinute(5)->by($request->ip()),         // 5 attempts/min by IP
            Limit::perMinute(3)->by($request->input('email')), // 3 attempts/min by email
        ];
    });

    // ─── Password reset ───────────────────────────────────────────────────
    RateLimiter::for('password-reset', function (Request $request) {
        return Limit::perHour(3)->by($request->ip());
    });
}
```

### Apply in Routes
```php
// routes/api.php
Route::prefix('v1')->group(function () {

    // Public — low limit
    Route::middleware('throttle:public-api')->group(function () {
        Route::get('/scholarships', [ScholarshipController::class, 'index']);
        Route::get('/scholarships/{id}', [ScholarshipController::class, 'show']);
    });

    // Auth endpoints — strict brute-force protection
    Route::middleware('throttle:auth')->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login']);
        Route::post('/auth/register', [AuthController::class, 'register']);
    });

    Route::middleware('throttle:password-reset')->group(function () {
        Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    });

    // Authenticated applicants
    Route::middleware(['auth:sanctum', 'throttle:applicant'])->group(function () {
        Route::get('/applications', [ApplicationController::class, 'index']);
        Route::post('/applications', [ApplicationController::class, 'store']);

        // Stricter limit for expensive operations
        Route::middleware('throttle:document-upload')
            ->post('/documents/upload', [DocumentController::class, 'upload']);
    });

    // Admin — higher limits
    Route::middleware(['auth:sanctum', 'role:admin', 'throttle:admin'])->group(function () {
        Route::get('/admin/analytics', [AnalyticsController::class, 'index']);
    });
});
```

---

## Rate Limit Headers in Responses

Laravel automatically adds these headers (RFC 6585):
```
X-RateLimit-Limit: 60          ← max requests allowed
X-RateLimit-Remaining: 45      ← requests left in window
Retry-After: 30                ← seconds until reset (on 429)
X-RateLimit-Reset: 1718000000  ← Unix timestamp of window reset
```

### Custom 429 Response
```php
RateLimiter::for('applicant', function (Request $request) {
    return Limit::perMinute(60)
        ->by($request->user()?->id)
        ->response(function (Request $request, array $headers) {
            return response()->json([
                'error'       => 'Rate limit exceeded. Please slow down.',
                'limit'       => 60,
                'retry_after' => (int) $headers['Retry-After'],
                'reset_at'    => now()->addSeconds($headers['Retry-After'])->toISOString(),
            ], 429, $headers);
        });
});
```

---

## Redis-Backed Rate Limiting (Distributed)

When running multiple app instances, rate limiting must use Redis (not in-memory) so all instances share the same counters.

```env
CACHE_DRIVER=redis
```

Laravel's `ThrottleRequests` middleware automatically uses the cache driver, which is Redis. No extra config needed — just make sure `CACHE_DRIVER=redis`.

---

## Sliding Window with Redis (Advanced)

For precise sliding window rate limiting (not fixed window):
```php
// app/Services/SlidingWindowRateLimiter.php
namespace App\Services;

use Illuminate\Support\Facades\Redis;

class SlidingWindowRateLimiter
{
    public function attempt(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        $now      = microtime(true);
        $window   = $now - $windowSeconds;
        $redisKey = "rl:{$key}";

        // Use a sorted set: score = timestamp, member = unique request ID
        Redis::pipeline(function ($pipe) use ($redisKey, $window, $now) {
            $pipe->zremrangebyscore($redisKey, 0, $window);    // remove old entries
            $pipe->zadd($redisKey, $now, uniqid('', true));    // add current
            $pipe->expire($redisKey, 60);                       // auto-cleanup
        });

        $count = Redis::zcount($redisKey, $window, '+inf');

        return $count <= $maxAttempts;
    }

    public function tooManyAttempts(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        return !$this->attempt($key, $maxAttempts, $windowSeconds);
    }
}
```

---

## Protecting Supabase from Rate Limit Exhaustion

Supabase has its own rate limits. Your app must not exhaust them.

| Supabase Limit              | Default         | Mitigation                     |
|-----------------------------|-----------------|--------------------------------|
| Auth: email signups         | 3/hour per IP   | Add your own pre-validation    |
| Auth: sign-ins              | 30/5min per IP  | Add your own auth rate limiter |
| Realtime connections        | 200/project     | Use connection pooling         |
| Storage upload              | 100MB file max  | Validate before uploading      |
| Database API requests       | 500 req/sec     | Cache heavy read queries       |

---

## AEGIS Rate Limit Configuration Summary

| Endpoint                       | Limit              | By                  | Reason                     |
|--------------------------------|--------------------|---------------------|----------------------------|
| `GET /api/v1/scholarships`     | 30/min             | IP                  | Public; prevent scraping   |
| `POST /api/v1/auth/login`      | 5/min + 3/min      | IP + email          | Brute-force protection     |
| `POST /api/v1/auth/register`   | 5/min              | IP                  | Prevent bot registrations  |
| `POST /api/v1/auth/forgot-pwd` | 3/hour             | IP                  | Prevent email flooding     |
| `GET /api/v1/applications`     | 60/min             | user_id             | Normal usage               |
| `POST /api/v1/applications`    | 10/min             | user_id             | Prevent duplicate submits  |
| `POST /api/v1/documents/upload`| 10/hour + 20/day   | user_id             | HuggingFace cost control   |
| `GET /api/v1/admin/*`          | 300/min            | user_id             | Admin needs more headroom  |

---

## Anti-Patterns to Avoid
- ❌ Rate limiting only by IP — authenticated users can share IPs (NAT); use user_id for auth routes
- ❌ No rate limiting on auth endpoints — brute-force attacks become trivial
- ❌ Using in-memory rate limiting with multiple server instances (each instance has separate counters)
- ❌ Rate limiting the health check endpoint — load balancer needs it unrestricted
- ❌ Setting the same limit for all endpoints — tailor limits to the cost and risk of each operation
- ❌ Not returning `Retry-After` header — clients can't implement proper backoff

---

## Quick Reference
```php
// Simple
Route::middleware('throttle:60,1')->group(fn() => ...);

// Named limiter
RateLimiter::for('name', fn(Request $r) => Limit::perMinute(60)->by($r->user()->id));
Route::middleware('throttle:name')->group(fn() => ...);

// Multiple limits
return [
    Limit::perHour(10)->by($r->user()->id),
    Limit::perDay(50)->by($r->user()->id),
];

// Check programmatically
if (RateLimiter::tooManyAttempts('key', 5)) {
    $seconds = RateLimiter::availableIn('key');
    return response()->json(['retry_after' => $seconds], 429);
}
RateLimiter::hit('key', $decaySeconds = 60);
RateLimiter::clear('key');
```
