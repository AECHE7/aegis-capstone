#!/usr/bin/env bash
# ── A.E.G.I.S. Render Start Script ───────────────────────────────────────────
# Optimized for zero-latency Render free-tier cold starts.
# Binds HTTP server on port 10000 immediately to eliminate 502 Bad Gateway errors.
set -e

echo "▶ Warming application configuration & caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "▶ Spawning database migrations & queue worker asynchronously..."
(
    php artisan migrate --force || true
    php artisan queue:work --verbose --tries=3 --timeout=120
) &

echo "▶ Starting HTTP server on port ${PORT:-10000} immediately..."
export PHP_CLI_SERVER_WORKERS=10
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000} --no-reload
