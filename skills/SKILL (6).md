# SKILL: Circuit Breaker

## What It Is
A circuit breaker prevents cascading failures by detecting when a dependency (external API, microservice, database) is repeatedly failing and "opening the circuit" — stopping all calls to that dependency for a cooldown period. Instead of every request failing slowly after a timeout, the circuit breaker fails fast with a fallback, protecting your app and giving the dependency time to recover.

Named after electrical circuit breakers: when a fault is detected, the breaker trips and stops current flow to prevent damage.

## When to Apply This Concept
- You call an external API (Hugging Face, email provider, SMS service) that can be slow or down
- A slow external service causes your own response times to degrade
- You want graceful degradation instead of full system failure
- You need to prevent a cascade where one failing service brings down your whole app

---

## The Three States

```
         ┌──────────────────────────────────────────────┐
         │                                              │
         ▼                                              │
    ┌─────────┐   N failures       ┌──────────┐        │
    │  CLOSED  │ ────────────────► │   OPEN   │        │
    │ (normal) │                   │ (tripped)│        │
    └─────────┘                    └──────────┘        │
         ▲                              │               │
         │                    Cooldown  │               │
         │                    expires   ▼               │
         │               ┌────────────────┐             │
         │   SUCCESS     │   HALF-OPEN    │   FAILURE   │
         └───────────────│ (test request) │─────────────┘
                         └────────────────┘
```

- **CLOSED**: Normal operation. Requests flow through. Failures are counted.
- **OPEN**: Too many failures. All requests fail immediately (no actual call made). Returns a fallback.
- **HALF-OPEN**: Cooldown expired. One test request is allowed through. If it succeeds → CLOSED. If it fails → OPEN again.

---

## Manual Implementation (Redis-Backed)

```php
// app/Services/CircuitBreaker.php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CircuitBreaker
{
    private const STATE_CLOSED    = 'closed';
    private const STATE_OPEN      = 'open';
    private const STATE_HALF_OPEN = 'half_open';

    public function __construct(
        private readonly string $name,
        private readonly int    $failureThreshold = 5,    // open after 5 failures
        private readonly int    $cooldownSeconds  = 60,   // stay open for 60s
        private readonly int    $windowSeconds    = 120,  // count failures in 2-min window
    ) {}

    public function isOpen(): bool
    {
        return $this->getState() === self::STATE_OPEN;
    }

    public function isClosed(): bool
    {
        return $this->getState() === self::STATE_CLOSED;
    }

    public function isHalfOpen(): bool
    {
        return $this->getState() === self::STATE_HALF_OPEN;
    }

    public function recordSuccess(): void
    {
        Cache::forget($this->failureKey());
        Cache::forget($this->stateKey());
        // Circuit returns to CLOSED
    }

    public function recordFailure(): void
    {
        $failures = Cache::increment($this->failureKey());

        if ($failures === 1) {
            // Set expiry on the first failure
            Cache::put($this->failureKey(), 1, $this->windowSeconds);
        }

        if ($failures >= $this->failureThreshold) {
            // Trip the breaker
            Cache::put($this->stateKey(), self::STATE_OPEN, $this->cooldownSeconds);
            Cache::forget($this->failureKey()); // reset counter
        }
    }

    private function getState(): string
    {
        $state = Cache::get($this->stateKey());

        if ($state === self::STATE_OPEN) {
            // Check if cooldown has expired (TTL gone → key missing → half-open)
            return self::STATE_OPEN;
        }

        if ($state === null && Cache::get($this->stateKey() . '_was_open')) {
            return self::STATE_HALF_OPEN;
        }

        return self::STATE_CLOSED;
    }

    private function stateKey(): string    { return "circuit_breaker.{$this->name}.state"; }
    private function failureKey(): string  { return "circuit_breaker.{$this->name}.failures"; }
}
```

### Cleaner Implementation Using a call() Wrapper
```php
// app/Services/CircuitBreaker.php (simplified wrapper pattern)
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CircuitBreaker
{
    public function __construct(
        private readonly string $service,
        private readonly int $threshold = 5,
        private readonly int $cooldown  = 60,
    ) {}

    /**
     * Execute callable with circuit breaker protection.
     * Returns null if circuit is open (caller decides fallback).
     */
    public function call(callable $fn, mixed $fallback = null): mixed
    {
        if ($this->isTripped()) {
            \Log::warning("Circuit breaker OPEN for [{$this->service}] — returning fallback");
            return is_callable($fallback) ? $fallback() : $fallback;
        }

        try {
            $result = $fn();
            $this->onSuccess();
            return $result;
        } catch (\Exception $e) {
            $this->onFailure($e);
            return is_callable($fallback) ? $fallback() : $fallback;
        }
    }

    private function isTripped(): bool
    {
        return Cache::has("cb.{$this->service}.open");
    }

    private function onSuccess(): void
    {
        Cache::forget("cb.{$this->service}.failures");
        Cache::forget("cb.{$this->service}.open");
    }

    private function onFailure(\Exception $e): void
    {
        \Log::error("Circuit breaker failure [{$this->service}]: {$e->getMessage()}");

        $failures = Cache::increment("cb.{$this->service}.failures");

        if ($failures === 1) {
            Cache::put("cb.{$this->service}.failures", 1, 120); // 2-min failure window
        }

        if ($failures >= $this->threshold) {
            Cache::put("cb.{$this->service}.open", true, $this->cooldown);
            Cache::forget("cb.{$this->service}.failures");
        }
    }
}
```

