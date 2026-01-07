#!/usr/bin/env bash
set -euo pipefail

PORT_TO_USE="${PORT:-8080}"

echo "[render] Start: preparing app"

# Safe defaults for stateless envs (can be overridden in dashboard)
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export CACHE_STORE="${CACHE_STORE:-file}"
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"

# Generate key if missing (prefer setting APP_KEY in Render env)
if ! grep -qE "^APP_KEY=.+" < <(printenv) && [ -z "${APP_KEY:-}" ]; then
	php artisan key:generate --force || true
fi

# Migrate if database is reachable; do not crash on failure
php artisan migrate --force || echo "[render] Migrate skipped/failed (DB likely not configured)."

# Optimize caches
php artisan optimize || true

echo "[render] Starting server on 0.0.0.0:${PORT_TO_USE}"
exec php artisan serve --host=0.0.0.0 --port="${PORT_TO_USE}"
