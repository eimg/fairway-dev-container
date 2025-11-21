FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
    software-properties-common \
    ca-certificates \
    lsb-release \
    apt-transport-https \
    git \
    curl \
    wget \
    unzip \
    zip \
    nano \
    tree \
    build-essential \
    libnss3-tools

# PHP 8.5 & Extensions
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y \
    php8.5-fpm \
    php8.5-cli \
    php8.5-common \
    php8.5-mysql \
    php8.5-xml \
    php8.5-curl \
    php8.5-gd \
    php8.5-imagick \
    php8.5-dev \
    php8.5-imap \
    php8.5-mbstring \
    php8.5-opcache \
    php8.5-soap \
    php8.5-zip \
    php8.5-intl \
    php8.5-bcmath \
    php8.5-gmp \
    php8.5-sqlite3 \
    php8.5-pgsql \
    php8.5-redis

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Node 24
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g nodemon ts-node typescript yarn pnpm http-server prettier eslint

# Clean up apt cache to reduce image size.
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Configure PHP-FPM to listen on TCP instead of socket
RUN sed -i 's/listen = \\/run\\/php\\/php8.5-fpm.sock/listen = 9000/' /etc/php/8.5/fpm/pool.d/www.conf

# Enable error display for development
RUN echo "display_errors = On" >> /etc/php/8.5/fpm/php.ini && \
    echo "display_startup_errors = On" >> /etc/php/8.5/fpm/php.ini && \
    echo "error_reporting = E_ALL" >> /etc/php/8.5/fpm/php.ini

CMD ["php-fpm8.5", "-F"]
