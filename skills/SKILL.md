# SKILL: Auto Scaling

## What It Is
Auto scaling automatically adjusts the number of running application instances up or down based on real-time metrics (CPU, memory, request count, queue depth). When load increases, it spins up more instances. When load drops, it terminates unneeded instances to save costs. The goal is optimal performance at minimal cost — you never over-provision or under-provision.

## When to Apply This Concept
- Traffic is unpredictable or varies significantly by time of day/week
- You want to handle traffic spikes without manual intervention
- You're paying for compute and want to optimize costs
- You run queue workers that need more capacity during batch processing
- Your application is already stateless (prerequisite — see Load Balancing skill)

---

## Two Types of Scaling

### Horizontal Scaling (Scale Out/In) ← Preferred
Add or remove instances of the same size.
```
Low traffic:    [Instance 1]
Medium traffic: [Instance 1] [Instance 2]
Peak traffic:   [Instance 1] [Instance 2] [Instance 3] [Instance 4]
```
**Pro:** Linear cost scaling, no downtime, works with stateless apps.
**Con:** Requires stateless architecture (sessions/files externalized).

### Vertical Scaling (Scale Up/Down)
Make the existing instance bigger (more CPU/RAM) or smaller.
```
Low:  1 CPU  / 512MB RAM
High: 4 CPUs / 4GB RAM
```
**Pro:** Simpler — no code changes needed.
**Con:** Has a ceiling, causes brief downtime on resize, more expensive per unit.

---

## Auto Scaling on Render

Render supports horizontal auto scaling on Standard and above plans.

### Configuring Auto Scaling (Render Dashboard)
```
Service Settings → Scaling
├── Min Instances: 1      ← always keep at least 1 running
├── Max Instances: 5      ← never exceed this (cost ceiling)
├── Target CPU: 70%       ← scale up when CPU > 70%
└── Target Memory: 80%    ← scale up when memory > 80%
```

### render.yaml (Infrastructure as Code)
```yaml
services:
  # Web API — scales based on HTTP load
  - type: web
    name: aegis-api
    env: php
    plan: standard
    scaling:
      minInstances: 1
      maxInstances: 5
      targetMemoryPercent: 80
      targetCPUPercent: 70
    envVars:
      - key: SESSION_DRIVER
        value: redis
      - key: CACHE_DRIVER
        value: redis
    buildCommand: composer install --optimize-autoloader && php artisan optimize
    startCommand: php artisan serve --host=0.0.0.0 --port=10000

  # Queue Worker — scales based on queue depth
  - type: worker
    name: aegis-queue-worker
    env: php
    plan: standard
    scaling:
      minInstances: 1
      maxInstances: 3
    startCommand: php artisan queue:work redis --queue=ai-analysis,notifications,default --tries=3 --timeout=120 --max-time=3600

  # Scheduler — single instance only, never scale this
  - type: cron
    name: aegis-scheduler
    env: php
    schedule: "* * * * *"
    buildCommand: composer install
    startCommand: php artisan schedule:run
```

---

## Prerequisites for Auto Scaling (Stateless Checklist)

Auto scaling ONLY works correctly if your app is fully stateless. Each instance must be interchangeable.

```
✅ Sessions         → Redis (SESSION_DRIVER=redis)
✅ Cache            → Redis (CACHE_DRIVER=redis)
✅ File Uploads     → Supabase Storage (not local disk)
✅ Queue Jobs       → Redis Queue (QUEUE_CONNECTION=redis)
✅ WebSocket state  → Pusher/Supabase Realtime (external)
✅ Logs             → External log aggregator (Papertrail, Logtail)
✅ Config           → Environment variables (no local .env mutation)
```

---

## Scaling Triggers and Metrics

### CPU-Based Scaling (Default)
Best for compute-heavy workloads.
```
Scale Out: when average CPU > 70% for 2 consecutive minutes
Scale In:  when average CPU < 30% for 10 consecutive minutes
```
The asymmetry (fast scale-out, slow scale-in) prevents thrashing.

### Request Count Scaling
Best for HTTP APIs.
```
Scale Out: when requests/sec > 100 per instance
Scale In:  when requests/sec < 20 per instance
```

### Queue Depth Scaling (Most Relevant for AEGIS)
Scales queue workers based on how many jobs are waiting.
```
Scale Out: when queue depth > 50 pending jobs
Scale In:  when queue depth < 5 pending jobs
```

Render doesn't natively support queue-depth scaling (you'd need custom metrics). Use CPU as the proxy trigger for workers.

### Custom Metrics with Render (Advanced)
```php
// Push custom metrics to Render's metrics endpoint or external tools
// Example: push queue depth to Datadog/Grafana, trigger scale from there
$queueDepth = Queue::size('ai-analysis');
\Log::channel('metrics')->info('queue.depth', ['value' => $queueDepth]);
```

---

## Database Connection Pooling — Critical for Auto Scaling

Each new app instance opens DB connections. With 5 instances × 10 connections each = 50 connections to Supabase. Supabase (on free plan) limits to 60. Without pooling, you'll hit the limit fast.

### Enable Supabase PgBouncer (Connection Pooler)
In Supabase Dashboard → Settings → Database → Connection Pooling → Enable

