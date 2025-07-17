# Setting Up HTTPS for Development

This guide explains how to set up HTTPS for your local development environment using mkcert with the Docker Compose setup.

## Quick Start
1. Install mkcert on your machine
2. Generate SSL certificates for your custom domain
3. Create HTTPS-enabled docker-compose configuration
4. Start services with HTTPS support

## Prerequisites

- Docker & Docker Compose installed
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
# Create directories for certificates and HTTPS configs
mkdir -p certs nginx-https

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

### Step 2: Create HTTPS nginx configuration
```bash
cat > nginx-https/default-ssl << 'EOF'
# HTTP server - informational only
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

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP via php-fpm
    location ~ \.php$ {
        fastcgi_pass php-fpm:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param HTTPS on;
        fastcgi_param SERVER_PORT 443;
    }
}
EOF
```

### Step 3: Create HTTPS-enabled docker-compose override
```bash
cat > docker-compose.https.yml << 'EOF'
services:
  nginx:
    ports:
      - "8800:80"   # HTTP (info only)
      - "4433:443"  # HTTPS (main access)
    volumes:
      - ./src:/var/www/html
      - ./certs:/etc/nginx/certs:ro
      - ./nginx-https/default-ssl:/etc/nginx/sites-enabled/default:ro
    depends_on:
      - php-fpm

  php-fpm:
    volumes:
      - ./src:/var/www/html

  mysql:
    environment:
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - ./mysql-data:/var/lib/mysql

  redis:
    volumes:
      - ./redis-data:/data

  phpmyadmin:
    environment:
      PMA_HOST: mysql
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: root
    ports:
      - "8801:80"  # phpMyAdmin on HTTP
    depends_on:
      - mysql
EOF
```

## 3. Running with HTTPS

### Start services
```bash
# Start services with HTTPS
docker-compose -f docker-compose.yml -f docker-compose.https.yml up -d

# Check status
docker-compose -f docker-compose.yml -f docker-compose.https.yml ps

# Stop services
docker-compose -f docker-compose.yml -f docker-compose.https.yml down
```

### Access Your Applications
- **HTTPS** (`https://myapp.test:4433`): Main application with SSL
- **HTTP Info** (`http://myapp.test:8800`): Shows informational message  
- **phpMyAdmin** (`http://localhost:8801`): Database management

## 4. Dev Container Integration

**Default Behavior:**
- Dev container uses base `docker-compose.yml` (HTTP only)
- HTTPS is **not automatically enabled**

**To Enable HTTPS:**

**Option 1: Modify devcontainer.json (Recommended)**
```json
{
  "name": "PHP Development Container",
  "dockerComposeFile": [
    "../docker-compose.yml",
    "../docker-compose.https.yml"
  ],
  "service": "php-fpm",
  // ... rest of existing configuration
}
```

**Option 2: Add to existing override**
Add to `.devcontainer/docker-compose.override.yml`:
```yaml
services:
  nginx:
    ports:
      - "8800:80"
      - "4433:443"
    volumes:
      - ../src:/var/www/html:cached
      - ../certs:/etc/nginx/certs:ro
      - ../nginx-https/default-ssl:/etc/nginx/sites-enabled/default:ro
    environment:
      - NGINX_ERROR_LOG_LEVEL=debug
```

**Update port forwarding in devcontainer.json:**
```json
"forwardPorts": [8800, 8801, 4433],
"portsAttributes": {
  "4433": {
    "label": "Web Application (HTTPS)",
    "onAutoForward": "notify"
  }
}
```

## 5. Troubleshooting

**Certificate Issues:**
- **Certificate not trusted?** Run `mkcert -install` again
- **Wrong certificate?** Regenerate with `mkcert myapp.test "*.myapp.test"`

**Connection Issues:**
- **Port in use?** Check with `lsof -i :4433`
- **nginx errors?** Check with `docker-compose ... exec nginx nginx -t`
- **502 errors?** Verify php-fpm is running: `docker-compose ... ps php-fpm`

**Quick Reset:**
```bash
# Restart all services
docker-compose -f docker-compose.yml -f docker-compose.https.yml down
docker-compose -f docker-compose.yml -f docker-compose.https.yml up -d
```

## 6. Advanced: phpMyAdmin HTTPS (Optional)

For phpMyAdmin over HTTPS, create additional configuration:

```bash
cat > docker-compose.https-full.yml << 'EOF'
# Copy contents from docker-compose.https.yml and add:
services:
  nginx-pma:
    build:
      context: ./nginx
      dockerfile: Dockerfile
    ports:
      - "4434:443"
    volumes:
      - ./certs:/etc/nginx/certs:ro
      - ./nginx-https/phpmyadmin-ssl:/etc/nginx/sites-enabled/default:ro
    depends_on:
      - phpmyadmin

  phpmyadmin:
    environment:
      PMA_ABSOLUTE_URI: https://myapp.test:4434/
EOF

# Create phpMyAdmin nginx config
cat > nginx-https/phpmyadmin-ssl << 'EOF'
server {
    listen 443 ssl default_server;
    server_name _;
    
    ssl_certificate /etc/nginx/certs/myapp.test+1.pem;
    ssl_certificate_key /etc/nginx/certs/myapp.test+1-key.pem;

    location / {
        proxy_pass http://phpmyadmin:80;
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-Proto https;
    }
}
EOF

# Use with: docker-compose -f docker-compose.yml -f docker-compose.https-full.yml up -d
# Access at: https://myapp.test:4434
```

## 7. Notes

- **Development only** - never use in production
- Add `certs/` to `.gitignore`
- For standard ports (80/443), modify port mappings in docker-compose.https.yml
- For custom domains, update certificate generation and hosts file accordingly

## 8. File Structure After Setup

```
with-compose/
├── docker-compose.yml              # Base services
├── docker-compose.https.yml        # HTTPS override
├── docker-compose.https-full.yml   # Full HTTPS (optional)
├── certs/                          # SSL certificates
│   ├── myapp.test+1.pem
│   └── myapp.test+1-key.pem
├── nginx-https/                    # HTTPS nginx configs
│   ├── default-ssl                 # Main app HTTPS config
│   └── phpmyadmin-ssl             # phpMyAdmin HTTPS config (optional)
├── nginx/                          # Original nginx files
├── php-fpm/                        # PHP-FPM Dockerfile
├── src/                           # Your application code
└── README.md
```

## 9. Security Notes

- **Development only** - never use in production
- Don't commit certificates to git (add `certs/` to `.gitignore`)
- Regenerate certificates if compromised
- Use environment variables for sensitive data in production

## 10. Alternative Configurations

### Using Standard Ports (80/443)
If you want to use standard ports, modify the docker-compose.https.yml:

```yaml
services:
  nginx:
    ports:
      - "80:80"    # HTTP
      - "443:443"  # HTTPS
```

Access at:
- `http://myapp.test` (info only)
- `https://myapp.test`

### Custom Domain
To use a different domain, update:
1. Certificate generation: `mkcert yourdomain.test`
2. Hosts file: `127.0.0.1 yourdomain.test`
3. Certificate paths in nginx configuration
4. PMA_ABSOLUTE_URI in phpMyAdmin configuration 