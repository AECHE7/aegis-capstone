#!/usr/bin/env bash
# ── A.E.G.I.S. Render Start Script ───────────────────────────────────────────
# Optimized for zero-latency Render free-tier cold starts.
# Binds HTTP server on port 10000 immediately to eliminate 502 Bad Gateway errors.
set -e

echo "▶ Warming application configuration & caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "▶ Ensuring storage symlink..."
php artisan storage:link || true

echo "▶ Running database migrations..."
php artisan migrate --force

echo "▶ Spawning queue worker in background..."
(
    while true; do
        php artisan queue:work --verbose --tries=3 --timeout=120 --sleep=3 --max-time=3600
        echo "Queue worker stopped, restarting in 3s..."
        sleep 3
    done
) &

echo "▶ Starting HTTP server on port ${PORT:-10000}..."
export PHP_CLI_SERVER_WORKERS=10
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000} --no-reload