```env
# Use the pooler URL instead of direct connection
DB_HOST=db.yourproject.supabase.co           # direct (limited connections)
# ↓ Use this instead:
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com  # pooler (unlimited effective connections)
DB_PORT=6543                                  # pooler port (not 5432)
DB_POOL_MODE=transaction                      # transaction pooling (most efficient)
```

```php
// config/database.php — also set pool size per instance
'pgsql' => [
    'host'     => env('DB_HOST'),
    'port'     => env('DB_PORT', '6543'),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'sslmode'  => 'require',
    'options'  => [
        PDO::ATTR_EMULATE_PREPARES   => true,  // required for transaction pooling
        PDO::ATTR_PERSISTENT         => false, // don't persist connections with pooler
    ],
],
```

---

## Graceful Shutdown

When auto scaling terminates an instance, it should finish in-flight requests before dying.

```php
// app/Console/Commands/QueueWorkerGraceful.php
// Actually, Laravel handles this: SIGTERM → finish current job → exit
// Use --stop-when-empty or handle SIGTERM in custom worker

// Ensure Render sends SIGTERM (not SIGKILL) and allows time to drain
// In render.yaml:
// stopCommand: sleep 120  ← give 2 min to finish jobs before forced kill
```

```bash
# Worker command with graceful shutdown flags
php artisan queue:work redis \
  --max-time=3600 \       # restart worker every hour (prevents memory leaks)
  --max-jobs=1000 \       # restart after 1000 jobs (prevents memory leaks)
  --timeout=120 \         # kill a single job if it runs > 2 min
  --tries=3               # retry failed jobs 3 times
```

---

## Laravel Octane (Optional: High-Performance Server)

Laravel Octane boots the app once and keeps it in memory across requests, dramatically improving throughput per instance before you need to scale.

```bash
composer require laravel/octane
php artisan octane:install  # choose: roadrunner (recommended for Render) or swoole
```

```yaml
# render.yaml with Octane
startCommand: php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=10000 --workers=4
```

With Octane: 1 instance can handle 4–8× more requests before needing to scale out.

---

## Monitoring and Alerting

Set up monitoring so you know when scaling is happening and why.

```php
// app/Console/Kernel.php — report queue health every 5 minutes
$schedule->call(function () {
    $metrics = [
        'queue.ai_analysis.depth'  => Queue::size('ai-analysis'),
        'queue.notifications.depth'=> Queue::size('notifications'),
        'queue.failed'             => DB::table('failed_jobs')->count(),
        'cache.memory.used'        => // Redis INFO memory
    ];

    foreach ($metrics as $key => $value) {
        \Log::channel('metrics')->info($key, ['value' => $value]);
    }
})->everyFiveMinutes();
```

---

## AEGIS Scaling Architecture

```
                        [Cloudflare CDN]
                               ↓
                    [Render Load Balancer]
                    ╱         ↓          ╲
          [aegis-api-1]  [aegis-api-2]  [aegis-api-3]
          (auto-scaled: 1–5 instances based on CPU)
                    ╲         ↓          ╱
                        [Redis Cluster]
                        (sessions, cache, queues)
                               ↓
                    [aegis-worker-1] [aegis-worker-2]
                    (auto-scaled: 1–3 instances based on CPU)
                               ↓
                    [Hugging Face Inference API]
                               ↓
                    [Supabase PostgreSQL + PgBouncer]
```

---

## Cost Optimization Strategy

| Time                    | Expected Traffic | Instances | Monthly Cost Estimate |
|-------------------------|------------------|-----------|----------------------|
| Peak (8 AM–5 PM weekdays)| High           | 3–5       | Higher               |
| Off-peak (nights/weekends)| Low           | 1         | Lower                |
| Batch processing (month-end)| Spike      | 5         | Short-lived spike    |

Set Min=1 (always available), Max=5 (hard cost ceiling). Render auto-manages the rest.

---

## Anti-Patterns to Avoid
- ❌ Scaling with local file sessions — users get logged out when hitting different instances
- ❌ Setting Min=0 (scale to zero for web apps) — cold start adds 30–60s delay on first request
- ❌ Not enabling PgBouncer — DB connection exhaustion takes down the app faster than you scale
- ❌ Scaling the cron scheduler — only 1 instance should run scheduled tasks; use `withoutOverlapping()`
- ❌ No health check endpoint — the load balancer can't detect unhealthy instances
- ❌ Not setting `--max-time` on queue workers — memory leaks accumulate over time and crash workers

---

## Auto Scaling Checklist
- [ ] App is fully stateless (sessions, cache, files externalized)
- [ ] Supabase PgBouncer (connection pooling) enabled
- [ ] `/api/health` endpoint returns HTTP 200 quickly
- [ ] Render Scaling configured: Min=1, Max=5, CPU target=70%
- [ ] Queue workers deployed as separate Render worker service
- [ ] Cron scheduler is a single Render cron job (not a worker)
- [ ] `--max-time` and `--max-jobs` set on all queue workers
- [ ] `withoutOverlapping()` on all scheduled tasks
- [ ] Redis session/cache configured for all instances to share state
