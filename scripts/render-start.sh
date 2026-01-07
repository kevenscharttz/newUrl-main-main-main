#!/usr/bin/env bash
set -euo pipefail

echo "[render-start] Clearing caches..."
php artisan optimize:clear || true

echo "[render-start] Resetting permission cache..."
php artisan permission:cache-reset || true

echo "[render-start] Caching config/routes/views..."
php artisan config:cache || true
php artisan route:cache  || true
php artisan view:cache   || true

PORT_TO_USE=${PORT:-8080}
echo "[render-start] Starting server on :${PORT_TO_USE}"
exec php artisan serve --host=0.0.0.0 --port=${PORT_TO_USE}
