FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

# Standard Dev Tools
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
    libnss3-tools \
    zsh

# Install Oh My Zsh
RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" || true

# Add dev container indicator to zsh prompt
RUN echo 'export PS1="🐳 dev $PS1"' >> ~/.zshrc

# Set zsh as default shell
RUN chsh -s $(which zsh)

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

# Configure PHP for development
RUN echo "display_errors = On" > /etc/php/8.4/fpm/conf.d/99-development.ini && \
    echo "display_startup_errors = On" >> /etc/php/8.4/fpm/conf.d/99-development.ini && \
    echo "error_reporting = E_ALL" >> /etc/php/8.4/fpm/conf.d/99-development.ini && \
    echo "log_errors = On" >> /etc/php/8.4/fpm/conf.d/99-development.ini && \
    echo "html_errors = On" >> /etc/php/8.4/fpm/conf.d/99-development.ini && \
    echo "opcache.revalidate_freq = 0" >> /etc/php/8.4/fpm/conf.d/99-development.ini && \
    echo "opcache.validate_timestamps = 1" >> /etc/php/8.4/fpm/conf.d/99-development.ini && \
    echo "max_execution_time = 300" >> /etc/php/8.4/fpm/conf.d/99-development.ini && \
    echo "memory_limit = 512M" >> /etc/php/8.4/fpm/conf.d/99-development.ini

# Apply same settings to CLI PHP
RUN cp /etc/php/8.4/fpm/conf.d/99-development.ini /etc/php/8.4/cli/conf.d/99-development.ini

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Node 22
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g nodemon ts-node typescript yarn pnpm http-server prettier eslint

# GitHub CLI
RUN curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | dd of=/usr/share/keyrings/githubcli-archive-keyring.gpg \
    && chmod go+r /usr/share/keyrings/githubcli-archive-keyring.gpg \
    && echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | tee /etc/apt/sources.list.d/github-cli.list > /dev/null \
    && apt-get update \
    && apt-get install gh -y

# React Native & Expo Environment Variables
ENV EXPO_DEVTOOLS_LISTEN_ADDRESS=0.0.0.0
ENV REACT_NATIVE_PACKAGER_HOSTNAME=0.0.0.0
ENV EXPO_CLI_NO_INSTALL_DEPENDENCIES=1

# Nginx
RUN apt-get install -y nginx
RUN rm -f /etc/nginx/sites-enabled/default

RUN rm -f /var/www/html/index.nginx-debian.html
COPY dashboard/index.php /var/www/html/index.php

COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/sites-available/default /etc/nginx/sites-available/default

RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# MySQL
RUN apt-get update && apt-get install -y debconf-utils
RUN echo "mysql-server mysql-server/root_password password root" | debconf-set-selections
RUN echo "mysql-server mysql-server/root_password_again password root" | debconf-set-selections
RUN apt-get install -y mysql-server

# Create data directories
RUN mkdir -p /mysql/data && mkdir -p /redis/data

# Fix MySQL user home directory to prevent su warnings
RUN mkdir -p /var/lib/mysql && \
    chown mysql:mysql /var/lib/mysql && \
    usermod -d /var/lib/mysql mysql

# Create MySQL configuration for development
RUN echo "[mysqld]" > /etc/mysql/conf.d/development.cnf && \
    echo "default_authentication_plugin=mysql_native_password" >> /etc/mysql/conf.d/development.cnf

# Configure MySQL to use external data directory
RUN echo "[mysqld]" > /etc/mysql/conf.d/datadir.cnf && \
    echo "datadir = /mysql/data" >> /etc/mysql/conf.d/datadir.cnf

# Set proper ownership for MySQL data directory
RUN mkdir -p /mysql/data && chown -R mysql:mysql /mysql/data

# Fix debian-sys-maint user for service management
RUN cat > /etc/mysql/debian.cnf << 'EOF'
# Automatically generated for Debian scripts. DO NOT TOUCH!
[client]
host     = localhost
user     = debian-sys-maint
password = 
socket   = /var/run/mysqld/mysqld.sock
[mysql_upgrade]
host     = localhost
user     = debian-sys-maint
password = 
socket   = /var/run/mysqld/mysqld.sock
basedir  = /usr
EOF

# Note: MySQL data directory initialization and startup must be handled at runtime
# in the entrypoint script because the external data directory (/mysql/data)
# is mounted as a volume and not available during build time.

# phpMyAdmin
RUN wget https://files.phpmyadmin.net/phpMyAdmin/5.2.2/phpMyAdmin-5.2.2-all-languages.zip -O phpmyadmin.zip && \
    unzip phpmyadmin.zip -d /var/www/html && \
    mv /var/www/html/phpMyAdmin-5.2.2-all-languages /var/www/html/phpmyadmin && \
    rm phpmyadmin.zip

# Copy phpMyAdmin configuration
COPY phpmyadmin/config.inc.php /var/www/html/phpmyadmin/config.inc.php

# Redis
RUN apt-get install -y redis-server
COPY redis/redis.conf /etc/redis/redis.conf 
RUN chown redis:redis /etc/redis/redis.conf

# Adjust ownership for web server
RUN chown -R www-data:www-data /var/www/html/phpmyadmin

# Final Setup & Startup Command
# Clean up apt cache to reduce image size.
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy the shared service setup functions
COPY scripts/service-setup.sh /usr/local/bin/service-setup.sh
RUN chmod +x /usr/local/bin/service-setup.sh

# Copy the entrypoint script and make it executable
COPY scripts/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose ports for web (HTTP)
EXPOSE 80

# Expose React Native & Expo ports
EXPOSE 8081 19000 19001 19002 8097 4000 3000

# Set the working directory
WORKDIR /app

# Startup Command
CMD ["/usr/local/bin/entrypoint.sh"]
