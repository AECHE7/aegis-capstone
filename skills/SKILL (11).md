# SKILL: Caching

## What It Is
Caching stores the result of an expensive operation (DB query, API call, complex computation) in fast storage (Redis, Memcached, in-memory) so subsequent requests for the same data are served instantly without repeating the work.

## When to Apply This Concept
- A query is run repeatedly with the same result (e.g., list of active scholarships)
- A computation is expensive but the result changes infrequently
- You want to reduce database load and query count
- External API calls (Hugging Face) return the same result for the same input
- Response times on certain routes are unacceptably slow

---

## Caching Layers (Know All Four)

### 1. Application Cache (Laravel Cache Facade)
The most common layer you control directly.
```php
// Cache-Aside pattern (most common)
$scholarships = Cache::remember('scholarships.active', 3600, function () {
    return Scholarship::where('status', 'active')->with('requirements')->get();
});
```

### 2. Database Query Cache
PostgreSQL/Supabase does NOT have a built-in query result cache (unlike MySQL).
Use application-level caching (above) or materialized views for heavy aggregations.

### 3. HTTP Response Cache
Tells browsers and CDNs how long to cache a response.
```php
// In a controller
return response()->json($data)
    ->header('Cache-Control', 'public, max-age=300'); // 5 minutes
```

### 4. CDN / Edge Cache
Cloudflare or similar caches full HTTP responses at edge servers globally. (See CDN skill.)

---

## Cache Strategies

### Cache-Aside (Lazy Loading) ← Use This Most
```
Request → Check Cache → HIT: return cached value
                     → MISS: query DB → store in cache → return value
```
```php
$result = Cache::remember('key', $ttlSeconds, fn() => DB::query());
```
**Pro:** Only caches what's actually requested.
**Con:** First request after cache miss is slow (cold start).

### Write-Through
Write to cache AND DB simultaneously on every write.
```php
public function updateScholarship(Scholarship $s, array $data): Scholarship {
    $s->update($data);
    Cache::put("scholarship.{$s->id}", $s->fresh(), 3600);
    return $s;
}
```
**Pro:** Cache is always fresh.
**Con:** Every write hits cache + DB — adds write latency.

### Write-Behind (Write-Back)
Write to cache immediately, flush to DB asynchronously via a job.
**Best for:** high-throughput counters (page views, likes). Not recommended as a starting pattern for AEGIS.

### Cache-Through (Read-Through)
The cache layer handles DB fetches transparently. Not natively in Laravel — implemented via custom repositories.

---

## Cache Invalidation Strategies

### 1. TTL (Time-to-Live) — Simplest
```php
Cache::put('key', $value, now()->addMinutes(30));
Cache::remember('key', 1800, fn() => /* ... */);
```

### 2. Event-Based Invalidation — Most Accurate
```php
// In a model observer or listener
class ScholarshipObserver
{
    public function updated(Scholarship $scholarship): void
    {
        Cache::forget("scholarship.{$scholarship->id}");
        Cache::forget('scholarships.active');
    }

    public function created(Scholarship $scholarship): void
    {
        Cache::forget('scholarships.active');
        Cache::forget('scholarships.count');
    }
}
```

### 3. Cache Tags (Redis only) — Best for Group Invalidation
```php
// Store with tags
Cache::tags(['scholarships', 'active'])->put('list', $data, 3600);
Cache::tags(['scholarships'])->put("detail.{$id}", $data, 3600);

// Invalidate entire group at once
Cache::tags(['scholarships'])->flush(); // clears ALL scholarship-related cache
```
```php
// Register observer in AppServiceProvider
public function boot(): void
{
    Scholarship::observe(ScholarshipObserver::class);
}
```

---

## Laravel Cache Configuration (Redis)

### `.env`
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379
REDIS_CACHE_DB=1       # Use DB 1 for cache, 0 for sessions
```

### `config/cache.php`
```php
'redis' => [
    'driver' => 'redis',
    'connection' => 'cache',
    'lock_connection' => 'default',
],
```

### `config/database.php` (Redis connections)
```php
'redis' => [
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),        // sessions/queue
    ],
    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'), // cache only
    ],
],
```

---

## Cache Key Naming Conventions
Use a consistent, hierarchical format to avoid key collisions and make debugging easier.
```
{entity}.{scope}.{identifier}
{entity}.{action}.{filters_hash}
```
```php
// Examples
"scholarship.{$id}"
"scholarships.active"
"scholarships.list.page.{$page}"
"user.{$userId}.applications"
"stats.dashboard.total_applicants"

// For dynamic filters, hash the parameters:
$cacheKey = 'scholarships.filtered.' . md5(json_encode($filters));
```

---

## AEGIS-Specific Cache Strategy

| Data                            | Cache Key                       | TTL      | Invalidate On             |
|---------------------------------|---------------------------------|----------|---------------------------|
| Active scholarship list         | `scholarships.active`           | 30 min   | scholarship create/update |
| Scholarship detail              | `scholarship.{id}`              | 1 hour   | scholarship update        |
| Application count per program   | `stats.apps.{scholarshipId}`    | 5 min    | new application submitted |
| Dashboard totals                | `stats.dashboard`               | 10 min   | any data change           |
| AI analysis result (by doc hash)| `ai.result.{sha256_of_doc}`     | 24 hours | never (deterministic)     |
| User application status         | `user.{userId}.app.status`      | 5 min    | application status update |

### Caching AI Inference Results
Since ResNet-50 is deterministic, the same document always produces the same result. Cache it forever (or very long TTL) by document hash:
```php
public function analyzeDocument(string $documentPath): array
{
    $docHash = hash_file('sha256', $documentPath);
    $cacheKey = "ai.result.{$docHash}";

    return Cache::remember($cacheKey, now()->addDays(7), function () use ($documentPath) {
        return $this->huggingFaceService->analyze($documentPath);
    });
}
```

---

## Anti-Patterns to Avoid
- ❌ Caching per-user private data under a shared key
- ❌ Using `Cache::forever()` without a manual invalidation plan
- ❌ Cache keys that include raw user input (XSS/injection in keys)
- ❌ Serializing Eloquent models with lazy-loaded relationships into cache (cache only arrays or DTOs)
- ❌ Forgetting to warm the cache after a full flush (cold start spike)

---

## Quick Reference
```php
Cache::put('key', $value, $seconds);        // store
Cache::get('key', $default);                // retrieve
Cache::remember('key', $ttl, fn() => …);   // get or store
Cache::forget('key');                       // delete one
Cache::tags(['tag'])->flush();              // delete by tag (Redis only)
Cache::has('key');                          // check existence
Cache::increment('counter.key');            // atomic increment
```
