# SKILL: CDN (Content Delivery Network)

## What It Is
A CDN is a geographically distributed network of servers (edge nodes) that caches and serves static assets — JS, CSS, images, fonts, videos — from the location closest to the user. Instead of every user downloading your assets from your origin server in the US (or wherever Render hosts you), a user in the Philippines gets assets from a nearby edge node in Singapore.

## When to Apply This Concept
- Your app serves static assets (CSS, JS bundles, images, fonts)
- Users are geographically distributed or far from your origin server
- You want to reduce bandwidth costs and load on your origin server
- You need DDoS protection and automatic HTTPS at the edge
- You want faster Time-to-First-Byte (TTFB) for static content

---

## What a CDN Caches vs. What It Doesn't

| Cacheable by CDN           | NOT Cacheable (dynamic)       |
|----------------------------|-------------------------------|
| CSS/JS bundles             | API responses with auth       |
| Images, fonts, icons       | User-specific dashboard data  |
| HTML of public pages       | Form submissions (POST)       |
| Supabase Storage files     | Any response with `Set-Cookie`|
| Favicons, robots.txt       | Admin panel pages             |

---

## How CDN Caching Works

```
User (Manila) → CDN Edge (Singapore) → HIT: serve cached asset immediately
                                     → MISS: fetch from origin (Render/Supabase)
                                             → cache at edge → serve to user
```

On the second request from any user near that edge node, the asset is served instantly from Singapore memory — no round-trip to the origin.

---

## Cloudflare (Recommended — Free Tier)

### Setup for Laravel on Render
1. Add your domain to Cloudflare (change nameservers at your registrar).
2. Cloudflare automatically proxies and caches static assets.
3. Your Render service URL becomes the origin; Cloudflare sits in front.

### Cloudflare Auto-Caches These by Default
`.css`, `.js`, `.jpg`, `.png`, `.gif`, `.woff2`, `.svg`, `.ico`, `.webp`

### Cache Rules (Cloudflare Dashboard)
```
Rule: Cache Everything (for /assets/*)
TTL: 1 month (assets are hash-versioned by Vite)

Rule: Bypass Cache (for /api/*, /sanctum/*)
Bypass: always (never cache API responses)
```

---

## Laravel + Vite Asset Versioning
The key problem with CDN caching: if you deploy new CSS/JS, the edge still serves the old cached version. Solution: **content-hash filenames**.

Vite does this automatically:
```
/build/assets/app-4f3a2b1c.js   ← hash changes when file content changes
/build/assets/app-4f3a2b1c.css
```

### `vite.config.js`
```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                // Vite adds content hash by default — no extra config needed
                assetFileNames: 'assets/[name]-[hash][extname]',
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
            },
        },
    },
});
```

### Pointing `asset()` to CDN URL
```env
# .env
ASSET_URL=https://cdn.yourdomain.com
```
```php
// In Blade — automatically prefixes with CDN URL
<link rel="stylesheet" href="{{ asset('build/assets/app-4f3a2b1c.css') }}">
// Renders as: https://cdn.yourdomain.com/build/assets/app-4f3a2b1c.css
```

---

## Cache-Control Headers
These HTTP headers tell CDNs and browsers how long to cache a response.

```php
// In a middleware or controller for public static content
return response($content)
    ->header('Cache-Control', 'public, max-age=31536000, immutable')
    // max-age=31536000 = 1 year
    // immutable = browser won't revalidate even on refresh
    ->header('Vary', 'Accept-Encoding');

// For dynamic but publicly cacheable content (e.g., public scholarship list)
return response()->json($data)
    ->header('Cache-Control', 'public, max-age=300, stale-while-revalidate=60');
    // Serve cached for 5 min, serve stale for 1 min more while revalidating in bg

// For private/auth-protected responses — NEVER cache these at CDN
return response()->json($data)
    ->header('Cache-Control', 'private, no-store');
```

---

## Supabase Storage + CDN
Supabase Storage already has a built-in CDN (via Cloudflare). Public bucket files are served from the edge automatically.

```php
// Storing a file in Supabase Storage public bucket
$path = Storage::disk('supabase')->put('documents', $file);

// Public URL (CDN-served automatically)
$url = Storage::disk('supabase')->url($path);
// Returns: https://xxx.supabase.co/storage/v1/object/public/documents/...
```

For private files (uploaded grant documents in AEGIS), use **signed URLs** — these are not cached by CDN:
```php
$signedUrl = Storage::disk('supabase')->temporaryUrl($path, now()->addMinutes(15));
```

---

## AEGIS-Specific CDN Strategy

| Asset Type               | CDN?     | Cache Duration | Notes                          |
|--------------------------|----------|----------------|--------------------------------|
| Vite JS/CSS bundles      | ✅ Yes   | 1 year         | Hash-versioned filenames       |
| Images/logos/icons       | ✅ Yes   | 1 year         | Served from Supabase Storage   |
| Public scholarship PDFs  | ✅ Yes   | 30 min         | Supabase public bucket         |
| Private grant documents  | ❌ No    | None           | Use signed URLs only           |
| API responses (`/api/*`) | ❌ No    | None           | Bypass CDN; authenticated data |
| Admin dashboard pages    | ❌ No    | None           | Private, auth-required         |

---

## Anti-Patterns to Avoid
- ❌ Caching API responses that contain user-specific data at the CDN level
- ❌ Non-versioned asset filenames — deploying new code won't bust the CDN cache
- ❌ Forgetting `Cache-Control: private` on authenticated responses
- ❌ Putting sensitive documents in a public CDN-cached bucket
- ❌ Running `php artisan storage:link` in production on Render (use Supabase Storage instead)

---

## Cache Invalidation
When you need to purge CDN cache immediately (e.g., a bug in your JS):
- **Cloudflare**: Dashboard → Caching → Purge Cache → Purge Everything
- **Vite**: Rename the file (change any source line) → redeploy → new hash = new URL = no stale cache issue
- **Supabase Storage**: change the filename to get a fresh URL

---

## Quick Checklist
- [ ] Cloudflare (or CDN of choice) sits in front of Render origin
- [ ] Vite build produces hash-versioned asset filenames
- [ ] `ASSET_URL` env var points to CDN domain
- [ ] `Cache-Control: private, no-store` on all `/api/*` authenticated routes
- [ ] Public documents in Supabase Storage public bucket (CDN auto-enabled)
- [ ] Private documents use signed temporary URLs
- [ ] Cache bypass rule in CDN for `/api/*` and `/sanctum/*`
