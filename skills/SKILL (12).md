# SKILL: Load Balancing

## What It Is
Load balancing distributes incoming network traffic across multiple server instances so no single instance becomes a bottleneck or single point of failure. It sits in front of your application servers and acts as a traffic cop.

## When to Apply This Concept
- When a single server cannot handle peak request volume
- When you need zero-downtime deployments (route traffic away from the instance being updated)
- When you want fault tolerance (if one instance dies, traffic automatically reroutes)
- When horizontal scaling is more practical than buying a bigger server

---

## Core Algorithms

### Round Robin (Default)
Sends each new request to the next server in a rotating list.
```
Request 1 → Server A
Request 2 → Server B
Request 3 → Server C
Request 4 → Server A  (cycle repeats)
```
**Best for:** homogeneous servers with similar response times.

### Least Connections
Routes each request to the server with the fewest active connections at that moment.
**Best for:** workloads with variable request durations (e.g., AI inference calls that take unpredictable time).

### IP Hash (Sticky Sessions)
`hash(client_ip) % num_servers` always sends a client to the same server.
**Best for:** apps that store session state locally on the server. ⚠️ Avoid this — centralize sessions in Redis instead.

### Weighted Round Robin
Servers get a "weight" proportional to their capacity. A server with weight=3 gets 3x the traffic of a weight=1 server.
**Best for:** mixed hardware (e.g., one powerful node + two cheaper nodes).

---

## Health Checks
The load balancer periodically calls a `/health` or `/ping` endpoint on each server. If the server fails N consecutive checks, it is removed from the pool until it recovers.

### Laravel Health Check Endpoint
```php
// routes/api.php
Route::get('/health', function () {
    return response()->json([
        'status'   => 'ok',
        'db'       => DB::connection()->getPdo() ? 'connected' : 'error',
        'cache'    => Cache::store('redis')->ping() ? 'connected' : 'error',
        'timestamp'=> now()->toISOString(),
    ]);
});
```

---

## Nginx Upstream Configuration (Self-Hosted)
```nginx
upstream aegis_app {
    least_conn;                        # algorithm
    server 10.0.0.1:8000 weight=3;
    server 10.0.0.2:8000 weight=1;
    server 10.0.0.3:8000 backup;      # only used when others fail
    keepalive 32;
}

server {
    listen 80;
    location / {
        proxy_pass http://aegis_app;
        proxy_set_header Host              $host;
        proxy_set_header X-Real-IP         $remote_addr;
        proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

---

## Critical: Stateless Application Requirements
For load balancing to work correctly, your app MUST be stateless — each instance must be interchangeable. This means:

| State Type     | DON'T store on disk/local | DO use instead         |
|----------------|---------------------------|------------------------|
| Sessions       | Local file sessions       | Redis (`SESSION_DRIVER=redis`) |
| Cache          | Local array/file cache    | Redis (`CACHE_DRIVER=redis`)   |
| File uploads   | Local `/storage`          | Supabase Storage / S3  |
| Job queues     | Database (if shared)      | Redis Queue            |

### Laravel `.env` for stateless operation
```env
SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
FILESYSTEM_DISK=supabase   # custom driver pointing to Supabase Storage
```

---

## Render Platform Notes
- **Free plan**: single instance only, no LB needed yet.
- **Starter/Standard plan**: enable "Scaling" in service settings → set Min/Max instances.
- Render handles the load balancer layer automatically — you do NOT configure Nginx manually.
- Your app just needs to be stateless (sessions/cache in Redis).
- Render uses round-robin by default across your instances.

---

## AEGIS-Specific Guidance
For A.E.G.I.S., the most important preparatory work is making the app stateless now:

```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'redis'),
'connection' => 'default',

// config/cache.php
'default' => env('CACHE_DRIVER', 'redis'),
```

The AI document analysis (Hugging Face calls) are the heaviest operations. When you scale:
1. Keep the web server instances stateless.
2. Scale queue workers **independently** — they are separate Render services.
3. The Supabase connection pool (PgBouncer) must be enabled to handle connections from multiple instances.

---

## Anti-Patterns to Avoid
- ❌ Storing sessions in local files when running multiple instances
- ❌ Writing uploaded files to local disk (`storage/app/public`) — they won't be visible across instances
- ❌ Using sticky sessions to work around stateful code — fix the statefulness instead
- ❌ Setting health check endpoint to a route that hits the DB heavily on every check

---

## Decision Checklist
- [ ] Sessions stored in Redis, not files
- [ ] Cache stored in Redis, not file/array
- [ ] Uploaded files sent to Supabase Storage
- [ ] `/api/health` endpoint returns HTTP 200 with DB + Redis status
- [ ] Queue workers are separate from web servers
- [ ] Supabase PgBouncer (connection pooling) is ON
