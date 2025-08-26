FROM php:8.2-apache

# 必要なツールを追加
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip

# Composer をコピー
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# mysqli 拡張
RUN docker-php-ext-install mysqli

# アプリのソースをコピー
COPY src/ /var/www/html/
