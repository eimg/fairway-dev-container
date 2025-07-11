#!/bin/bash
set -e

setup_mysql() {
    # Ensure proper ownership of the MySQL data directory
    echo "Setting proper ownership for MySQL data directory..."
    chown -R mysql:mysql /mysql/data
    chmod 750 /mysql/data

    # Initialize MySQL data directory if it's empty or corrupted
    # Check for MySQL 8.0+ files: mysql directory and ibdata1 file
    if [ ! -d /mysql/data/mysql ] || [ ! -f /mysql/data/ibdata1 ]; then
        echo "Initializing MySQL data directory..."
        # Remove any existing incomplete data
        rm -rf /mysql/data/*
        # Initialize with timeout protection
        timeout 60 mysqld --initialize-insecure --user=mysql --datadir=/mysql/data
        if [ $? -ne 0 ]; then
            echo "MySQL initialization failed or timed out. Removing corrupted data and retrying..."
            rm -rf /mysql/data/*
            mysqld --initialize-insecure --user=mysql --datadir=/mysql/data
        fi
        echo "MySQL data directory initialized successfully."
    else
        echo "MySQL data directory already exists, skipping initialization."
    fi
}

start_services() {
    # PHP-FPM
    echo "Starting PHP-FPM..."
    service php8.4-fpm start

    # Nginx
    echo "Starting Nginx..."
    service nginx start

    # MySQL
    echo "Starting MySQL..."
    service mysql start

    # Brief wait for MySQL to be ready
    echo "Waiting for MySQL to be ready..."
    sleep 3
}

configure_mysql_password() {
    # Set MySQL root password if not already set
    echo "Configuring MySQL root password..."
    mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root'; FLUSH PRIVILEGES;" 2>/dev/null || {
        echo "Root password already configured or MySQL not ready yet..."
    }
}

print_status() {
    echo "All services started successfully!"
    echo "Nginx is running on port 80"
    echo "MySQL is running on localhost (user: root, pass: root)"
    echo "MySQL data directory: /mysql/data"
    echo "PHP-FPM is running"
    echo "phpMyAdmin at: http://localhost/phpmyadmin/"
    echo ""
    echo "Development environment is ready!"
}
