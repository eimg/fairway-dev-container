# Complete PHP & JavaScript Development Environment

All-in-one development environment container with PHP 8.4, Nginx, MySQL 8.0, Redis 7.0, Node 22, and phpMyAdmin. **Designed primarily for VS Code Dev Containers** for the best development experience, learning, and quick prototyping. Perfect for both development projects and educational environments where you need a complete stack ready to go.

## What's Included

-   **OS**: Ubuntu 24.04 LTS with zsh + oh-my-zsh
-   **PHP**: 8.4 with FPM and common extensions
-   **Web Server**: Nginx
-   **Database**: MySQL 8.0
-   **Cache**: Redis 7.0
-   **Node**: 22 with npm
-   **Tools**: Composer, phpMyAdmin
-   **Dev Tools**: git, github-cli, curl, nano, tree

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
        -v "gh-auth:/root/.config/gh" \
        -v "$HOME/.gitconfig:/root/.gitconfig" \
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

### Node & Package Managers

This container includes **Node 22** with several globally installed packages for development convenience:

#### Package Managers
- **npm**: Default Node package manager
- **yarn**: Fast, reliable package manager
- **pnpm**: Efficient disk space usage with symlinked node_modules

#### Global Development Tools
The following packages are installed globally for **one-off convenience and quick scripts during learning**:
- **nodemon**: Auto-restart Node applications during development
- **ts-node**: Execute TypeScript files directly without compilation
- **typescript**: TypeScript compiler
- **http-server**: Simple HTTP server for static files
- **prettier**: Code formatter
- **eslint**: JavaScript/TypeScript linter

> [!NOTE]
> For production projects and team consistency, **always use local packages** instead of global ones.

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

> [!NOTE]
> Vite requires `0.0.0.0` instead of the default `localhost` to allow connections from outside the container.

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

> [!NOTE]
> Despite this pre-configuration, this container setup is primarily suitable for **quick start learning and prototyping**. For projects requiring development builds and advanced debugging, it's recommended to work directly on the host machine rather than attempting to tweak the container. The constraints of emulators, simulators, and build processes make containerized React Native development rarely worth the complexity.

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

-   ⚠️ Container runs as root for development simplicity
-   ⚠️ `.gitconfig` is bind-mounted from host for development convenience - this shares your git credentials with the container and should only be used in trusted development environments
-   MySQL only accessible from inside container
-   Redis only accessible from inside container
-   Terminal shows `🐳 dev` indicator when in dev container
