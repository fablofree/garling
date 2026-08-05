#!/bin/sh
# ──────────────────────────────────────────────────────────────────────────────
# Container entrypoint
# 1. Waits until PostgreSQL is reachable (belt-and-suspenders on top of
#    depends_on healthcheck, which may not be used in all environments).
# 2. Runs setup.php (idempotent: IF NOT EXISTS + ON CONFLICT DO NOTHING).
# 3. Hands off to the default Apache foreground process.
# ──────────────────────────────────────────────────────────────────────────────
set -e

echo "[entrypoint] Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT} ..."

MAX_TRIES=30
TRIES=0
until php -r "
    \$dsn = 'pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}';
    new PDO(\$dsn, '${DB_USERNAME}', '${DB_PASSWORD}');
" 2>/dev/null; do
    TRIES=$((TRIES + 1))
    if [ "$TRIES" -ge "$MAX_TRIES" ]; then
        echo "[entrypoint] ERROR: PostgreSQL not reachable after ${MAX_TRIES}s. Aborting."
        exit 1
    fi
    echo "[entrypoint]   not ready yet (attempt ${TRIES}/${MAX_TRIES}), retrying in 1s ..."
    sleep 1
done

echo "[entrypoint] PostgreSQL is ready."

echo "[entrypoint] Running database setup (migrations + admin seed) ..."
php /var/www/html/setup.php

echo "[entrypoint] Starting Apache ..."
exec apache2-foreground
