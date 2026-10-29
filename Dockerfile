# Etapa 1: dependencias de PHP con Composer
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

# Etapa 2: Apache + PHP 8.2 con la aplicación
FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite

# Render indica el puerto en la variable PORT
ENV PORT=10000
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN echo 'Listen ${PORT}' > /etc/apache2/ports.conf

WORKDIR /var/www/html
COPY --from=vendor /app /var/www/html
RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start.sh
RUN sed -i 's/\r$//' /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

EXPOSE 10000
CMD ["/usr/local/bin/start.sh"]
