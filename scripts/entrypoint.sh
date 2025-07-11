#!/bin/bash
set -e

source /usr/local/bin/service-setup.sh

echo ""
echo "🐳 Fairway Development Container"
echo ""
echo "➜ Starting development services..."

setup_mysql
setup_redis
start_services

# Configure MySQL root password
configure_mysql_password

print_status

# Keep the container running
if [ "${DEV_CONTAINER:-false}" != "true" ]; then
    echo ""
    echo "🔄 Standalone Mode"
    echo "Running in standalone mode, keeping container alive..."
    tail -f /dev/null
fi
