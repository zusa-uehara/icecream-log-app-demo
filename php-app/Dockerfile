FROM php:8.2-apache

# 必要なツールを追加
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Composer をコピー
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# mysqli 拡張
RUN docker-php-ext-install mysqli

# アプリのソースをコピー
COPY src/ /var/www/html/

# wait-for-it.sh をコピー
COPY wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

# Apache 起動時に MySQL が起動するまで待ってから PHP サーバーを起動
CMD ["bash", "-c", "wait-for-it.sh db:3306 --timeout=30 -- apache2-foreground"]
