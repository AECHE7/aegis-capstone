# SKILL: Sharding

## What It Is
Sharding is horizontal partitioning of a database — splitting a large dataset across multiple database instances (shards) where each shard holds a subset of the data. Each shard is an independent database with its own storage and compute. Unlike vertical scaling (bigger server), sharding scales horizontally (more servers).

## When to Apply This Concept
- A single database instance cannot handle the data volume or query throughput
- Table sizes exceed tens of millions of rows and performance degrades
- You need to distribute data across geographic regions
- Write throughput has saturated a single DB node
- ⚠️ This is an advanced, last-resort scaling strategy — exhaust all other options first

---

## Before You Consider Sharding — Do These First

Sharding adds enormous complexity. These simpler approaches should be exhausted first:

1. **Query optimization** — add indexes, rewrite slow queries, use EXPLAIN ANALYZE
2. **Caching** — cache frequent reads in Redis (see Caching skill)
3. **Read replicas** — route SELECT queries to replica, writes to primary
4. **Table partitioning** — Postgres-native, no app changes needed (see below)
5. **Connection pooling** — PgBouncer/Supabase pooler
6. **Vertical scaling** — bigger Supabase/Postgres plan

Only if those fail: consider sharding.

---

## Types of Sharding

### 1. Range-Based Sharding
Split by a range of values (e.g., date ranges, ID ranges).
```
Shard 1: application_id 1 → 1,000,000       (2023–2024 data)
Shard 2: application_id 1,000,001 → 2,000,000 (2024–2025 data)
Shard 3: application_id 2,000,001 → ...       (2025–present)
```
**Pro:** Easy range queries, good for time-series data.
**Con:** Hot shards (newest shard gets all new writes).

### 2. Hash-Based Sharding
`shard_id = hash(user_id) % num_shards`
```
User 101 → hash(101) % 4 = 1 → Shard 1
User 205 → hash(205) % 4 = 2 → Shard 2
```
**Pro:** Even data distribution.
**Con:** Adding/removing shards requires re-hashing nearly all data (use Consistent Hashing to solve this — see Consistent Hashing skill).

### 3. Directory-Based Sharding
A lookup table maps each record to its shard.
```
Lookup: { user_id: 101 → shard_id: "shard-ph-north" }
        { user_id: 205 → shard_id: "shard-ph-south" }
```
**Pro:** Flexible, can move data between shards.
**Con:** Lookup table becomes a bottleneck; single point of failure.

### 4. Geographic Sharding
Data is partitioned by region.
```
Shard PH-North: users from Nueva Ecija, Pampanga, Bulacan
Shard PH-South: users from Laguna, Cavite, Batangas
```
**Pro:** Low latency — users hit their local shard.
**Con:** Cross-region queries are expensive.

---

## PostgreSQL Table Partitioning (Before Full Sharding)

Postgres has built-in partitioning — this gives you most sharding benefits with none of the app complexity. Do this first.

### Range Partitioning by Date (e.g., applications table)
```sql
-- Create the parent partitioned table
CREATE TABLE applications (
    id            BIGSERIAL,
    applicant_id  UUID NOT NULL,
    scholarship_id UUID NOT NULL,
    status        VARCHAR(50),
    submitted_at  TIMESTAMP WITH TIME ZONE NOT NULL,
    PRIMARY KEY (id, submitted_at)
) PARTITION BY RANGE (submitted_at);

-- Create partitions per year
CREATE TABLE applications_2024 PARTITION OF applications
    FOR VALUES FROM ('2024-01-01') TO ('2025-01-01');

CREATE TABLE applications_2025 PARTITION OF applications
    FOR VALUES FROM ('2025-01-01') TO ('2026-01-01');

CREATE TABLE applications_2026 PARTITION OF applications
    FOR VALUES FROM ('2026-01-01') TO ('2027-01-01');

-- Indexes on each partition
CREATE INDEX ON applications_2025 (applicant_id);
CREATE INDEX ON applications_2025 (scholarship_id);
CREATE INDEX ON applications_2025 (status, submitted_at);
```

```php
// In Laravel — no changes needed! Partitioning is transparent
$applications = Application::where('status', 'approved')
    ->whereBetween('submitted_at', [
        now()->startOfYear(),
        now()->endOfYear(),
    ])
    ->get();
// Postgres automatically queries only the 2026 partition
```

