# SKILL: Consistent Hashing

## What It Is
Consistent hashing is an algorithm that distributes keys (requests, data) across nodes (servers, cache instances) in a way that minimizes re-mapping when nodes are added or removed. It's the solution to the problem of "simple modulo hashing breaks everything when you add or remove a server."

## When to Apply This Concept
- You use Redis Cluster (it uses consistent hashing internally)
- You need to distribute cache keys across multiple Redis nodes
- You're building a multi-node CDN or load balancer
- You need to route requests to the same backend node consistently (without sticky sessions)
- You're distributing data across multiple database shards dynamically

---

## The Problem: Simple Modulo Hashing

```
3 servers: server_0, server_1, server_2
Key "user:101" → hash("user:101") % 3 → server 1
Key "doc:500"  → hash("doc:500")  % 3 → server 0
Key "sess:99"  → hash("sess:99")  % 3 → server 2
```

Works fine. Now add a 4th server:
```
4 servers: server_0, server_1, server_2, server_3
Key "user:101" → hash("user:101") % 4 → server 1  ← same (lucky)
Key "doc:500"  → hash("doc:500")  % 4 → server 0  ← same (lucky)
Key "sess:99"  → hash("sess:99")  % 4 → server 3  ← DIFFERENT! Cache miss!
```

With N keys and modulo hashing, adding 1 server remaps approximately (N × k) / (k+1) keys — almost all of them. Every cache miss becomes a DB hit, causing a thundering herd.

---

## The Solution: Hash Ring

```
                    0° / 360°
                    server_A
                   ╱
          server_D           key_1 → server_A
         ╱
    270°                    90°
         ╲                ╱
          server_C ─── server_B
                   ╲
                    key_2 → server_B
                    180°
```

1. Map all server nodes to positions on a circle (0–360° or 0–2³²).
2. To find which server owns a key: hash the key → find position → walk clockwise → first server you hit owns it.
3. When a server is added: only the keys between the new server and its predecessor migrate.
4. When a server is removed: only the keys it owned move to the next server clockwise.

**Result:** Adding or removing a node only remaps 1/N of all keys (where N is the number of nodes).

---

## Implementation

### PHP Consistent Hash Ring
```php
// app/Services/ConsistentHashRing.php
namespace App\Services;

class ConsistentHashRing
{
    private array $ring   = [];  // position → node_name
    private array $nodes  = [];  // node_name → true

    public function __construct(
        private readonly int $virtualNodes = 150 // replicas per physical node
    ) {}

    public function addNode(string $node): void
    {
        $this->nodes[$node] = true;

        for ($i = 0; $i < $this->virtualNodes; $i++) {
            $position          = $this->hash("{$node}:{$i}");
            $this->ring[$position] = $node;
        }

        ksort($this->ring); // keep ring sorted by position
    }

    public function removeNode(string $node): void
    {
        unset($this->nodes[$node]);

        for ($i = 0; $i < $this->virtualNodes; $i++) {
            $position = $this->hash("{$node}:{$i}");
            unset($this->ring[$position]);
        }
    }

    public function getNode(string $key): string
    {
        if (empty($this->ring)) {
            throw new \RuntimeException('No nodes in the ring.');
        }

        $position = $this->hash($key);

        // Walk clockwise: find the first node position >= key position
        foreach ($this->ring as $ringPos => $node) {
            if ($ringPos >= $position) {
                return $node;
            }
        }

        // Wrap around: return the first node on the ring
        return reset($this->ring);
    }

    private function hash(string $key): int
    {
        // CRC32 gives a consistent 32-bit integer
        return crc32($key) & 0x7FFFFFFF; // ensure positive
    }
}
```

### Usage
```php
$ring = new ConsistentHashRing(virtualNodes: 150);

$ring->addNode('redis-node-1');
$ring->addNode('redis-node-2');
$ring->addNode('redis-node-3');

// Route a cache key to its node
$node = $ring->getNode('user:101:profile');  // → "redis-node-2" (always)
$node = $ring->getNode('session:abc123');    // → "redis-node-1" (always)

// Add a new node
$ring->addNode('redis-node-4');

// Now "user:101:profile" might still be on redis-node-2
// Only ~25% of keys move to the new node — not all of them
```

---

## Virtual Nodes Explained

Without virtual nodes, with only 3 physical servers on the ring, the key distribution is uneven (each server owns a huge arc):
```
ring: [server_A at 100, server_B at 250, server_C at 900]
server_A owns: 901→100 (large arc)
server_B owns: 101→250 (small arc)
server_C owns: 251→900 (very large arc)
```

