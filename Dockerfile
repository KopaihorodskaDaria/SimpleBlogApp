FROM php:8.3-cli

RUN apt-get update && apt-get install -y default-mysql-client libzip-dev unzip zip curl \
    && docker-php-ext-install pdo pdo_mysql zip



COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]


