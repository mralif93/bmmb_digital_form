#!/bin/bash

################################################################################
# Fix eForm Permissions and Config Issues
################################################################################
# This script fixes file permissions in Docker container and clears caches
#
# Usage:
#   chmod +x fix-permissions.sh
#   ./fix-permissions.sh
################################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}║     Fix eForm Permissions & Configuration                 ║${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

################################################################################
# Step 1: Fix Ownership
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 1: Fixing file ownership...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web chown -R www-data:www-data /var/www/html

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Ownership fixed (www-data:www-data)${NC}"
else
    echo -e "${RED}✗ Failed to fix ownership${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 2: Fix Directory Permissions (755)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 2: Fixing directory permissions (755)...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web find /var/www/html -type d -exec chmod 755 {} \;

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Directory permissions fixed (755)${NC}"
else
    echo -e "${RED}✗ Failed to fix directory permissions${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 3: Fix File Permissions (644)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 3: Fixing file permissions (644)...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web find /var/www/html -type f -exec chmod 644 {} \;

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ File permissions fixed (644)${NC}"
else
    echo -e "${RED}✗ Failed to fix file permissions${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 4: Fix Storage & Cache Permissions (775)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 4: Fixing storage and cache permissions (775)...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Storage and cache permissions fixed (775)${NC}"
else
    echo -e "${RED}✗ Failed to fix storage/cache permissions${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 5: Verify config/map.php
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 5: Verifying config/map.php...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web ls -la /var/www/html/config/map.php

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ config/map.php exists and is readable${NC}"
else
    echo -e "${RED}✗ config/map.php not found${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 6: Clear Configuration Cache
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 6: Clearing configuration cache...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web php artisan config:clear

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Configuration cache cleared${NC}"
else
    echo -e "${RED}✗ Failed to clear configuration cache${NC}"
fi
echo ""

################################################################################
# Step 7: Clear Application Cache
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 7: Clearing application cache...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web php artisan cache:clear

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Application cache cleared${NC}"
else
    echo -e "${RED}✗ Failed to clear application cache${NC}"
fi
echo ""

################################################################################
# Step 8: Clear View Cache
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 8: Clearing view cache...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web php artisan view:clear

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ View cache cleared${NC}"
else
    echo -e "${RED}✗ Failed to clear view cache${NC}"
fi
echo ""

################################################################################
# Step 9: Regenerate Autoload
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 9: Regenerating Composer autoload...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web composer dump-autoload

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Composer autoload regenerated${NC}"
else
    echo -e "${RED}✗ Failed to regenerate autoload${NC}"
fi
echo ""

################################################################################
# Step 10: Restart Container
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 10: Restarting container...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

docker compose restart web

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Container restarted${NC}"
else
    echo -e "${RED}✗ Failed to restart container${NC}"
fi

# Wait for container to be ready
echo "Waiting for container to be ready..."
sleep 5
echo ""

################################################################################
# Step 11: Verify Configuration
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 11: Verifying MAP configuration...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

sudo docker exec eform_web php artisan config:show map

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✓ Configuration loaded successfully!${NC}"
else
    echo ""
    echo -e "${RED}✗ Configuration failed to load${NC}"
    exit 1
fi
echo ""

################################################################################
# Summary
################################################################################
echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}║          ALL FIXES COMPLETED SUCCESSFULLY ✓                ║${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${BLUE}Permissions Summary:${NC}"
echo "  Ownership: www-data:www-data"
echo "  Directories: 755 (rwxr-xr-x)"
echo "  Files: 644 (rw-r--r--)"
echo "  Storage/Cache: 775 (rwxrwxr-x)"
echo ""

echo -e "${GREEN}✓ Your eForm application should now work correctly!${NC}"
echo ""
echo -e "${BLUE}Next steps:${NC}"
echo "  1. Test the application in your browser"
echo "  2. Check logs: docker compose logs -f web"
echo "  3. If you see any errors, run this script again"
echo ""
