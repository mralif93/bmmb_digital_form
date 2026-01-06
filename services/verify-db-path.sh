#!/bin/bash

################################################################################
# MAP Database Path Verification Script
################################################################################
# This script helps you verify that the MAP database path is correctly
# configured and accessible before running migration/sync operations
#
# Usage:
#   chmod +x verify-db-path.sh
#   ./verify-db-path.sh
################################################################################

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}║        MAP Database Path Verification                     ║${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Check if .env exists
if [ ! -f ".env" ]; then
    echo -e "${RED}✗ .env file not found${NC}"
    echo "  Please create .env file first"
    exit 1
fi

echo -e "${BLUE}1. Checking .env configuration...${NC}"
if grep -q "MAP_DATABASE_PATH=" .env; then
    MAP_PATH=$(grep "MAP_DATABASE_PATH=" .env | cut -d '=' -f2-)
    echo -e "${GREEN}✓ MAP_DATABASE_PATH is configured${NC}"
    echo -e "  Value: ${YELLOW}${MAP_PATH}${NC}"
else
    echo -e "${RED}✗ MAP_DATABASE_PATH not found in .env${NC}"
    echo ""
    echo -e "${YELLOW}Please add this line to your .env file:${NC}"
    echo "MAP_DATABASE_PATH=/path/to/your/MAP/db.sqlite3"
    echo ""
    echo -e "${BLUE}Common paths:${NC}"
    echo "  Docker:           /map_db/db.sqlite3"
    echo "  Direct:           /opt/FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3"
    echo "  Development:      ../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3"
    exit 1
fi

echo ""
echo -e "${BLUE}2. Checking Laravel configuration...${NC}"

# Get path from Laravel config
CONFIG_PATH=$(php artisan tinker --execute="echo config('map.database_path');" 2>/dev/null)

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Laravel can read configuration${NC}"
    echo -e "  Resolved path: ${YELLOW}${CONFIG_PATH}${NC}"
else
    echo -e "${RED}✗ Failed to read Laravel configuration${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}3. Verifying file exists...${NC}"

# Check file existence
php artisan tinker --execute="
    \$path = config('map.database_path');
    if (file_exists(\$path)) {
        echo 'EXISTS';
    } else {
        echo 'NOT_FOUND: ' . \$path;
    }
" 2>/dev/null | grep -q "EXISTS"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database file exists${NC}"
else
    echo -e "${RED}✗ Database file not found${NC}"
    echo ""
    echo -e "${YELLOW}Searching for db.sqlite3 files...${NC}"
    find / -name "db.sqlite3" -path "*Financing*" 2>/dev/null | head -5
    exit 1
fi

echo ""
echo -e "${BLUE}4. Checking file permissions...${NC}"

# Check if readable
php artisan tinker --execute="
    \$path = config('map.database_path');
    if (is_readable(\$path)) {
        echo 'READABLE';
    } else {
        echo 'NOT_READABLE';
    }
" 2>/dev/null | grep -q "READABLE"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database file is readable${NC}"
else
    echo -e "${RED}✗ Database file is not readable${NC}"
    echo "  Check file permissions with: ls -la ${CONFIG_PATH}"
    exit 1
fi

echo ""
echo -e "${BLUE}5. Getting file information...${NC}"

php artisan tinker --execute="
    \$path = config('map.database_path');
    echo 'Size: ' . round(filesize(\$path) / 1024 / 1024, 2) . ' MB' . PHP_EOL;
    echo 'Modified: ' . date('Y-m-d H:i:s', filemtime(\$path)) . PHP_EOL;
    echo 'Owner: ' . posix_getpwuid(fileowner(\$path))['name'] . PHP_EOL;
"

echo ""
echo -e "${BLUE}6. Testing database connection...${NC}"

# Try to connect to database
php artisan tinker --execute="
    try {
        \$pdo = new PDO('sqlite:' . config('map.database_path'));
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Test query
        \$stmt = \$pdo->query('SELECT COUNT(*) as count FROM user_user');
        \$result = \$stmt->fetch(PDO::FETCH_ASSOC);
        
        echo 'CONNECTION_SUCCESS:' . \$result['count'];
    } catch (Exception \$e) {
        echo 'CONNECTION_FAILED:' . \$e->getMessage();
    }
" 2>/dev/null | grep "CONNECTION_SUCCESS" > /dev/null

if [ $? -eq 0 ]; then
    USER_COUNT=$(php artisan tinker --execute="
        \$pdo = new PDO('sqlite:' . config('map.database_path'));
        \$stmt = \$pdo->query('SELECT COUNT(*) as count FROM user_user');
        echo \$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    " 2>/dev/null)
    
    echo -e "${GREEN}✓ Database connection successful${NC}"
    echo -e "  Found ${YELLOW}${USER_COUNT}${NC} users in MAP database"
else
    echo -e "${RED}✗ Failed to connect to database${NC}"
    php artisan tinker --execute="
        try {
            \$pdo = new PDO('sqlite:' . config('map.database_path'));
        } catch (Exception \$e) {
            echo \$e->getMessage();
        }
    "
    exit 1
fi

echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}║          ALL CHECKS PASSED ✓                               ║${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}You can now run the migration scripts:${NC}"
echo "  ./migrate-all-data.sh --dry-run    # Preview migration"
echo "  ./migrate-all-data.sh              # Run migration"
echo "  ./sync-data.sh --dry-run           # Preview sync"
echo "  ./sync-data.sh                     # Run sync"
echo ""