---

## Using the Circuit Breaker in AEGIS

### Protecting Hugging Face API Calls
```php
// app/Services/HuggingFaceService.php
namespace App\Services;

class HuggingFaceService
{
    private CircuitBreaker $breaker;

    public function __construct()
    {
        $this->breaker = new CircuitBreaker(
            service:   'huggingface',
            threshold: 3,     // open after 3 failures
            cooldown:  120,   // try again after 2 minutes
        );
    }

    public function analyzeDocument(string $filePath): ?array
    {
        return $this->breaker->call(
            fn: function () use ($filePath) {
                $response = Http::timeout(30)
                    ->withToken(config('services.huggingface.token'))
                    ->post(config('services.huggingface.url'), [
                        'file' => base64_encode(file_get_contents($filePath)),
                    ]);

                if ($response->failed()) {
                    throw new \RuntimeException("HuggingFace API error: {$response->status()}");
                }

                return $response->json();
            },
            fallback: function () {
                // Fallback: queue for manual review
                return [
                    'fraud_score' => null,
                    'status'      => 'manual_review_required',
                    'reason'      => 'AI service temporarily unavailable',
                ];
            }
        );
    }
}
```

### In the Queue Job
```php
// app/Jobs/AnalyzeDocumentJob.php
public function handle(HuggingFaceService $service): void
{
    $result = $service->analyzeDocument($this->document->file_path);

    if ($result === null || $result['status'] === 'manual_review_required') {
        // Circuit is open — flag for manual review, don't fail the job
        $this->document->update([
            'status'    => 'pending_manual_review',
            'notes'     => 'AI service unavailable. Queued for manual review.',
        ]);

        // Notify admin
        event(new ManualReviewRequired($this->document));
        return; // Job completes successfully — no retry
    }

    $this->document->update([
        'status'      => 'completed',
        'fraud_score' => $result['fraud_score'],
    ]);
}
```

---

## Multiple Services — One Breaker Each
```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->singleton('cb.huggingface', fn() =>
        new CircuitBreaker('huggingface', threshold: 3, cooldown: 120)
    );

    $this->app->singleton('cb.email', fn() =>
        new CircuitBreaker('email', threshold: 5, cooldown: 300)
    );

    $this->app->singleton('cb.supabase-storage', fn() =>
        new CircuitBreaker('supabase-storage', threshold: 5, cooldown: 60)
    );
}
```

---

## Using Spatie's Package (Recommended for Production)

```bash
composer require spatie/laravel-circuit-breaker
php artisan vendor:publish --provider="Spatie\CircuitBreaker\CircuitBreakerServiceProvider"
```

```php
use Spatie\CircuitBreaker\CircuitBreaker;

$result = CircuitBreaker::for('huggingface')
    ->call(function () {
        return $this->huggingFaceApiCall();
    })
    ->fallbackValue(['status' => 'manual_review_required'])
    ->get();
```

---

## Monitoring Circuit State

Create an admin endpoint to see circuit states:
```php
// routes/api.php
Route::middleware(['auth:sanctum', 'role:admin'])
    ->get('/admin/system/circuit-breakers', function () {
        $services = ['huggingface', 'email', 'supabase-storage'];

        return response()->json(
            collect($services)->mapWithKeys(fn($s) => [
                $s => [
                    'state'    => Cache::has("cb.{$s}.open") ? 'OPEN' : 'CLOSED',
                    'failures' => Cache::get("cb.{$s}.failures", 0),
                ]
            ])
        );
    });
```

---

## AEGIS Circuit Breaker Configuration

| Service              | Threshold | Cooldown | Fallback Behavior                     |
|----------------------|-----------|----------|---------------------------------------|
| Hugging Face API     | 3 fails   | 2 min    | Flag document for manual review       |
| Email service (SMTP) | 5 fails   | 5 min    | Queue email for retry when recovered  |
| Supabase Storage     | 5 fails   | 1 min    | Return error to user, don't process   |

---

## Anti-Patterns to Avoid
- ❌ One circuit breaker shared across multiple services — each service needs its own
- ❌ Setting threshold=1 — a single transient failure opens the breaker
- ❌ No fallback — open circuit should gracefully degrade, not throw unhandled exceptions
- ❌ Storing circuit state in a file (use Redis — it's shared across instances)
- ❌ Using circuit breaker for DB calls to your own Supabase — use connection pooling instead

---

## Quick Checklist
- [ ] Circuit breaker wrapping all Hugging Face API calls
- [ ] Circuit breaker wrapping email/SMS dispatch
- [ ] State stored in Redis (shared across app instances)
- [ ] Fallback behavior defined (not just `null`)
- [ ] Admin endpoint to monitor circuit states
- [ ] Failure logged with context (service name, error message)
