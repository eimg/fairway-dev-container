# Development Environment Docker Setup

A comprehensive development environment with PHP 8.4, Nginx, MySQL 8.0, Node.js 22, and phpMyAdmin.

## Build Image

```bash
docker build -t fairway-pwd .
```

## Run with Docker Directly

1. **Create MySQL data directory**:

    ```bash
    mkdir -p mysql/data
    ```

2. **Run container**:

    ```bash
    docker run -d \
      -p 90:80 \
      -v "$(pwd):/pwd" \
      -v "$(pwd)/mysql/data:/mysql/data" \
      fairway-pwd
    ```

3. **Access services**:
    - Web Server: http://localhost:90
    - phpMyAdmin: http://localhost:90/phpmyadmin
    - MySQL: Available inside container (root/root)

## Run with VS Code Dev Container

1. **Open in VS Code**: `code .`
2. **Reopen in Container**: Command Palette → "Dev Containers: Reopen in Container"
3. **Auto-setup**: All services start automatically

**Access services** (same as above but port mapping handled automatically):

-   Web Server: http://localhost:90
-   phpMyAdmin: http://localhost:90/phpmyadmin

## Customization for VS Code Dev Container

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

## More Information

### What's Included

-   **OS**: Ubuntu 24.04 LTS
-   **PHP**: 8.4 with FPM and common extensions
-   **Web Server**: Nginx
-   **Database**: MySQL 8.0
-   **Node.js**: 22 with npm
-   **Tools**: Composer, phpMyAdmin
-   **Dev Tools**: Git, curl, nano, tree

### File Structure

-   **Project files**: Mounted to `/pwd`
-   **Web root**: `/var/www/html`
-   **MySQL data**: `/mysql/data` (persisted via volume)
-   **Configuration**: `nginx/`, `phpmyadmin/`, `scripts/`

### Important Notes

-   Container runs as root for development simplicity
-   MySQL only accessible from inside container
-   Use phpMyAdmin for database management
