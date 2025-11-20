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

# PHP 8.4 & Extensions
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y \
    php8.4-fpm \
    php8.4-cli \
    php8.4-common \
    php8.4-mysql \
    php8.4-xml \
    php8.4-xmlrpc \
    php8.4-curl \
    php8.4-gd \
    php8.4-imagick \
    php8.4-dev \
    php8.4-imap \
    php8.4-mbstring \
    php8.4-opcache \
    php8.4-soap \
    php8.4-zip \
    php8.4-intl \
    php8.4-bcmath \
    php8.4-gmp \
    php8.4-pdo \
    php8.4-sqlite3 \
    php8.4-pgsql \
    php8.4-redis

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
RUN sed -i 's/listen = \/run\/php\/php8.4-fpm.sock/listen = 9000/' /etc/php/8.4/fpm/pool.d/www.conf

# Enable error display for development
RUN echo "display_errors = On" >> /etc/php/8.4/fpm/php.ini && \
    echo "display_startup_errors = On" >> /etc/php/8.4/fpm/php.ini && \
    echo "error_reporting = E_ALL" >> /etc/php/8.4/fpm/php.ini

CMD ["php-fpm8.4", "-F"]
