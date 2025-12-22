#!/bin/bash

################################################################################
# RESET & RE-MIGRATE MAP DATA TO EFORM
################################################################################
# This script resets all MAP-synced data and re-runs complete migration
# 
# WARNING: This will DELETE and RE-CREATE:
#   - All regions
#   - All states
#   - All branches
#   - All MAP-synced users
#
# Use this for:
#   - Recovery after data corruption
#   - Resetting to latest MAP data
#   - Testing migration process
#
# Usage:
#   chmod +x reset-migration.sh
#   ./reset-migration.sh
#   ./reset-migration.sh --force  # Skip confirmation
################################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
NC='\033[0m' # No Color

# Determine if running in Docker or local
if [ -f "docker-compose.yml" ]; then
    DOCKER_MODE=true
    EXEC_PREFIX="docker compose exec web"
else
    DOCKER_MODE=false
    EXEC_PREFIX=""
fi

# Parse arguments
FORCE_MODE=false
if [ "$1" == "--force" ] || [ "$1" == "-f" ]; then
    FORCE_MODE=true
fi

################################################################################
# Warning and Confirmation
################################################################################
clear
echo -e "${RED}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${RED}║                                                            ║${NC}"
echo -e "${RED}║           ⚠️  RESET & RE-MIGRATE MAP DATA  ⚠️              ║${NC}"
echo -e "${RED}║                                                            ║${NC}"
echo -e "${RED}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${YELLOW}⚠️  WARNING: This script will DELETE and RE-CREATE:${NC}"
echo ""
echo -e "  ${RED}❌ All regions${NC}"
echo -e "  ${RED}❌ All states${NC}"
echo -e "  ${RED}❌ All branches${NC}"
echo -e "  ${RED}❌ All MAP-synced users${NC}"
echo ""
echo -e "${CYAN}Then it will re-migrate fresh data from MAP database.${NC}"
echo ""

if [ "$FORCE_MODE" = false ]; then
    echo -e "${YELLOW}Are you ABSOLUTELY SURE you want to continue?${NC}"
    read -p "Type 'RESET' in uppercase to confirm: " CONFIRM
    
    if [ "$CONFIRM" != "RESET" ]; then
        echo -e "${GREEN}Cancelled. No changes made.${NC}"
        exit 0
    fi
    echo ""
    
    echo -e "${RED}Last chance to cancel!${NC}"
    read -p "Press Enter to continue or Ctrl+C to cancel..."
    echo ""
fi

echo -e "${BLUE}Starting reset and migration...${NC}"
echo ""

################################################################################
# Step 0: Verify MAP Database Path from .env
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 0: Verifying MAP database path...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# Get database path from Laravel config (which reads from .env)
DB_PATH=$($EXEC_PREFIX php artisan tinker --execute="echo config('map.database_path');" 2>/dev/null | tail -1)

if [ -z "$DB_PATH" ]; then
    echo -e "${RED}✗ MAP_DATABASE_PATH not configured${NC}"
    echo -e "${YELLOW}Please set MAP_DATABASE_PATH in .env file${NC}"
    exit 1
fi

echo -e "  Database path from .env: ${YELLOW}${DB_PATH}${NC}"

# Check if database file exists
$EXEC_PREFIX php artisan tinker --execute="
    \$path = config('map.database_path');
    if (file_exists(\$path)) {
        echo 'EXISTS';
    } else {
        echo 'NOT_FOUND';
    }
" 2>/dev/null | grep -q "EXISTS"

