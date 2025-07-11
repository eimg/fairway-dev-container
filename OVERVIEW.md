# Complete PHP & JavaScript Development Environment

All-in-one development environment container with PHP 8.4, Nginx, MySQL 8.0, Redis 7.0.15, Node.js 22, and phpMyAdmin. Perfect for rapid development and prototyping.

## Quick Start

```bash
# Create data directory
mkdir -p mysql/data && mkdir -p redis/data

# Run container
docker run -d \
  -p 90:80 \
  -p 8081:8081 \
  -p 19000:19000 \
  -p 3000:3000 \
  -v "$(pwd):/workspaces" \
  -v "$(pwd)/mysql/data:/mysql/data" \
  -v "$(pwd)/redis/data:/redis/data" \
  fairway-pwd
```

Access your environment at http://localhost:90 and phpMyAdmin at http://localhost:90/phpmyadmin

## Starting New Project

Starting a new project? Use this pre-built image:

1. **Pull the image**:
   ```bash
   docker pull fairway-pwd
   ```

2. **Copy devcontainer config**:
   ```bash
   # Create .devcontainer directory
   mkdir -p .devcontainer
   
   # Download devcontainer.json from GitHub
   curl -o .devcontainer/devcontainer.json https://raw.githubusercontent.com/eimg/fairway-dev-container/main/.devcontainer/devcontainer.json
   ```

3. **Open in VS Code**:
   ```bash
   code .
   ```

4. **Reopen in Container**: Command Palette → "Dev Containers: Reopen in Container"

5. **Ready to go!** All services start automatically, and you can begin development immediately.

## What's Included

- **PHP 8.4** with FPM and 20+ extensions (MySQL, GD, cURL, XML, etc.)
- **Nginx** web server with optimized configuration
- **MySQL 8.0** with persistent data storage
- **Redis 7.0** with persistent data storage
- **Node.js 22** with npm for modern frontend tooling
- **phpMyAdmin** for database management
- **Composer** for PHP dependency management
- **React Native & Expo** support with universal networking
- **Development tools**: Git, curl, nano, tree, build-essential
- **Shell**: zsh + oh-my-zsh with dev container indicator

## Key Features

- **Zero Configuration**: Services start automatically
- **Persistent Data**: MySQL data survives container restarts
- **Persistent Data**: Redis data survives container restarts
- **VS Code Ready**: Built-in dev container support
- **Universal Networking**: Works with Android emulator & iOS simulator
- **Multi-Framework**: PHP/Laravel, Node, React Native/Expo development

## Perfect For

- **Full-Stack Projects**
- **Laravel Projects**
- **React Projects**
- **Node Back-end Projects**
- **React Native/Expo Projects**
- **Quick Development Environment**

## Usage Examples

### Basic Usage
```bash
docker run -d \
  -p 90:80 \
  -v "$(pwd):/workspaces" \
  -v "$(pwd)/mysql/data:/mysql/data" \
  -v "$(pwd)/redis/data:/redis/data" \
  fairway-pwd
```

### With React Native/Expo Support
```bash
docker run -d \
  -p 90:80 -p 8081:8081 -p 19000:19000 -p 3000:3000 \
  -v "$(pwd):/workspaces" \
  -v "$(pwd)/mysql/data:/mysql/data" \
  -v "$(pwd)/redis/data:/redis/data" \
  fairway-pwd
```

## More Information

### Laravel
```bash
# Create new Laravel project
composer create-project laravel/laravel my-project
cd my-project
composer run dev
```

**Redis Configuration:**
```bash
# In your .env file
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=root
REDIS_PORT=6379
REDIS_USERNAME=root
```

### React Native & Expo
```bash
# Create new Expo project
npx create-expo-app@latest MyApp
cd MyApp
npx expo start
```

## Services & Ports

- **Web Server**: Port 80 (Nginx + PHP-FPM)
- **MySQL**: Internal only (use phpMyAdmin or connect from app)
- **Redis**: Internal only (root/root - connect from app)
- **MySQL**: Internal only (root/root - use phpMyAdmin)
- **phpMyAdmin**: /phpmyadmin (auto-login as root)
- **Metro Bundler**: Port 8081 (React Native)
- **Expo DevTools**: Ports 19000-19002
- **Development Servers**: Ports 3000, 4000

## Environment Variables

- `EXPO_DEVTOOLS_LISTEN_ADDRESS=0.0.0.0` - External connections
- `REACT_NATIVE_PACKAGER_HOSTNAME=0.0.0.0` - Universal compatibility

## Configuration Notes

**Vite Projects**: Add `host: "0.0.0.0"` to vite.config.js for proper port forwarding
**Terminal**: Shows `🐳 dev` indicator when in container

## Tags

- `latest` - Latest stable build

---

Perfect for developers who want to focus on coding, not environment setup! 🚀 