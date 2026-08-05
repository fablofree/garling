FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;

EXPOSE 80

ENV DB_DRIVER=pgsql \
    DB_HOST=db \
    DB_PORT=5432 \
    DB_DATABASE=garage_lingiah \
    DB_USERNAME=postgres \
    DB_PASSWORD=postgres \
    APP_DEBUG=false

ENTRYPOINT ["entrypoint.sh"]
