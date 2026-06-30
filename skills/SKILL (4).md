# SKILL: Service Discovery

## What It Is
Service Discovery is the mechanism by which services in a distributed system automatically detect and communicate with each other without hardcoded addresses. When instances scale up/down or restart with new IPs, other services need a way to find them dynamically.

## When to Apply This Concept
- You have multiple services that call each other (microservices)
- Services are deployed in environments where IPs/ports change dynamically (containers, auto-scaling)
- You need health-aware routing (only route to healthy instances)
- You're managing multiple third-party service endpoints (Hugging Face, Supabase, Redis, Render)

---

## How It Works

### Client-Side Discovery
```
Service A → Query Registry → Get list of healthy Service B addresses
         → Load balance among them → Call Service B directly
```

### Server-Side Discovery
```
Service A → Call Load Balancer → Load Balancer queries Registry
                              → Routes to healthy Service B instance
```

---

## Service Discovery in Practice for AEGIS

AEGIS is a monolith on Render — not a full microservices setup. But service discovery principles still apply in two ways:

1. **Internal service registry** — your app's own services (HuggingFace, Supabase, Redis, Email) are "discovered" via environment variables and config.
2. **Health-aware routing** — knowing which of your dependencies are currently healthy before calling them.

---

## Level 1: Environment-Based Service Locator (Your Current Level)

This is the simplest and most appropriate form of service discovery for a Laravel monolith. Services are registered via `.env` and resolved via `config/services.php`.

```php
// config/services.php — Your internal service registry
return [
    'huggingface' => [
        'url'       => env('HUGGINGFACE_API_URL', 'https://api-inference.huggingface.co/models/microsoft/resnet-50'),
        'token'     => env('HUGGINGFACE_TOKEN'),
        'timeout'   => env('HUGGINGFACE_TIMEOUT', 30),
    ],

    'supabase' => [
        'url'         => env('SUPABASE_URL'),
        'key'         => env('SUPABASE_KEY'),
        'service_key' => env('SUPABASE_SERVICE_KEY'),
    ],

    'redis' => [
        'host'     => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port'     => env('REDIS_PORT', 6379),
    ],

    'mail' => [
        'driver' => env('MAIL_MAILER', 'smtp'),
        'host'   => env('MAIL_HOST'),
        'port'   => env('MAIL_PORT', 587),
        'user'   => env('MAIL_USERNAME'),
        'pass'   => env('MAIL_PASSWORD'),
    ],
];
```

---

## Level 2: Service Registry Pattern (Internal)

Build a service registry class that centralizes how services are resolved, health-checked, and swapped.

```php
// app/Services/ServiceRegistry.php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ServiceRegistry
{
    private static array $services = [];

    public static function register(string $name, callable $factory): void
    {
        static::$services[$name] = $factory;
    }

    public static function resolve(string $name): mixed
    {
        if (!isset(static::$services[$name])) {
            throw new \RuntimeException("Service [{$name}] not registered.");
        }

        return app()->call(static::$services[$name]);
    }

    public static function health(): array
    {
        $checks = [
            'redis'       => fn() => \Illuminate\Support\Facades\Redis::ping(),
            'database'    => fn() => \DB::connection()->getPdo() !== null,
            'huggingface' => fn() => Http::timeout(5)->head(config('services.huggingface.url'))->successful(),
        ];

        return collect($checks)->mapWithKeys(function ($check, $name) {
            try {
                $healthy = $check();
                return [$name => ['status' => 'healthy', 'latency_ms' => null]];
            } catch (\Exception $e) {
                return [$name => ['status' => 'unhealthy', 'error' => $e->getMessage()]];
            }
        })->all();
    }
}
```

---

## Level 3: Health Check Endpoint (Service Self-Announcement)

Every service you deploy should announce its own health. Load balancers, monitoring tools, and other services use this.

```php
// routes/api.php
Route::get('/health', [HealthController::class, 'check'])->name('health');
Route::get('/health/detailed', [HealthController::class, 'detailed'])
    ->middleware(['auth:sanctum', 'role:admin']);
```

