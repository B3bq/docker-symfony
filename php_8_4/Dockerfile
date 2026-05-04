FROM php:8.4-fpm
ENV DEBIAN_FRONTEND=noninteractive

# 1. Instalacja podstawowych narzędzi systemowych
# Dodano 'unzip' - Composer bardzo go potrzebuje do rozpakowywania paczek
RUN apt-get update && apt-get install -y --no-install-recommends \
    nano vim curl git acl zip unzip gnupg cron apt-utils

# 2. Instalacja zależności dla rozszerzeń PHP oraz samych rozszerzeń
# Skonsolidowałem to w jedną warstwę (RUN), co drastycznie zmniejsza wagę obrazu
RUN apt-get install -y --no-install-recommends \
    zlib1g-dev libicu-dev g++ \
    libpng-dev libxslt-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev \
    libsodium-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl xsl pdo_mysql soap zip bcmath gd sodium opcache

# 3. Instalacja rozszerzeń PECL (Xdebug do nauki, APCu dla Symfony)
RUN pecl install apcu xdebug \
    && docker-php-ext-enable apcu xdebug

# 4. Node.js (przydatne do AssetMapper / Webpack Encore w Symfony)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 5. Instalacja Composera (zapis skrócony i bezpieczniejszy)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 6. (Opcjonalnie) Instalacja Symfony CLI - świetne narzędzie dla studentów
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get install -y symfony-cli

# 7. Czyszczenie cache apt - dobra praktyka Dockerowa, odchudza obraz
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

ENV DEBIAN_FRONTEND=teletype