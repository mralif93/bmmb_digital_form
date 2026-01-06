#!/bin/bash
# Internal permission fix script - runs inside Docker container

echo "Fixing permissions inside container..."

# Fix ownership
chown -R www-data:www-data /var/www/html 2>/dev/null || true

# Fix directory permissions (755)
find /var/www/html -type d -exec chmod 755 {} \; 2>/dev/null || true

# Fix file permissions (644)  
find /var/www/html -type f -exec chmod 644 {} \; 2>/dev/null || true

# Fix storage and cache permissions (775)
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

echo "✓ Permissions fixed"
