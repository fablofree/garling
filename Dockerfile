FROM php:8.2-apache

# ── System dependencies ──────────────────────────────────────────────────────
# libpq-dev is required to compile the pdo_pgsql extension.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get purge -y --auto-remove libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# ── Apache ───────────────────────────────────────────────────────────────────
RUN a2enmod rewrite

# Replace the default vhost with one that serves from /public
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# ── Entrypoint ───────────────────────────────────────────────────────────────
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# ── Application source ───────────────────────────────────────────────────────
COPY . /var/www/html/

# Correct ownership; web server needs read access to all files.
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;

EXPOSE 80

# ── Default runtime environment ──────────────────────────────────────────────
# All DB_* variables are overridable at `docker run -e` or via compose env_file.
ENV DB_DRIVER=pgsql \
    DB_HOST=db \
    DB_PORT=5432 \
    DB_DATABASE=garage_lingiah \
    DB_USERNAME=postgres \
    DB_PASSWORD=postgres \
    APP_DEBUG=false

ENTRYPOINT ["entrypoint.sh"]
