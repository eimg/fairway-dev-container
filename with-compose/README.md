# PHP Development Environment with Docker

A complete PHP development stack with nginx, PHP-FPM, MySQL, Redis, and phpMyAdmin, optimized for modern PHP development with optional VS Code dev container integration.

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- VS Code with Dev Containers extension (optional)

### Start Development Environment
```bash
# Start all services
docker-compose up -d

# Check status
docker-compose ps
```

**Access Your Application:**
- **Web Application**: http://localhost:8800
- **phpMyAdmin**: http://localhost:8801

## 📋 Services Overview

- **nginx** (Ubuntu 24.04 + nginx, port 8800): Web server & reverse proxy
- **php-fpm** (Ubuntu 24.04 + PHP 8.4): PHP application server  
- **mysql** (MySQL 8.0): Database server
- **redis** (Redis latest): Caching & sessions
- **phpmyadmin** (phpMyAdmin latest, port 8801): Database management

## 🐳 Docker Architecture

### **Container Communication**
- **nginx** ↔ **php-fpm** via TCP (port 9000)
- **php-fpm** ↔ **mysql** via hostname `mysql:3306`
- **php-fpm** ↔ **redis** via hostname `redis:6379`
- **phpmyadmin** ↔ **mysql** via hostname `mysql:3306`

### **Data Persistence**
```
./src/         → /var/www/html     # Application code
./mysql-data/  → /var/lib/mysql    # Database files
./redis-data/  → /data             # Redis persistence
```

### **PHP Configuration**
- **Version**: PHP 8.4
- **Extensions**: MySQL, Redis, GD, XML, cURL, Xdebug, and 30+ more
- **Tools**: Composer, Node.js 22, npm, yarn, pnpm
- **Error Display**: Enabled for development
- **Xdebug**: Available for debugging

## 💻 Development Modes

### **Option 1: Regular Docker (Background Services)**

Perfect for production-like testing and quick development.

```bash
# Start services
docker-compose up -d

# View logs
docker-compose logs -f

# Execute commands in containers
docker-compose exec php-fpm php --version
docker-compose exec php-fpm composer install
docker-compose exec nginx nginx -t

# Stop services
docker-compose down
```

**Characteristics:**
- Containers run in background
- Edit files on host system
- Access containers via `docker-compose exec`
- Suitable for deployment testing

### **Option 2: VS Code Dev Container (Integrated IDE)**

Full IDE integration with VS Code running inside the PHP container.

```bash
# In VS Code:
# 1. Install "Dev Containers" extension
# 2. Open project folder
# 3. Command Palette → "Dev Containers: Reopen in Container"
```

**What You Get:**
- **VS Code runs inside PHP container**
- **Pre-installed extensions**: PHP tools, Xdebug, MySQL, Git
- **Integrated terminal** in container environment
- **Automatic port forwarding**
- **Step-through debugging** with Xdebug
- **SSH keys & Git config** mounted from host
- **GitHub CLI (gh)** pre-installed for repository management

**Auto-Installed Extensions:**
- **PHP & Laravel**: Intelephense, Laravel Extra IntelliSense, Laravel Blade, Laravel Blade Spacer
- **Frontend**: Tailwind CSS, Auto Rename Tag, Path IntelliSense
- **Code Quality**: Prettier
- **Database**: Prisma, SQLite Viewer
- **Environment**: DotENV

## 📁 Project Structure

```
dev-container-2/
├── docker-compose.yml              # Main service definitions
├── .devcontainer/
│   ├── devcontainer.json          # VS Code dev container config
│   └── docker-compose.override.yml # Dev container overrides
├── nginx/
│   ├── Dockerfile                 # Custom nginx image
│   ├── nginx.conf                # Main nginx configuration
│   └── default                   # Site configuration
├── php-fpm/
│   └── Dockerfile                # Custom PHP-FPM image (PHP 8.4)
├── src/                          # Your application code
│   └── index.php                # Main application file
├── mysql-data/                   # Database persistence (auto-created)
├── redis-data/                   # Redis persistence (auto-created)
└── README.md                     # This file
```

## 🔧 Common Development Tasks

### **PHP Development**
```bash
# Install dependencies
docker-compose exec php-fpm composer install

# Run PHP scripts
docker-compose exec php-fpm php src/script.php

# Check PHP configuration
docker-compose exec php-fpm php -i
```

### **Database Operations**
```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u root -p

# Import database
docker-compose exec -T mysql mysql -u root -proot database_name < backup.sql

# Database backup
docker-compose exec mysql mysqldump -u root -proot database_name > backup.sql
```

### **Frontend Development**
```bash
# Install Node packages
docker-compose exec php-fpm npm install

# Build assets
docker-compose exec php-fpm npm run build

# Watch for changes
docker-compose exec php-fpm npm run watch
```

## 🐛 Debugging

### **PHP Error Display**
Errors are displayed directly in the browser with detailed Xdebug formatting.

### **Log Files**
```bash
# PHP-FPM logs
docker-compose logs php-fpm

# Nginx logs
docker-compose logs nginx

# MySQL logs
docker-compose logs mysql
```

### **Xdebug (Dev Container Mode)**
- **Port**: 9003
- **IDE Key**: Automatically configured
- **Step debugging**: Ready to use in VS Code

## 🚨 Troubleshooting

### **Database Connection Errors**
- **Host**: Use `mysql` (not `localhost`)
- **Credentials**: root/root
- **Check**: `docker-compose logs mysql`

### **File Permission Issues**
```bash
# Fix permissions
sudo chown -R $USER:$USER src/
```

## ⚙️ Configuration

### **Environment Variables**
- `MYSQL_ROOT_PASSWORD`: MySQL root password (default: root)
- `PMA_HOST`: phpMyAdmin MySQL host (default: mysql)

### **Port Mapping**
- nginx: Port 80 → 8800 (http://localhost:8800)
- phpMyAdmin: Port 80 → 8801 (http://localhost:8801)

## 🔄 Switching Between Modes

Both modes use the same:
- Database files (`mysql-data/`)
- Application code (`src/`)
- Redis data (`redis-data/`)

You can seamlessly switch between regular Docker and dev container modes without losing data or configuration.
