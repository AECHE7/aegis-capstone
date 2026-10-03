#!/bin/sh
set -e

echo "▶ Preparing A.E.G.I.S. Production Environment..."

# Adjust Nginx port if $PORT is assigned dynamically by cloud host (Render, Fly, Cloud Run)
TARGET_PORT="${PORT:-10000}"
if [ "$TARGET_PORT" != "10000" ]; then
    echo "▶ Rebinding Nginx to port $TARGET_PORT..."
    sed -i "s/10000/$TARGET_PORT/g" /etc/nginx/http.d/default.conf
fi

echo "▶ Ensuring storage symlink..."
php artisan storage:link || true

echo "▶ Warming Laravel production caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "▶ Running database migrations..."
php artisan migrate --force

echo "▶ Launching Supervisord (PHP-FPM, Nginx, Queue Workers)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
