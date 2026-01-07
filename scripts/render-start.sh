#!/usr/bin/env bash
set -euo pipefail

PORT_TO_USE="${PORT:-8080}"

echo "[render] Start: preparing app"

# Safe defaults for stateless envs (can be overridden in dashboard)
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export CACHE_STORE="${CACHE_STORE:-file}"
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"

# Helper: ensure Node available (portable) for last-resort asset build
ensure_node() {
	if command -v npm >/dev/null 2>&1; then
		echo "[render] npm found: $(npm -v)"
		return 0
	fi
	echo "[render] npm not found — fetching portable Node.js (v20.x, .tar.gz)"
	NODE_VERSION="v20.18.0"
	ARCHIVE="node-${NODE_VERSION}-linux-x64"
	TMPDIR="${XDG_CACHE_HOME:-/tmp}/node-portable"
	mkdir -p "$TMPDIR"
	if [ ! -d "$TMPDIR/${ARCHIVE}" ]; then
		curl -fsSL "https://nodejs.org/dist/${NODE_VERSION}/${ARCHIVE}.tar.gz" -o "$TMPDIR/${ARCHIVE}.tar.gz"
		tar -xzf "$TMPDIR/${ARCHIVE}.tar.gz" -C "$TMPDIR"
	fi
	export PATH="$TMPDIR/${ARCHIVE}/bin:$PATH"
	command -v npm >/dev/null 2>&1
}

# Generate key if missing (prefer setting APP_KEY in Render env)
if ! grep -qE "^APP_KEY=.+" < <(printenv) && [ -z "${APP_KEY:-}" ]; then
	php artisan key:generate --force || true
fi

# If assets are missing, attempt a one-time build
if [ ! -f "public/build/manifest.json" ]; then
	echo "[render] No asset manifest found; attempting fallback build"
	if ensure_node; then
		npm ci || npm install
		npm run build || echo "[render] Fallback build failed; proceeding without compiled assets"
	else
		echo "[render] npm unavailable; cannot build assets on start"
	fi
fi

# Migrate if database is reachable; do not crash on failure
php artisan migrate --force || echo "[render] Migrate skipped/failed (DB likely not configured)."

# Optimize caches
php artisan optimize || true

echo "[render] Starting server on 0.0.0.0:${PORT_TO_USE}"
exec php artisan serve --host=0.0.0.0 --port="${PORT_TO_USE}"