Virtual nodes place each physical server at multiple positions, creating an even distribution:
```
150 virtual nodes per server × 3 servers = 450 points on the ring
→ each server statistically owns ~33% of the ring
```

---

## Redis Cluster (Uses Consistent Hashing Internally)

Redis Cluster is the most common place you'll encounter consistent hashing in practice. It handles everything for you.

```env
# .env — Redis Cluster connection
REDIS_CLUSTER=true
REDIS_HOST=redis-node-1:6379,redis-node-2:6379,redis-node-3:6379
```

```php
// config/database.php
'redis' => [
    'clusters' => [
        'default' => [
            ['host' => env('REDIS_NODE_1_HOST'), 'port' => 6379],
            ['host' => env('REDIS_NODE_2_HOST'), 'port' => 6379],
            ['host' => env('REDIS_NODE_3_HOST'), 'port' => 6379],
        ],
    ],
    'options' => [
        'cluster' => 'redis',  // use Redis native clustering (consistent hashing)
    ],
],
```

### Hash Tags in Redis Cluster
Keys with `{...}` use the contents of braces as the hash key, ensuring related keys land on the same node (needed for multi-key commands and transactions):
```php
// These will all land on the same Redis node because the hash tag is {user:101}
Cache::put('{user:101}.profile', $profile);
Cache::put('{user:101}.sessions', $sessions);
Cache::put('{user:101}.preferences', $prefs);

// Now this multi-key operation works (all keys on same node)
Redis::del('{user:101}.profile', '{user:101}.sessions', '{user:101}.preferences');
```

---

## Consistent Hashing vs. Simple Modulo Comparison

| Metric                     | Simple Modulo (% N)         | Consistent Hashing          |
|----------------------------|-----------------------------|------------------------------|
| Keys remapped on +1 node   | ~(N/(N+1)) ≈ nearly all     | ~(1/N) ≈ only 1 node's share |
| Keys remapped on -1 node   | ~(N/(N-1)) ≈ nearly all     | ~(1/N) ≈ only failed node's  |
| Distribution evenness      | Even (with good hash fn)    | Even (with virtual nodes)    |
| Implementation complexity  | Trivial                     | Moderate                     |
| Where it's used            | Simple caching, no scaling  | Redis Cluster, CDN, LB       |

---

## AEGIS Application

For AEGIS on a single Redis instance, consistent hashing is handled internally by Redis. You only need to think about it when:

1. **Upgrading to Redis Cluster** (when single Redis hits memory limits): configure `'cluster' => 'redis'` in `config/database.php` and use hash tags for related keys.

2. **Building a custom cache shard router**: if you manually run 2+ Redis instances (not cluster), use the `ConsistentHashRing` class to route keys.

3. **Multi-node queue routing**: route jobs to the least-loaded queue worker node.

```php
// Example: Route cache operations across 2 Redis instances with consistent hashing
class MultiRedisCache
{
    private ConsistentHashRing $ring;

    public function __construct()
    {
        $this->ring = new ConsistentHashRing();
        $this->ring->addNode('redis-cache-1');
        $this->ring->addNode('redis-cache-2');
    }

    public function put(string $key, mixed $value, int $ttl): void
    {
        $node = $this->ring->getNode($key);
        Redis::connection($node)->setex($key, $ttl, serialize($value));
    }

    public function get(string $key): mixed
    {
        $node = $this->ring->getNode($key);
        $val  = Redis::connection($node)->get($key);
        return $val ? unserialize($val) : null;
    }
}
```

---

## Anti-Patterns to Avoid
- ❌ Using modulo hashing for distributed caching when you plan to scale node count
- ❌ Too few virtual nodes (< 50) — leads to uneven distribution
- ❌ Mutable node names — if the string representation of a node changes, all its key mappings break
- ❌ Trying to do multi-key Redis commands (MGET, MSET, pipeline) across nodes without hash tags
- ❌ Ignoring that Redis Cluster doesn't support cross-slot operations

---

## Quick Checklist
- [ ] If using Redis Cluster: set `'cluster' => 'redis'` in database config
- [ ] Use `{hash_tag}` in related Redis keys to ensure co-location on the same slot
- [ ] Virtual nodes ≥ 100 for production use (reduces variance in key distribution)
- [ ] Node names are stable strings (not IPs that change on restart)
- [ ] If self-implementing, use CRC32 or MD5 as the hash function (fast, consistent)
