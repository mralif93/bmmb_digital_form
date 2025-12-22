#!/bin/bash

################################################################################
# MAP to eForm - INITIAL FULL DATA MIGRATION
################################################################################
# This script performs a complete initial migration of all data from MAP to eForm
# Run this ONCE when setting up eForm for the first time
#
# Usage:
#   chmod +x migrate-all-data.sh
#   ./migrate-all-data.sh
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
echo -e "${BLUE}║        MAP to eForm - Initial Full Data Migration         ║${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo -e "${YELLOW}📍 Working directory: ${SCRIPT_DIR}${NC}"
echo ""

# Check if we should do a dry run first
if [ "$1" == "--dry-run" ]; then
    DRY_RUN="--dry-run"
    echo -e "${YELLOW}⚠️  DRY RUN MODE - No changes will be made${NC}"
    echo ""
else
    DRY_RUN=""
    echo -e "${GREEN}✓ LIVE MODE - Changes will be applied${NC}"
    echo ""
    
    read -p "Are you sure you want to proceed with full migration? (yes/no): " CONFIRM
    if [ "$CONFIRM" != "yes" ]; then
        echo -e "${RED}✗ Migration cancelled${NC}"
        exit 0
    fi
    echo ""
fi

################################################################################
# Step 1: Migrate Regions (7 regions)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 1/4: Migrating Regions${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

php artisan map:migrate-regions $DRY_RUN --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Regions migration completed successfully${NC}"
else
    echo -e "${RED}✗ Regions migration failed${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 2: Migrate States (14+ states)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 2/4: Migrating States${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

php artisan map:migrate-states $DRY_RUN --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ States migration completed successfully${NC}"
else
    echo -e "${RED}✗ States migration failed${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 3: Migrate Branches (depends on regions & states)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 3/4: Migrating Branches${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

php artisan map:migrate-branches $DRY_RUN --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Branches migration completed successfully${NC}"
else
    echo -e "${RED}✗ Branches migration failed${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 4: Migrate Users (depends on branches) - Using SYNC command for better logic
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Step 4/4: Migrating Users${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Using sync-from-db instead of migrate-users for better handling
php artisan map:sync-from-db $DRY_RUN

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Users migration completed successfully${NC}"
else
    echo -e "${RED}✗ Users migration failed${NC}"
    exit 1
fi
echo ""

################################################################################
# Summary
################################################################################
echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}║              MIGRATION COMPLETED SUCCESSFULLY              ║${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

if [ "$DRY_RUN" == "--dry-run" ]; then
    echo -e "${YELLOW}⚠️  This was a DRY RUN - no changes were made${NC}"
    echo -e "${YELLOW}Run without --dry-run to apply changes: ./migrate-all-data.sh${NC}"
else
    echo -e "${GREEN}✓ All data has been migrated from MAP to eForm${NC}"
    echo ""
    echo -e "${BLUE}Verification:${NC}"
    echo ""
    
    # Show statistics
    php artisan tinker --execute="
        echo 'Regions: ' . DB::table('regions')->count() . PHP_EOL;
        echo 'States: ' . DB::table('states')->count() . PHP_EOL;
        echo 'Branches: ' . DB::table('branches')->count() . PHP_EOL;
        echo 'Users (synced): ' . App\Models\User::where('is_map_synced', true)->count() . PHP_EOL;
        echo 'Users (total): ' . App\Models\User::count() . PHP_EOL;
    "
    
    echo ""
    echo -e "${GREEN}Next steps:${NC}"
    echo "  1. Verify the data in your eForm application"
    echo "  2. Set up scheduled sync: Add './sync-data.sh' to your crontab"
    echo "  3. Test SSO login flow"
fi

echo ""
