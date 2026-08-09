#!/bin/sh
set -e

if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
    echo "Waiting for the database..."
    until php -r 'exit(@fsockopen(getenv("DB_HOST") ?: "database", (int)(getenv("DB_PORT") ?: 5432)) ? 0 : 1);'; do
        sleep 1
    done
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

exec "$@"
