#!/bin/bash

################################################################################
# Post-Deployment Commands - Run After docker-compose down/up
################################################################################
# This script runs all necessary commands after restarting Docker services
#
# Usage:
#   chmod +x post-deploy.sh
#   ./post-deploy.sh
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
echo -e "${BLUE}║         Post-Deployment Setup Commands                    ║${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Determine if running in Docker or local
if [ -f "docker-compose.yml" ]; then
    DOCKER_MODE=true
    echo -e "${YELLOW}📦 Docker mode detected${NC}"
    EXEC_PREFIX="docker compose exec web"
else
    DOCKER_MODE=false
    echo -e "${YELLOW}💻 Local mode detected${NC}"
    EXEC_PREFIX=""
fi

echo ""

################################################################################
# Step 1: Clear Configuration Cache
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 1: Clearing configuration cache...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

$EXEC_PREFIX php artisan config:clear

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Configuration cache cleared${NC}"
else
    echo -e "${RED}✗ Failed to clear configuration cache${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 2: Clear Application Cache
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 2: Clearing application cache...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

$EXEC_PREFIX php artisan cache:clear

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Application cache cleared${NC}"
else
    echo -e "${RED}✗ Failed to clear application cache${NC}"
fi
echo ""

################################################################################
# Step 3: Clear View Cache
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 3: Clearing view cache...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

$EXEC_PREFIX php artisan view:clear

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ View cache cleared${NC}"
else
    echo -e "${RED}✗ Failed to clear view cache${NC}"
fi
echo ""

################################################################################
# Step 4: Clear Route Cache
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 4: Clearing route cache...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

$EXEC_PREFIX php artisan route:clear

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Route cache cleared${NC}"
else
    echo -e "${RED}✗ Failed to clear route cache${NC}"
fi
echo ""

################################################################################
# Step 5: Regenerate Composer Autoload
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 5: Regenerating Composer autoload...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

$EXEC_PREFIX composer dump-autoload

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Composer autoload regenerated${NC}"
else
    echo -e "${RED}✗ Failed to regenerate Composer autoload${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 6: Run Database Migrations (Optional)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 6: Checking for database migrations...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

read -p "Run database migrations? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    $EXEC_PREFIX php artisan migrate --force
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Migrations completed${NC}"
    else
        echo -e "${RED}✗ Migration failed${NC}"
    fi
else
    echo -e "${YELLOW}⊘ Skipped migrations${NC}"
fi
echo ""

################################################################################
# Step 7: Fix Permissions (Docker only)
################################################################################
if [ "$DOCKER_MODE" = true ]; then
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${GREEN}Step 7: Fixing storage permissions...${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    
    docker compose exec web chown -R www-data:www-data /var/www/html/storage
    docker compose exec web chown -R www-data:www-data /var/www/html/bootstrap/cache
    docker compose exec web chmod -R 775 /var/www/html/storage
    docker compose exec web chmod -R 775 /var/www/html/bootstrap/cache
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Permissions fixed${NC}"
    else
        echo -e "${YELLOW}⚠ Permission fix had issues (may need sudo)${NC}"
    fi
    echo ""
fi

################################################################################
# Step 8: Optimize for Production (Optional)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 8: Optimize for production?${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if [ "${APP_ENV:-local}" = "production" ]; then
    read -p "Cache config/routes for production? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "Caching configuration..."
        $EXEC_PREFIX php artisan config:cache
        
        echo "Caching routes..."
        $EXEC_PREFIX php artisan route:cache
        
        echo "Caching views..."
        $EXEC_PREFIX php artisan view:cache
        
        echo -e "${GREEN}✓ Production caching completed${NC}"
    else
        echo -e "${YELLOW}⊘ Skipped production optimization${NC}"
    fi
else
    echo -e "${YELLOW}⊘ Not in production mode, skipping optimization${NC}"
fi
echo ""

################################################################################
# Summary
################################################################################
echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}║          POST-DEPLOYMENT COMPLETE ✓                        ║${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${BLUE}Verification:${NC}"
echo ""

# Test config loading
echo "Testing configuration loading..."
$EXEC_PREFIX php artisan tinker --execute="echo 'Config loaded: ' . (config('map.base_url') ? 'YES' : 'NO');"

echo ""
echo -e "${GREEN}✓ All post-deployment commands completed!${NC}"
echo ""
echo -e "${BLUE}Next steps:${NC}"
echo "  1. Test the application: Visit your URL"
echo "  2. Check logs: docker compose logs -f (if using Docker)"
echo "  3. Verify MAP config: php artisan config:show map"
echo ""
