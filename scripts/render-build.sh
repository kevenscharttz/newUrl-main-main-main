#!/usr/bin/env bash
set -euo pipefail

echo "[render-build] Installing PHP deps..."
composer install --no-dev --prefer-dist --optimize-autoloader

if ! grep -q "^APP_KEY=" .env 2>/dev/null || [ -z "$(grep '^APP_KEY=' .env | cut -d'=' -f2-)" ]; then
  echo "[render-build] Generating APP_KEY..."
  php artisan key:generate --force || true
fi

echo "[render-build] Linking storage..."
php artisan storage:link || true

echo "[render-build] Installing Node deps..."
if command -v npm >/dev/null 2>&1; then
  npm ci || npm install
  echo "[render-build] Building assets..."
  npm run build
else
  echo "[render-build] npm not found. Ensure Render installs Node or switch to Docker deploy." >&2
  exit 1
fi

echo "[render-build] Running migrations..."
php artisan migrate --force || echo "[render-build] Migrations failed (likely DB not ready). Continuing..."

echo "[render-build] Done."
