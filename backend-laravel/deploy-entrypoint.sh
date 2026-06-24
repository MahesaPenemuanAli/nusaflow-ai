#!/usr/bin/env sh
set -e

PORT="${PORT:-8000}"

php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT}"
