# --- Build stage ---
FROM php:8.3-fpm AS builder

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libonig-dev libxml2-dev libpng-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --optimize-autoloader

# --- Runtime stage ---
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y nginx

COPY --from=builder /var/www/html /var/www/html

COPY ./nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD service nginx start && php-fpm
