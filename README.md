# Complete PHP & JavaScript Development Environment

All-in-one development environment container with PHP 8.4, Nginx, MySQL 8.0, Redis 7.0, Node.js 22, and phpMyAdmin. **Designed primarily for VS Code Dev Containers** for the best development experience.

## What's Included

-   **OS**: Ubuntu 24.04 LTS with zsh + oh-my-zsh
-   **PHP**: 8.4 with FPM and common extensions
-   **Web Server**: Nginx
-   **Database**: MySQL 8.0
-   **Cache**: Redis 7.0
-   **Node.js**: 22 with npm
-   **Tools**: Composer, phpMyAdmin
-   **Dev Tools**: Git, curl, nano, tree

## VS Code Dev Container (Recommended)

This container is designed with VS Code Dev Containers in mind for the optimal development experience.

**Prerequisites:**
- VS Code with [Dev Containers extension](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

### Quick Start

Use the pre-built image from Docker Hub:

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

**Access services:**
- Web Server: http://localhost:90
- phpMyAdmin: http://localhost:90/phpmyadmin

## Direct Docker Usage

For advanced users who prefer direct Docker commands:

### Build Image

```bash
docker build -t fairway-pwd .
```

### Run with Docker

1. **Create data directories**:
    ```bash
    mkdir -p mysql/data redis/data
    ```

2. **Run container**:
    ```bash
    docker run -d \
        -p 90:80 \
        -p 8081:8081 \
        -p 19000:19000 \
        -p 3000:3000 \
        -v "$(pwd):/app" \
        -v "$(pwd)/mysql/data:/mysql/data" \
        -v "$(pwd)/redis/data:/redis/data" \
        fairway-pwd
    ```

3. **Access services**:
    - Web Server: http://localhost:90
    - phpMyAdmin: http://localhost:90/phpmyadmin
    - MySQL: Available inside container (user: root, pass: root)
    - Redis: Available inside container (user: root, pass: root)

## Development Environments

This container supports multiple development environments, providing everything needed for modern PHP and JavaScript development.

### Laravel Development

This container does not include the Laravel installer as a global Composer package. We recommend using the `composer create-project` approach.

```bash
# Create new Laravel project
composer create-project laravel/laravel my-project
cd my-project
composer run dev
```

**Redis Configuration for Laravel:**
```bash
# In your .env file
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=root
REDIS_PORT=6379
REDIS_USERNAME=root
```

### React Native & Expo Development

This container supports React Native and Expo development with pre-configured ports and environment settings.

#### Available Ports
- **8081**: Metro Bundler
- **19000-19002**: Expo DevTools
- **3000, 4000**: Development servers

#### Network Configuration

For optimal compatibility with both iOS simulator and Android emulator, you need to set the `REACT_NATIVE_PACKAGER_HOSTNAME` to your host machine's IP address.

> [!NOTE]
> React Native/Expo requires specific IP configuration because:
> - iOS Simulator can access the development server via `localhost`
> - Android Emulator uses `10.0.2.2` to access the host machine
> - Setting your actual host IP address works universally for both platforms

**Get your host machine's IP address:**

Run the appropriate command in your host machine's terminal (not inside the container):

**Windows:**
```bash
ipconfig | findstr "IPv4"
```

**macOS:**
```bash
ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}'
```

**Linux:**
```bash
hostname -I | awk '{print $1}'
# or
ip route get 1.1.1.1 | grep -oP 'src \K\S+'
```

#### Usage

Inside the container, set the environment variable using your host machine's IP address:

```bash
# Set your host IP (replace with your actual IP)
export REACT_NATIVE_PACKAGER_HOSTNAME=192.168.1.100
```

Then start your development workflow:

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
- `REACT_NATIVE_PACKAGER_HOSTNAME=<your-host-ip>` - Set to your host machine's IP for both iOS and Android compatibility

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

> [!IMPORTANT]
> Vite defaults to `localhost` which only accepts local connections. Dev containers need `0.0.0.0` to accept connections from outside the container. Without this setting, the host machine won't be able to access the Vite dev server running inside the container.

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

-   **Project files**: Mounted to 
    -   `/workspaces` when using VS Code Dev Container
    -   `/app` when using Docker directly
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