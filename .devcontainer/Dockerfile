FROM php:8.1-cli

COPY . /usr/src/panelop-core

WORKDIR /usr/src/panelop-core


# Set timezone because it will appear like interactive mode during running.
ENV TZ=Europe/Kiev
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Updates apt.
RUN apt-get update

# Install basic packages.
RUN apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    locales \
    zip \
    libzip-dev \
    libxml2-dev \
    libbz2-dev \
    libcurl4-openssl-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# Cleans cache.
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installs xDebug.
RUN pecl install xdebug-3.1.2
RUN docker-php-ext-enable xdebug

# Gets and adds composer.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY composer.* /usr/src/panelop-core
RUN composer install