### Hash Partitioning (by user_id for even distribution)
```sql
CREATE TABLE documents (
    id      BIGSERIAL,
    user_id UUID NOT NULL,
    PRIMARY KEY (id, user_id)
) PARTITION BY HASH (user_id);

CREATE TABLE documents_p0 PARTITION OF documents
    FOR VALUES WITH (modulus 4, remainder 0);
CREATE TABLE documents_p1 PARTITION OF documents
    FOR VALUES WITH (modulus 4, remainder 1);
CREATE TABLE documents_p2 PARTITION OF documents
    FOR VALUES WITH (modulus 4, remainder 2);
CREATE TABLE documents_p3 PARTITION OF documents
    FOR VALUES WITH (modulus 4, remainder 3);
```

---

## Application-Level Sharding in Laravel (Multi-DB)

If you truly need multiple database instances, configure multiple connections:

```php
// config/database.php
'connections' => [
    'pgsql_shard_1' => [
        'driver'   => 'pgsql',
        'host'     => env('DB_SHARD1_HOST'),
        'database' => env('DB_SHARD1_DATABASE'),
        'username' => env('DB_SHARD1_USERNAME'),
        'password' => env('DB_SHARD1_PASSWORD'),
    ],
    'pgsql_shard_2' => [
        'driver'   => 'pgsql',
        'host'     => env('DB_SHARD2_HOST'),
        'database' => env('DB_SHARD2_DATABASE'),
        'username' => env('DB_SHARD2_USERNAME'),
        'password' => env('DB_SHARD2_PASSWORD'),
    ],
],
```

### Shard Router
```php
// app/Services/ShardRouter.php
namespace App\Services;

class ShardRouter
{
    private const SHARDS = ['pgsql_shard_1', 'pgsql_shard_2'];

    public static function connectionFor(string $shardKey): string
    {
        $shardIndex = crc32($shardKey) % count(self::SHARDS);
        return self::SHARDS[abs($shardIndex)];
    }
}
```

```php
// In a model — resolve connection dynamically
class Application extends Model
{
    public function resolveConnection(): string
    {
        return ShardRouter::connectionFor((string) $this->applicant_id);
    }

    public function getConnectionName(): string
    {
        return $this->resolveConnection();
    }
}
```

---

## Cross-Shard Challenges

These operations become very hard with sharding — plan ahead:

| Operation               | Problem                                     | Solution                           |
|-------------------------|---------------------------------------------|------------------------------------|
| JOIN across shards      | Can't SQL JOIN tables on different servers  | Application-level join (slow)      |
| COUNT(*) across shards  | Must query all shards and sum              | Aggregate cache                    |
| Global unique IDs       | Auto-increment clashes across shards       | UUIDs or Snowflake IDs             |
| Distributed transactions| ACID across shards is very hard            | Saga pattern or avoid cross-shard  |
| Cross-shard search      | Must fan out to all shards                 | Dedicated search index (Meilisearch)|

---

## AEGIS Scaling Roadmap (Data Volume)

| Phase                    | Action                                      | Trigger                        |
|--------------------------|---------------------------------------------|--------------------------------|
| **Current**              | Single Supabase instance                    | < 100K records                 |
| **Phase 2**              | Add DB indexes + query optimization         | Queries > 500ms                |
| **Phase 3**              | Redis caching for hot reads                 | DB CPU > 60%                   |
| **Phase 4**              | Read replica (Supabase replication)         | Read/write ratio > 80/20       |
| **Phase 5**              | Postgres table partitioning                 | Tables > 10M rows              |
| **Phase 6 (far future)** | Full sharding                               | Single Postgres can't cope     |

AEGIS is firmly in Phase 1–3. Sharding is years away if ever needed.

---

## Anti-Patterns to Avoid
- ❌ Sharding prematurely — it's extremely hard to add after the fact, but so is premature sharding
- ❌ Picking a shard key that creates hot spots (e.g., timestamp: all new writes go to the newest shard)
- ❌ Using mutable shard keys (if user moves region, all their data needs to move)
- ❌ Doing cross-shard JOINs in SQL — they won't work; aggregate at application level
- ❌ Forgetting that auto-increment IDs collide across shards — use UUIDs from the start

---

## Quick Checklist (Pre-Sharding)
- [ ] All queries have appropriate indexes
- [ ] EXPLAIN ANALYZE run on slow queries
- [ ] Redis caching applied to hot reads
- [ ] Supabase connection pooler (PgBouncer) enabled
- [ ] Postgres table partitioning applied to large tables (if needed)
- [ ] Read replica configured for read-heavy workloads (if needed)
- [ ] UUIDs used as primary keys (not serial integers) — future-proof for sharding
