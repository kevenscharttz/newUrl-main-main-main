#!/usr/bin/env bash
set -euo pipefail

echo "[render] Build start"

# Ensure Composer is available (Render native PHP has composer)
if ! command -v composer >/dev/null 2>&1; then
	echo "[render] ERROR: composer not found in PATH" >&2
	exit 1
fi

# 1) PHP deps without dev, optimized
composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader

# 2) Node/Vite build
if command -v npm >/dev/null 2>&1; then
	echo "[render] Installing npm dependencies"
	npm ci || npm install
	echo "[render] Building assets with Vite"
	npm run build
else
	echo "[render] WARNING: npm not found; skipping asset build"
fi

# 3) Optimize and link storage (idempotent)
php artisan storage:link || true
php artisan optimize || true

echo "[render] Build done"
