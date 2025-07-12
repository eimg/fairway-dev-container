#!/bin/bash
set -e

setup_mysql() {
    echo ""
    echo "📁 MySQL Data Directory Setup"
    echo "Setting proper ownership for MySQL data directory..."
    chown -R mysql:mysql /mysql/data
    chmod 750 /mysql/data
    echo "MySQL ownership configured"

    # Initialize MySQL data directory if it's empty or corrupted
    # Check for MySQL 8.0+ files: mysql directory and ibdata1 file
    if [ ! -d /mysql/data/mysql ] || [ ! -f /mysql/data/ibdata1 ]; then
        echo "Initializing MySQL data directory..."
        # Remove any existing incomplete data
        rm -rf /mysql/data/*
        # Initialize with timeout protection
        timeout 60 mysqld --initialize-insecure --user=mysql --datadir=/mysql/data
        if [ $? -ne 0 ]; then
            echo "⚠ MySQL initialization failed or timed out. Removing corrupted data and retrying..."
            rm -rf /mysql/data/*
            mysqld --initialize-insecure --user=mysql --datadir=/mysql/data
        fi
        echo "MySQL data directory initialized successfully"
    else
        echo "MySQL data directory already exists, skipping initialization"
    fi
}

setup_redis() {
    echo ""
    echo "📁 Redis Data Directory Setup"
    echo "Setting proper ownership for Redis data directory..."
    mkdir -p /redis/data
    chown -R redis:redis /redis/data
    chmod 750 /redis/data
    echo "Redis data directory setup completed"
}

start_services() {
    echo ""
    echo "🔄 Starting Services"
    
    # PHP-FPM
    echo "Starting PHP-FPM..."
    service php8.4-fpm start
    echo "PHP-FPM started"

    # Nginx
    echo "Starting Nginx..."
    service nginx start
    echo "Nginx started"

    # MySQL
    echo "Starting MySQL..."
    service mysql start
    echo "MySQL started"

    # Redis
    echo "Starting Redis..."
    service redis-server start
    echo "Redis started"

    # Brief wait for services to be ready
    echo "Waiting for services to be ready..."
    sleep 3
    echo ""
    echo "✅ All services are ready"
}

configure_mysql_password() {
    echo ""
    echo "➜ MySQL Configuration"
    echo "Configuring MySQL root password..."
    mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root'; FLUSH PRIVILEGES;" 2>/dev/null || {
        echo "Root password already configured or MySQL not ready yet"
    }
    echo "MySQL configuration completed"
}

print_status() {
    echo ""
    echo "✅ All services started successfully!"
    echo ""
    echo "🟢 Service Information:"
    echo "   ➜ Nginx is running on port 80"
    echo "   ➜ MySQL is running on localhost (root/root)"
    echo "   ➜ MySQL data directory: /mysql/data"
    echo "   ➜ Redis is running on localhost port 6379 (root/root)"
    echo "   ➜ Redis data directory: /redis/data"
    echo "   ➜ PHP-FPM is running"
    echo "   ➜ phpMyAdmin at: http://localhost/phpmyadmin/"
    echo ""
    echo "🎉 Development environment is ready!"
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
} 