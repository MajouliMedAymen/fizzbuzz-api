# syntax=docker/dockerfile:1

FROM composer:2.7 AS vendor

WORKDIR /app
COPY composer.json composer.lock* ./

RUN if [ -f composer.lock ]; then \
        composer install \
            --no-dev --no-scripts --no-interaction --prefer-dist \
            --optimize-autoloader --ignore-platform-req=ext-pdo_pgsql; \
    else \
        echo "WARNING: no composer.lock found, resolving versions at build time." >&2; \
        composer update \
            --no-dev --no-scripts --no-interaction --prefer-dist \
            --optimize-autoloader --ignore-platform-req=ext-pdo_pgsql; \
    fi

FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache libpq icu-libs nginx supervisor \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS postgresql-dev icu-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql intl opcache \
    && apk del .build-deps

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/app

COPY --from=vendor /app/vendor ./vendor
COPY . .

ENV APP_ENV=prod APP_DEBUG=0

RUN php -m | grep -qx pdo_pgsql \
    && php -m | grep -qx intl \
    && mkdir -p var/cache var/log /tmp/nginx \
    && php bin/console cache:warmup --env=prod --no-debug \
    && chown -R www-data:www-data var /var/lib/nginx /tmp/nginx /var/log/nginx

USER www-data

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD wget -qO- http://127.0.0.1:8080/health/live || exit 1

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]