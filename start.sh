#!/usr/bin/env bash
# ── A.E.G.I.S. Render Start Script ───────────────────────────────────────────
# Runs migrations and seeding, then starts the HTTP server immediately.
# The queue worker runs in the background so it doesn't block the health check.
set -e

echo "▶ Running database migrations..."
php artisan migrate --force

echo "▶ Running database seeder (idempotent)..."
php artisan db:seed --force

echo "▶ Clearing application caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "▶ Starting queue worker in background..."
php artisan queue:work --verbose --tries=3 --timeout=120 &

echo "▶ Starting HTTP server on port ${PORT:-10000} with concurrent workers..."
export PHP_CLI_SERVER_WORKERS=10
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}

