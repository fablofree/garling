#!/bin/sh
# ──────────────────────────────────────────────────────────────────────────────
# Container entrypoint
# 1. Waits until PostgreSQL is reachable.
# 2. Runs docker/migrate.php — tracks migrations & seeds in the DB so this is
#    fully idempotent on every container restart.
# 3. Hands off to Apache.
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
echo "[entrypoint] Running migrations & seeds ..."

php /var/www/html/docker/migrate.php

echo "[entrypoint] Starting Apache ..."
exec apache2-foreground
