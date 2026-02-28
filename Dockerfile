FROM php:7.4-fpm

ARG APP_ENV=dev

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./

RUN if [ "$APP_ENV" = "prod" ]; then \
        composer install --no-dev --optimize-autoloader --no-interaction --no-scripts; \
    else \
        composer install --optimize-autoloader --no-interaction --no-scripts; \
    fi

COPY . .

RUN composer run-script post-install-cmd --no-interaction || true

RUN mkdir -p var/cache var/log public/uploads \
    && chown -R www-data:www-data var public/uploads \
    && chmod -R 775 var public/uploads

RUN if [ "$APP_ENV" = "prod" ]; then \
        echo "memory_limit=256M\nupload_max_filesize=20M\npost_max_size=20M\nmax_execution_time=60\ndisplay_errors=Off\nopcache.enable=1" \
        > /usr/local/etc/php/conf.d/symfony.ini; \
    else \
        echo "memory_limit=256M\nupload_max_filesize=20M\npost_max_size=20M\nmax_execution_time=60\ndisplay_errors=On\nerror_reporting=E_ALL" \
        > /usr/local/etc/php/conf.d/symfony.ini; \
    fi

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]