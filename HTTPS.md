# Setting Up HTTPS for Development

This guide explains how to set up HTTPS for your local development environment using mkcert.

## Quick Start
1. Install mkcert on your machine
2. Generate SSL certificates for your custom domain
3. Create Nginx HTTPS config
4. Run container with mounted certificates

## Prerequisites

- Our dev container running
- Admin access to your host machine (for mkcert installation)

## 1. Installing mkcert

### macOS
```bash
brew install mkcert
brew install nss # if you use Firefox
mkcert -install
```

### Linux
```bash
# Ubuntu/Debian
sudo apt install libnss3-tools
curl -JLO "https://dl.filippo.io/mkcert/latest?for=linux/amd64"
chmod +x mkcert-v*-linux-amd64
sudo cp mkcert-v*-linux-amd64 /usr/local/bin/mkcert
mkcert -install
```

### Windows
```powershell
choco install mkcert
mkcert -install
```

## 2. Setting Up HTTPS

### Step 1: Create directories and certificates
```bash
# Create directories
mkdir -p nginx-https/sites-available certs

# Generate certificates for your domain
cd certs
mkcert myapp.test "*.myapp.test"
cd ..

# Add custom domain to hosts file
# macOS/Linux
echo "127.0.0.1 myapp.test" | sudo tee -a /etc/hosts

# Windows (as admin)
echo "127.0.0.1 myapp.test" >> %SystemRoot%\System32\drivers\etc\hosts
```

### Step 2: Create Nginx config
```bash
cat > nginx-https/sites-available/default-ssl << 'EOF'
# HTTP informational page
server {
    listen 80 default_server;
    server_name _;
    
    location / {
        add_header Content-Type text/plain;
        return 200 'Use HTTPS with associated port';
    }
}

# HTTPS server
server {
    listen 443 ssl default_server;
    server_name _;
    root /var/www/html;
    index index.php index.html index.htm;

    ssl_certificate /etc/nginx/certs/myapp.test+1.pem;
    ssl_certificate_key /etc/nginx/certs/myapp.test+1-key.pem;

    # Dashboard (default)
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # phpMyAdmin
    location /phpmyadmin/ {
        alias /var/www/html/phpmyadmin/;
        index index.php;
        
        location ~ ^/phpmyadmin/(.+\.php)$ {
            fastcgi_pass unix:/run/php/php8.5-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME /var/www/html/phpmyadmin/$1;
            include fastcgi_params;
        }
    }

    # Development server proxies
    location /php/ {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location /vite/ {
        proxy_pass http://127.0.0.1:5173;
        proxy_set_header Host $host;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location /laravel/ {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
EOF
```

### Step 3: Run with Docker

**Standard ports (80/443):**
```bash
# Using docker run
docker run \
    -v $(pwd)/certs:/etc/nginx/certs \
    -v $(pwd)/nginx-https/sites-available/default-ssl:/etc/nginx/sites-enabled/default \
    -p 80:80 -p 443:443 \
    ... (other options) ...

# Using docker-compose
services:
  web:
    volumes:
      - ./certs:/etc/nginx/certs:ro
      - ./nginx-https/sites-available/default-ssl:/etc/nginx/sites-enabled/default:ro
    ports:
      - "80:80"
      - "443:443"
```

**Custom ports (8800/4433):**
```bash
# Using docker run
docker run \
    -v $(pwd)/certs:/etc/nginx/certs \
    -v $(pwd)/nginx-https/sites-available/default-ssl:/etc/nginx/sites-enabled/default \
    -p 8800:80 -p 4433:443 \
    ... (other options) ...

# Using docker-compose
services:
  web:
    volumes:
      - ./certs:/etc/nginx/certs:ro
      - ./nginx-https/sites-available/default-ssl:/etc/nginx/sites-enabled/default:ro
    ports:
      - "8800:80"
      - "4433:443"
```

## Access Your Applications

### Standard ports (80/443)
- **HTTP** (`http://myapp.test`): Shows informational message
- **HTTPS** (`https://myapp.test`): Full functionality
  - Dashboard: `https://myapp.test`
  - phpMyAdmin: `https://myapp.test/phpmyadmin`
  - PHP Dev Server: `https://myapp.test/php`
  - Vite Dev Server: `https://myapp.test/vite`
  - Laravel Dev Server: `https://myapp.test/laravel`

### Custom ports (8800/4433)
- **HTTP** (`http://myapp.test:8800`): Shows informational message
- **HTTPS** (`https://myapp.test:4433`): Full functionality
  - Dashboard: `https://myapp.test:4433`
  - phpMyAdmin: `https://myapp.test:4433/phpmyadmin`
  - PHP Dev Server: `https://myapp.test:4433/php`
  - Vite Dev Server: `https://myapp.test:4433/vite`
  - Laravel Dev Server: `https://myapp.test:4433/laravel`

## Troubleshooting

- **Certificate not trusted?** Run `mkcert -install` again
- **Connection refused?** Check if ports are free with `netstat -tulpn | grep :443`
- **Mixed content warnings?** Use relative URLs in your applications

## Security Notes

- For development only - never use in production
- Don't commit certificates to git (add `*.pem` to `.gitignore`)
- Regenerate certificates if compromised 
