#!/bin/bash
set -e

source /usr/local/bin/mysql-setup.sh

echo "Starting development services..."

setup_mysql
start_services

# Configure MySQL root password
configure_mysql_password

print_status

# Keep the container running
if [ "${DEV_CONTAINER:-false}" != "true" ]; then
    echo "Running in standalone mode, keeping container alive..."
    tail -f /dev/null
fi
