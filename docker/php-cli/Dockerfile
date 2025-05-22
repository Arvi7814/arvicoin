FROM php:8.1-cli

RUN apt-get update && apt-get install -y  \
    git \
    curl \
    libmcrypt-dev  \
    libzip-dev  \
    zip  \
    libxml2-dev  \
    libpq-dev  \
    libpng-dev  \
    libjpeg-dev  \
    libfreetype6-dev  \
    libxslt-dev  \
    libgcrypt-dev  \
    wget  \
    unzip \
    procps \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-source extract \
    && pecl install mcrypt \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ --with-freetype=/usr/include/ \
    && docker-php-ext-install sockets \
    soap \
    gd \
    dom \
    pcntl \
    xsl \
    mysqli \
    pdo_mysql \
    zip \
    bcmath \
    exif \
    && docker-php-source delete

RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/bin --filename=composer --quiet