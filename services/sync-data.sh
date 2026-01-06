#!/bin/bash

################################################################################
# MAP to eForm - REGULAR DATA SYNC
################################################################################
# This script syncs updated data from MAP to eForm
# Run this regularly (e.g., daily via cron) to keep data in sync
#
# Usage:
#   chmod +x sync-data.sh
#   ./sync-data.sh                    # Full sync
#   ./sync-data.sh --dry-run          # Preview changes
#   ./sync-data.sh --users-only       # Sync only users
#   ./sync-data.sh --branches-only    # Sync only branches/states/regions
################################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Default options
DRY_RUN=""
SYNC_BRANCHES=true
SYNC_USERS=true
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

# Parse arguments
for arg in "$@"; do
    case $arg in
        --dry-run)
            DRY_RUN="--dry-run"
            ;;
        --users-only)
            SYNC_BRANCHES=false
            ;;
        --branches-only)
            SYNC_USERS=false
            ;;
        *)
            ;;
    esac
done

echo -e "${CYAN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║                                                            ║${NC}"
echo -e "${CYAN}║           MAP to eForm - Regular Data Sync                ║${NC}"
echo -e "${CYAN}║                                                            ║${NC}"
echo -e "${CYAN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📅 Sync started: ${TIMESTAMP}${NC}"
echo -e "${YELLOW}📍 Working directory: ${SCRIPT_DIR}${NC}"
echo ""

if [ "$DRY_RUN" == "--dry-run" ]; then
    echo -e "${YELLOW}⚠️  DRY RUN MODE - No changes will be made${NC}"
    echo ""
fi

# Summary counters
TOTAL_ERRORS=0

################################################################################
# Step 1: Sync Branches, States, Regions (if enabled)
################################################################################
if [ "$SYNC_BRANCHES" = true ]; then
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${GREEN}Syncing Branches, States & Regions${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    
    php artisan map:sync-branches --all $DRY_RUN
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Branch sync completed successfully${NC}"
    else
        echo -e "${RED}✗ Branch sync failed${NC}"
        TOTAL_ERRORS=$((TOTAL_ERRORS + 1))
    fi
    echo ""
fi

################################################################################
# Step 2: Sync Users (if enabled)
################################################################################
if [ "$SYNC_USERS" = true ]; then
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${GREEN}Syncing Users${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    
    php artisan map:sync-from-db $DRY_RUN
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ User sync completed successfully${NC}"
    else
        echo -e "${RED}✗ User sync failed${NC}"
        TOTAL_ERRORS=$((TOTAL_ERRORS + 1))
    fi
    echo ""
fi

################################################################################
# Summary
################################################################################
END_TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

echo -e "${CYAN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║                                                            ║${NC}"

if [ $TOTAL_ERRORS -eq 0 ]; then
    echo -e "${CYAN}║              SYNC COMPLETED SUCCESSFULLY                   ║${NC}"
else
    echo -e "${CYAN}║          SYNC COMPLETED WITH ${TOTAL_ERRORS} ERROR(S)                    ║${NC}"
fi

echo -e "${CYAN}║                                                            ║${NC}"
echo -e "${CYAN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

if [ "$DRY_RUN" == "--dry-run" ]; then
    echo -e "${YELLOW}⚠️  This was a DRY RUN - no changes were made${NC}"
    echo -e "${YELLOW}Run without --dry-run to apply changes: ./sync-data.sh${NC}"
else
    echo -e "${GREEN}✓ Data sync completed${NC}"
    echo -e "${BLUE}📅 Finished: ${END_TIMESTAMP}${NC}"
    echo ""
    
    echo -e "${BLUE}Current Statistics:${NC}"
    echo ""
    
    # Show statistics
    php artisan tinker --execute="
        echo '  Regions: ' . DB::table('regions')->count() . PHP_EOL;
        echo '  States: ' . DB::table('states')->count() . PHP_EOL;
        echo '  Branches: ' . DB::table('branches')->count() . PHP_EOL;
        echo '  Users (synced): ' . App\Models\User::where('is_map_synced', true)->count() . PHP_EOL;
        echo '  Users (active): ' . App\Models\User::where('status', 'active')->count() . PHP_EOL;
        echo '  Users (total): ' . App\Models\User::count() . PHP_EOL;
    "
fi

echo ""

# Exit with error code if there were errors
if [ $TOTAL_ERRORS -gt 0 ]; then
    exit 1
fi

exit 0
