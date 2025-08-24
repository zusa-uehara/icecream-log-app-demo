FROM php:8.2-apache

WORKDIR /var/www/html

# 必要なツールとライブラリ
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip

# Composer をコピー
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP 拡張
RUN docker-php-ext-install mysqli

# Composer 依存関係
COPY src/composer.json src/composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# アプリ本体をコピー
COPY src/ ./

# Apache フォアグラウンドで起動
CMD ["apache2-foreground"]
