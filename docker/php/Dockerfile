# PHP 8.3
FROM php:8.3-fpm-bookworm

# System dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    libsqlite3-dev \
    zip \
    unzip \
    git \
    curl \
    gcc \
    make \
    autoconf \
    pkg-config \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        intl \
        zip \
        bcmath \
        sockets \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
