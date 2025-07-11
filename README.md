# Complete PHP & JavaScript Development Environment

All-in-one development environment container with PHP 8.4, Nginx, MySQL 8.0, Redis 7.0.15, Node.js 22, and phpMyAdmin. Perfect for rapid development and prototyping.

## What's Included

-   **OS**: Ubuntu 24.04 LTS with zsh + oh-my-zsh
-   **PHP**: 8.4 with FPM and common extensions
-   **Web Server**: Nginx
-   **Database**: MySQL 8.0
-   **Cache**: Redis 7.0.15
-   **Node.js**: 22 with npm
-   **Tools**: Composer, phpMyAdmin
-   **Dev Tools**: Git, curl, nano, tree

## Quick Start

### Build Image

```bash
docker build -t fairway-pwd .
```

### Run with Docker Directly

1. **Create MySQL data directory**:
    ```bash
    mkdir -p mysql/data 
    ```
2. **Create Redis data directory**:
    ```bash
    mkdir -p redis/data 
    ```
3. **Run container**:
    ```bash
    docker run -d \
      -p 90:80 \
      -v "$(pwd):/workspaces" \
      -v "$(pwd)/mysql/data:/mysql/data" \
      -v "$(pwd)/redis/data:/redis/data" \
      fairway-pwd
    ```

4. **Access services**:
    - Web Server: http://localhost:90
    - phpMyAdmin: http://localhost:90/phpmyadmin
    - MySQL: Available inside container (root/root)
    - Redis: Availabe inside container (root/root)

## Starting New Project

Starting a new project? Use the pre-built image from Docker Hub:

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

## More Information

### Laravel Development

This container does not include the Laravel installer as a global Composer package. We recommend using the `composer create-project` approach.

```bash
# Create new Laravel project
composer create-project laravel/laravel my-project
cd my-project
composer run dev
```

### React Native & Expo Development

This container supports React Native and Expo development with universal networking that works with both Android emulator and iOS simulator out of the box.

**Available Ports:**
- **8081**: Metro Bundler
- **19000-19002**: Expo DevTools
- **3000, 4000**: Development servers

**Quick Start:**
```bash
# Create new projects
npx create-expo-app@latest MyApp
cd MyApp

# Start development servers
npx expo start

# For tunneling (if needed)
npm install @expo/ngrok
npx expo start --tunnel
```

**Environment Variables:**
- `EXPO_DEVTOOLS_LISTEN_ADDRESS=0.0.0.0` - External connections
- `REACT_NATIVE_PACKAGER_HOSTNAME=0.0.0.0` - Universal compatibility


### Vite Projects
Vite (used in React SPA and Laravel) requires specific host configuration for VS Code dev container port forwarding:

```javascript
// vite.config.js
export default {
    server: {
        host: "0.0.0.0",
    },
};
```

**Why this is needed:**
- Vite defaults to `localhost` which only accepts local connections
- Dev containers need `0.0.0.0` to accept connections from VS Code's port forwarding
- Without this setting, "Open in Browser" won't work automatically

## Customization

### Change Port Mapping

Edit `.devcontainer/devcontainer.json`:
```json
"runArgs": ["-p", "8080:80", "-e", "DEV_CONTAINER=true"]
```

### Add Environment Variables

```json
"runArgs": ["-p", "90:80", "-e", "DEV_CONTAINER=true", "-e", "YOUR_VAR=value"]
```

### Additional VS Code Extensions

Add to `customizations.vscode.extensions` in `.devcontainer/devcontainer.json`.

## File Structure

### What's Included

-   **OS**: Ubuntu 24.04 LTS
-   **PHP**: 8.4 with FPM and common extensions
-   **Web Server**: Nginx
-   **Database**: MySQL 8.0
-   **Cache**: Redis 7.0.15
-   **Node.js**: 22 with npm
-   **Tools**: Composer, phpMyAdmin
-   **Dev Tools**: Git, curl, nano, tree

### File Structure

-   **Project files**: Mounted to `/workspaces`
-   **Web root**: `/var/www/html`
-   **MySQL data**: `/mysql/data` (persisted via volume)
-   **Redis data**: `/redis/data` (persisted via volume)
-   **Configuration**: `nginx/`, `phpmyadmin/`, `scripts/`

## Important Notes

-   Container runs as root for development simplicity
-   MySQL only accessible from inside container
-   Redis only accessible from inside container
-   Use phpMyAdmin for database management
-   Terminal shows `🐳 dev` indicator when in dev container