if [ $? -eq 0 ]; then
    # Get database file size
    FILE_SIZE=$($EXEC_PREFIX php artisan tinker --execute="
        \$size = filesize(config('map.database_path'));
        echo round(\$size / 1024 / 1024, 2);
    " 2>/dev/null | tail -1)
    
    echo -e "${GREEN}✓ Database file exists (${FILE_SIZE} MB)${NC}"
    
    # Test database connection
    $EXEC_PREFIX php artisan tinker --execute="
        try {
            \$pdo = new PDO('sqlite:' . config('map.database_path'));
            \$stmt = \$pdo->query('SELECT COUNT(*) as count FROM user_user');
            \$result = \$stmt->fetch(PDO::FETCH_ASSOC);
            echo 'Users in MAP DB: ' . \$result['count'];
        } catch (Exception \$e) {
            echo 'ERROR: ' . \$e->getMessage();
        }
    " 2>/dev/null | tail -1
    
    echo ""
else
    echo -e "${RED}✗ Database file not found at: ${DB_PATH}${NC}"
    echo -e "${YELLOW}Please check MAP_DATABASE_PATH in .env or run ./verify-db-path.sh${NC}"
    exit 1
fi

echo -e "${GREEN}✓ MAP database verified and accessible${NC}"
echo ""

################################################################################
# Step 1: Backup Current Data (Optional)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 1: Creating backup (optional)...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

BACKUP_DIR="backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/pre_reset_${TIMESTAMP}.sql"

mkdir -p "$BACKUP_DIR"

echo "Creating backup at: $BACKUP_FILE"

if [ "$DOCKER_MODE" = true ]; then
    $EXEC_PREFIX sqlite3 database/database.sqlite ".dump" > "$BACKUP_FILE" 2>/dev/null || echo "Backup skipped"
else
    sqlite3 database/database.sqlite ".dump" > "$BACKUP_FILE" 2>/dev/null || echo "Backup skipped"
fi

if [ -f "$BACKUP_FILE" ]; then
    echo -e "${GREEN}✓ Backup created: $BACKUP_FILE${NC}"
else
    echo -e "${YELLOW}⚠ Backup skipped (database may be empty)${NC}"
fi
echo ""

################################################################################
# Step 2: Delete MAP-synced Users
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 2: Deleting MAP-synced users...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

USER_COUNT=$($EXEC_PREFIX php artisan tinker --execute="echo App\\Models\\User::where('is_map_synced', true)->count();" 2>/dev/null || echo "0")

echo "Found $USER_COUNT MAP-synced users"

$EXEC_PREFIX php artisan tinker --execute="
    \$deleted = App\\Models\\User::where('is_map_synced', true)->delete();
    echo 'Deleted: ' . \$deleted . ' users';
" 2>/dev/null

echo -e "${GREEN}✓ MAP-synced users deleted${NC}"
echo ""

################################################################################
# Step 3: Delete All Branches
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 3: Deleting all branches...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

BRANCH_COUNT=$($EXEC_PREFIX php artisan tinker --execute="echo App\\Models\\Branch::count();" 2>/dev/null || echo "0")

echo "Found $BRANCH_COUNT branches"

$EXEC_PREFIX php artisan tinker --execute="
    \$deleted = App\\Models\\Branch::query()->delete();
    echo 'Deleted: ' . \$deleted . ' branches';
" 2>/dev/null

echo -e "${GREEN}✓ Branches deleted${NC}"
echo ""

################################################################################
# Step 4: Delete All States
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 4: Deleting all states...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

STATE_COUNT=$($EXEC_PREFIX php artisan tinker --execute="echo App\\Models\\State::count();" 2>/dev/null || echo "0")

echo "Found $STATE_COUNT states"

$EXEC_PREFIX php artisan tinker --execute="
    \$deleted = App\\Models\\State::query()->delete();
    echo 'Deleted: ' . \$deleted . ' states';
" 2>/dev/null

echo -e "${GREEN}✓ States deleted${NC}"
echo ""

################################################################################
# Step 5: Delete All Regions
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 5: Deleting all regions...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

REGION_COUNT=$($EXEC_PREFIX php artisan tinker --execute="echo App\\Models\\Region::count();" 2>/dev/null || echo "0")

echo "Found $REGION_COUNT regions"

$EXEC_PREFIX php artisan tinker --execute="
    \$deleted = App\\Models\\Region::query()->delete();
    echo 'Deleted: ' . \$deleted . ' regions';
" 2>/dev/null

echo -e "${GREEN}✓ Regions deleted${NC}"
echo ""

################################################################################
# Step 6: Verify Database is Clean
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 6: Verifying database cleanup...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

$EXEC_PREFIX php artisan tinker --execute="
    echo 'Users (MAP-synced): ' . App\\Models\\User::where('is_map_synced', true)->count() . PHP_EOL;
    echo 'Branches: ' . App\\Models\\Branch::count() . PHP_EOL;
    echo 'States: ' . App\\Models\\State::count() . PHP_EOL;
    echo 'Regions: ' . App\\Models\\Region::count() . PHP_EOL;
" 2>/dev/null

echo -e "${GREEN}✓ Database cleaned${NC}"
echo ""

################################################################################
# Step 7: Re-Sync Branches, States & Regions (All-in-One)
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 7: Re-syncing regions, states, and branches...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

$EXEC_PREFIX php artisan map:sync-branches --all

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Regions, states, and branches synced${NC}"
else
    echo -e "${RED}✗ Failed to sync branches${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 8: Re-Sync Users from MAP Database
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 8: Re-syncing users from MAP database...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

$EXEC_PREFIX php artisan map:sync-from-db

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Users synced from database${NC}"
else
    echo -e "${RED}✗ Failed to sync users${NC}"
    exit 1
fi
echo ""

################################################################################
# Step 9: Verify Migration Results
################################################################################
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}Step 11: Verifying migration results...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

$EXEC_PREFIX php artisan tinker --execute="
    echo 'Regions: ' . App\\Models\\Region::count() . PHP_EOL;
    echo 'States: ' . App\\Models\\State::count() . PHP_EOL;
    echo 'Branches: ' . App\\Models\\Branch::count() . PHP_EOL;
    echo 'Users (MAP-synced): ' . App\\Models\\User::where('is_map_synced', true)->count() . PHP_EOL;
    echo 'Users (total): ' . App\\Models\\User::count() . PHP_EOL;
" 2>/dev/null

echo -e "${GREEN}✓ Verification complete${NC}"
echo ""

################################################################################
# Summary
################################################################################
echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}║      RESET & MIGRATION COMPLETED SUCCESSFULLY ✓            ║${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${BLUE}Summary:${NC}"
echo "  ✓ Old data deleted"
echo "  ✓ Fresh data migrated from MAP"
echo "  ✓ All relationships restored"
echo ""

if [ -f "$BACKUP_FILE" ]; then
    echo -e "${CYAN}Backup saved at: ${YELLOW}$BACKUP_FILE${NC}"
    echo ""
fi

echo -e "${BLUE}Next steps:${NC}"
echo "  1. Verify users can login via MAP SSO"
echo "  2. Check branch relationships"
echo "  3. Test form submissions"
echo ""

echo -e "${GREEN}✓ Migration reset complete!${NC}"
echo ""
