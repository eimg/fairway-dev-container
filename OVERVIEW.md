# Complete PHP & JavaScript Development Environment

All-in-one development environment container with PHP 8.4, Nginx, MySQL 8.0, Node.js 22, and phpMyAdmin. Perfect for rapid development and prototyping.

## Quick Start

```bash
# Create data directory
mkdir -p mysql/data

# Run container
docker run -d \
  -p 80:80 \
  -v "$(pwd):/pwd" \
  -v "$(pwd)/mysql/data:/mysql/data" \
  fairway-pwd
```

Access your environment at http://localhost and phpMyAdmin at http://localhost/phpmyadmin

## What's Included

- **PHP 8.4** with FPM and 20+ extensions (MySQL, GD, cURL, XML, etc.)
- **Nginx** web server with optimized configuration
- **MySQL 8.0** with persistent data storage
- **Node.js 22** with npm for modern frontend tooling
- **phpMyAdmin** for database management
- **Composer** for PHP dependency management
- **Development tools**: Git, curl, nano, tree, build-essential

## Key Features

- **Zero Configuration**: Services start automatically
- **Persistent Data**: MySQL data survives container restarts
- **VS Code Ready**: Built-in dev container support

## Perfect For

- **Full-Stack Projects**: PHP, Laravel backend with Node.js frontend tools
- **Learning & Prototyping**: Quick setup for experiments
- **Team Development**: Consistent environment across team members

## Usage Examples

```bash
docker run -d -p 80:80 -v "$(pwd):/pwd" fairway-pwd
```

### With VS Code Dev Containers
```json
{
  "name": "PHP Dev Environment",
  "image": "fairway-pwd",
  "mounts": [
    "source=${localWorkspaceFolder},target=/pwd,type=bind",
    "source=${localWorkspaceFolder}/mysql/data,target=/mysql/data,type=bind"
  ]
}
```

## Source & More...

See [GitHub Repository](https://github.com/eimg/fairway-dev-container)

## Services & Ports

- **Web Server**: Port 80 (Nginx + PHP-FPM)
- **MySQL**: Internal only (use phpMyAdmin or connect from app)
- **phpMyAdmin**: /phpmyadmin (auto-login as root)

## Tags

- `latest` - Latest stable build

---

**Size**: ~1.76GB | **Base**: Ubuntu 24.04 LTS | **Maintained**: Active

Perfect for developers who want to focus on coding, not environment setup! 🚀 