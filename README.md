# Complete PHP Development Environment

All-in-one development environment container with PHP 8.5, Nginx, MySQL 8.0, Redis 7.0, Node 24 (for Composer/npm asset pipelines), and phpMyAdmin. **Designed primarily for VS Code Dev Containers** for the best development experience, learning, and quick prototyping. Perfect for both development projects and educational environments where you need a complete stack ready to go.

## What's Included

- **OS**: Ubuntu 24.04 LTS with zsh + oh-my-zsh
- **PHP**: 8.5 with FPM and common extensions
- **Web Server**: Nginx
- **Database**: MySQL 8.0
- **Cache**: Redis 7.0
- **Node**: 24 with npm, yarn, and pnpm (front-end tooling alongside PHP)
- **Tools**: Composer, phpMyAdmin
- **Dev Tools**: git, github-cli, curl, nano, tree

## VS Code Dev Container (Recommended)

This container is designed with VS Code Dev Containers in mind for the optimal development experience.

**Prerequisites:**

- VS Code with [Dev Containers extension](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

### Quick Start

Use the pre-built image from Docker Hub:

1. **Pull the image**:
  ```bash
   docker pull eimg/fairway-pwd
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

- Web Server: [http://localhost:8800](http://localhost:8800)
- phpMyAdmin: [http://localhost:8800/phpmyadmin](http://localhost:8800/phpmyadmin)

## Direct Docker Usage

For advanced users who prefer direct Docker commands:

### Build Image

```bash
docker build -t eimg/fairway-pwd .
```

### Run with Docker

1. **Create data directories**:
  ```bash
    mkdir -p mysql-data redis-data
  ```
2. **Run container**:
  ```bash
    docker run -d \
        -p 8800:80 \
        -p 3000:3000 \
        -p 4000:4000 \
        -v "$(pwd):/app" \
        -v "$(pwd)/mysql-data:/mysql/data" \
        -v "$(pwd)/redis-data:/redis/data" \
        -v "gh-auth:/root/.config/gh" \
        -v "$HOME/.gitconfig:/root/.gitconfig" \
        eimg/fairway-pwd
  ```
3. **Access services**:
  - Web Server: [http://localhost:8800](http://localhost:8800)
    - phpMyAdmin: [http://localhost:8800/phpmyadmin](http://localhost:8800/phpmyadmin)
    - MySQL: Available inside container (user: root, pass: root)
    - Redis: Available inside container (user: root, pass: root)

## Development Environments

This container is oriented toward **PHP applications** (Laravel and similar) with **Node** available for package installs, Vite, and other front-end build steps—not a full mobile or cross-platform JavaScript workflow.

### Laravel Development

This container does not include the Laravel installer as a global Composer package. We recommend using the `composer create-project` approach.

```bash
# Create new Laravel project
composer create-project laravel/laravel my-project
cd my-project
composer run dev
```

### Node & package managers

**Node 24** is included so you can run `npm`/`yarn`/`pnpm` in PHP projects (for example Laravel’s Vite integration). A few CLI tools are installed globally for convenience:

#### Package Managers

- **npm**: Default Node package manager
- **yarn**: Fast, reliable package manager
- **pnpm**: Efficient disk space usage with symlinked node_modules

#### Global CLI tools

These are installed globally for **quick scripts and small utilities**; prefer project-local dependencies for real apps:

- **nodemon**: Auto-restart Node applications during development
- **ts-node**: Execute TypeScript files directly without compilation
- **typescript**: TypeScript compiler
- **http-server**: Simple HTTP server for static files
- **prettier**: Code formatter
- **eslint**: JavaScript/TypeScript linter

> [!NOTE]
> For production projects and team consistency, **always use local packages** instead of global ones.

### Vite (e.g. Laravel)

Vite needs to listen on all interfaces so the dev server is reachable from the host when using Dev Containers or published ports:

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

## Customization

### Change Port Mapping

Edit `.devcontainer/devcontainer.json`:

```json
"runArgs": ["-p", "8800:80", "-e", "DEV_CONTAINER=true"]
```

### Add Environment Variables

```json
"runArgs": ["-p", "8800:80", "-e", "DEV_CONTAINER=true", "-e", "YOUR_VAR=value"]
```

### Additional VS Code Extensions

Add to `customizations.vscode.extensions` in `.devcontainer/devcontainer.json`.

## File Structure

- **Project files**: Mounted to 
  - `/workspaces` when using VS Code Dev Container
  - `/app` when using Docker directly
- **Web root**: `/var/www/html`
- **MySQL data**: `/mysql/data` (persisted via volume)
- **Redis data**: `/redis/data` (persisted via volume)
- **Configuration**: `nginx/`, `phpmyadmin/`, `scripts/`

## Important Notes

- ⚠️ Container runs as root for development simplicity
- ⚠️ `.gitconfig` is bind-mounted from host for development convenience - this shares your git credentials with the container and should only be used in trusted development environments
- MySQL only accessible from inside container
- Redis only accessible from inside container
- Terminal shows `🐳 dev` indicator when in dev container

