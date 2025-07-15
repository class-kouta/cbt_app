# ベースイメージを指定（Debian版で高速化）
FROM php:8.4-fpm

# Laravelが必要とするPHP拡張機能の依存関係をインストール
RUN apt-get update && apt-get install -y \
    curl \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    bcmath \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composerをインストール (公式インストーラーを使用する効率的な方法)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 作業ディレクトリを設定
WORKDIR /var/www

# コンテナのユーザーをwww-dataに設定 (オプションですが、パーミッション問題の予防になります)
USER www-data
