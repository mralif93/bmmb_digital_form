#!/bin/bash
set -e

# Fix permissions for storage and bootstrap/cache
if [ -f /var/www/html/fix-permissions.sh ]; then
    echo "Fixing permissions..."
    bash /var/www/html/fix-permissions.sh
fi

# Create storage link if it doesn't exist
if [ ! -L /var/www/html/public/storage ]; then
    echo "Creating storage symlink..."
    php artisan storage:link
fi

# Execute the main container command
exec "$@"
