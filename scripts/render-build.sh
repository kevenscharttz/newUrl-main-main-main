#!/usr/bin/env bash
set -euo pipefail

echo "[render] Build start"

# Ensure Composer is available (Render native PHP has composer)
if ! command -v composer >/dev/null 2>&1; then
	echo "[render] ERROR: composer not found in PATH" >&2
	exit 1
fi

# 0) Ensure we have a working Node.js toolchain even on Render native PHP
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
		URL="https://nodejs.org/dist/${NODE_VERSION}/${ARCHIVE}.tar.gz"
		OUT="$TMPDIR/${ARCHIVE}.tar.gz"
		if command -v curl >/dev/null 2>&1; then
			curl -fsSL "$URL" -o "$OUT"
		elif command -v wget >/dev/null 2>&1; then
			wget -q "$URL" -O "$OUT"
		else
			echo "[render] ERROR: neither curl nor wget available to fetch Node.js" >&2
			return 1
		fi
		tar -xzf "$OUT" -C "$TMPDIR"
	fi

	export PATH="$TMPDIR/${ARCHIVE}/bin:$PATH"
	if ! command -v npm >/dev/null 2>&1; then
		echo "[render] ERROR: failed to bootstrap portable Node.js" >&2
		return 1
	fi
	echo "[render] Using portable Node.js: $(node -v), npm: $(npm -v)"
}

# 1) PHP deps without dev, optimized
composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader

# 2) Node/Vite build (make sure Node exists on native PHP runtimes)
if ensure_node; then
	echo "[render] Installing npm dependencies"
	npm ci || npm install
	echo "[render] Building assets with Vite"
	npm run build
else
	echo "[render] WARNING: npm unavailable; skipping asset build. UI may be unstyled."
fi

# 3) Optimize and link storage (idempotent)
php artisan storage:link || true
php artisan optimize || true

echo "[render] Build done"