```php
// app/Http/Controllers/HealthController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\{Cache, DB, Redis};

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        // Simple: just HTTP 200 for load balancer
        return response()->json(['status' => 'ok', 'timestamp' => now()->toISOString()]);
    }

    public function detailed(): JsonResponse
    {
        $checks = $this->runChecks();
        $healthy = collect($checks)->every(fn($c) => $c['status'] === 'healthy');

        return response()->json([
            'status'    => $healthy ? 'healthy' : 'degraded',
            'version'   => config('app.version', '1.0.0'),
            'timestamp' => now()->toISOString(),
            'checks'    => $checks,
        ], $healthy ? 200 : 503);
    }

    private function runChecks(): array
    {
        return [
            'database'    => $this->checkDatabase(),
            'redis'       => $this->checkRedis(),
            'queue'       => $this->checkQueue(),
            'huggingface' => $this->checkHuggingFace(),
            'storage'     => $this->checkStorage(),
        ];
    }

    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            return ['status' => 'healthy', 'latency_ms' => round((microtime(true) - $start) * 1000, 2)];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    private function checkRedis(): array
    {
        try {
            $start = microtime(true);
            Redis::ping();
            return ['status' => 'healthy', 'latency_ms' => round((microtime(true) - $start) * 1000, 2)];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    private function checkQueue(): array
    {
        try {
            // Check that at least one queue worker is processing
            $size = \Queue::size('default');
            $failed = \DB::table('failed_jobs')->count();
            return [
                'status'       => 'healthy',
                'pending_jobs' => $size,
                'failed_jobs'  => $failed,
            ];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    private function checkHuggingFace(): array
    {
        // Check if circuit breaker is open (don't actually call the API)
        $circuitOpen = Cache::has('cb.huggingface.open');
        return [
            'status'  => $circuitOpen ? 'circuit_open' : 'available',
            'circuit' => $circuitOpen ? 'open' : 'closed',
            'failures' => Cache::get('cb.huggingface.failures', 0),
        ];
    }

    private function checkStorage(): array
    {
        try {
            $start = microtime(true);
            // Write and read a test file
            \Storage::disk('supabase')->put('health-check.txt', now()->toString());
            \Storage::disk('supabase')->delete('health-check.txt');
            return ['status' => 'healthy', 'latency_ms' => round((microtime(true) - $start) * 1000, 2)];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }
}
```

---

## Consul / Kubernetes (When You Outgrow Env Vars)

These are full service discovery tools for large microservice architectures. AEGIS doesn't need these yet, but know they exist.

| Tool             | Use Case                                         | Overhead  |
|------------------|--------------------------------------------------|-----------|
| **Consul**       | Service registry + health checks + KV store      | Medium    |
| **etcd**         | Distributed key-value store (used by Kubernetes) | Medium    |
| **Kubernetes DNS** | Automatic DNS-based discovery in K8s clusters  | High      |
| **AWS Cloud Map**| Service discovery for AWS ECS/EKS                | Medium    |
| **Render**       | Static URLs per service — use env vars           | None      |

---

## Render Multi-Service Discovery (Current Architecture)

On Render, each service gets a stable internal URL. Use these as your "registry entries":

```env
# In .env — Render internal service URLs
AEGIS_API_URL=https://aegis-api.onrender.com
AEGIS_WORKER_URL=https://aegis-worker.onrender.com
REDIS_URL=redis://your-redis.render.com:6379

# Third-party service URLs
HUGGINGFACE_API_URL=https://api-inference.huggingface.co/models/microsoft/resnet-50
SUPABASE_URL=https://yourproject.supabase.co
```

Each service reads its dependencies from env — zero configuration needed at runtime. Render handles the DNS/routing layer.

---

## Practical Checklist for AEGIS
- [ ] All external service URLs live in `.env` / `config/services.php`
- [ ] `/api/health` endpoint returns HTTP 200 (used by Render health check)
- [ ] `/api/health/detailed` endpoint shows per-dependency status (admin only)
- [ ] Circuit breaker state is part of health check response
- [ ] No hardcoded IPs or URLs in application code
- [ ] Each config value has a safe default (`env('KEY', 'default')`)
- [ ] Render health check path configured in service settings → `/api/health`

---

## Anti-Patterns to Avoid
- ❌ Hardcoding service URLs in PHP code (use env/config)
- ❌ Health check endpoint that hits every dependency heavily on each request (use cache TTL)
- ❌ Single health check endpoint that requires auth (LB can't call it)
- ❌ No health check at all — Render can't detect when your app is down
