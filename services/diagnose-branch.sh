#!/bin/bash

################################################################################
# Diagnose Branch Mismatch Issue
################################################################################
# This script checks why a user has wrong branch assigned
#
# Usage:
#   ./diagnose-branch.sh mralif93
################################################################################

USERNAME=${1:-mralif93}

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

# Determine if running in Docker or local
if [ -f "docker-compose.yml" ]; then
    EXEC_PREFIX="docker compose exec web"
else
    EXEC_PREFIX=""
fi

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}║     Branch Mismatch Diagnostic for: $USERNAME              ║${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${CYAN}Step 1: Check user in eForm database...${NC}"
$EXEC_PREFIX php artisan tinker --execute="
\$user = App\Models\User::where('username', '$USERNAME')->first();
if (!\$user) {
    echo '❌ User not found in eForm!' . PHP_EOL;
    exit(1);
}

echo '✓ User found in eForm:' . PHP_EOL;
echo '  Name: ' . \$user->full_name . PHP_EOL;
echo '  Branch ID: ' . \$user->branch_id . PHP_EOL;
if (\$user->branch) {
    echo '  Branch Name: ' . \$user->branch->branch_name . PHP_EOL;
    echo '  Branch Code: ' . \$user->branch->ti_agent_code . PHP_EOL;
} else {
    echo '  Branch: NULL' . PHP_EOL;
}
"

echo ""
echo -e "${CYAN}Step 2: Check user in MAP database...${NC}"
$EXEC_PREFIX php artisan tinker --execute="
try {
    \$mapDbPath = config('map.database_path');
    \$pdo = new PDO('sqlite:' . \$mapDbPath);
    
    \$stmt = \$pdo->prepare(\"
        SELECT 
            u.id,
            u.username,
            u.first_name,
            u.last_name,
            u.branch_id,
            b.name as branch_name,
            b.ti_agent_code as branch_code
        FROM user_user u
        LEFT JOIN Application_branch b ON u.branch_id = b.id
        WHERE u.username = ?
    \");
    \$stmt->execute(['$USERNAME']);
    \$mapUser = \$stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!\$mapUser) {
        echo '❌ User not found in MAP!' . PHP_EOL;
        exit(1);
    }
    
    echo '✓ User found in MAP:' . PHP_EOL;
    echo '  Name: ' . \$mapUser['first_name'] . ' ' . \$mapUser['last_name'] . PHP_EOL;
    echo '  Branch ID (MAP): ' . \$mapUser['branch_id'] . PHP_EOL;
    echo '  Branch Name (MAP): ' . \$mapUser['branch_name'] . PHP_EOL;
    echo '  Branch Code (MAP): ' . \$mapUser['branch_code'] . PHP_EOL;
    
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo -e "${CYAN}Step 3: Check branch resolution...${NC}"
$EXEC_PREFIX php artisan tinker --execute="
try {
    \$mapDbPath = config('map.database_path');
    \$pdo = new PDO('sqlite:' . \$mapDbPath);
    
    // Get MAP user's branch info
    \$stmt = \$pdo->prepare(\"
        SELECT 
            u.branch_id,
            b.name as branch_name,
            b.ti_agent_code as branch_code
        FROM user_user u
        LEFT JOIN Application_branch b ON u.branch_id = b.id
        WHERE u.username = ?
    \");
    \$stmt->execute(['$USERNAME']);
    \$mapUser = \$stmt->fetch(PDO::FETCH_ASSOC);
    
    echo 'Resolving branch using sync logic:' . PHP_EOL;
    echo '  MAP branch_id: ' . \$mapUser['branch_id'] . PHP_EOL;
    echo '  MAP branch_code: ' . \$mapUser['branch_code'] . PHP_EOL;
    echo '  MAP branch_name: ' . \$mapUser['branch_name'] . PHP_EOL;
    echo '' . PHP_EOL;
    
    // Try resolution by code (priority 1)
    if (\$mapUser['branch_code']) {
        \$byCode = App\Models\Branch::where('ti_agent_code', \$mapUser['branch_code'])->first();
        if (\$byCode) {
            echo '  ✓ Found by CODE: ' . \$byCode->branch_name . ' (ID: ' . \$byCode->id . ')' . PHP_EOL;
        } else {
            echo '  ✗ Not found by CODE' . PHP_EOL;
        }
    }
    
    // Try resolution by name (priority 2)
    if (\$mapUser['branch_name']) {
        \$byName = App\Models\Branch::where('branch_name', \$mapUser['branch_name'])->first();
        if (\$byName) {
            echo '  ✓ Found by NAME: ' . \$byName->branch_name . ' (ID: ' . \$byName->id . ')' . PHP_EOL;
        } else {
            echo '  ✗ Not found by NAME' . PHP_EOL;
        }
    }
    
    // Try resolution by ID (priority 3)
    if (\$mapUser['branch_id']) {
        \$byId = App\Models\Branch::where('id', \$mapUser['branch_id'])->first();
        if (\$byId) {
            echo '  ✓ Found by ID: ' . \$byId->branch_name . ' (ID: ' . \$byId->id . ')' . PHP_EOL;
        } else {
            echo '  ✗ Not found by ID' . PHP_EOL;
        }
    }
    
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo -e "${CYAN}Step 4: List all branches with similar names...${NC}"
$EXEC_PREFIX php artisan tinker --execute="
echo 'Branches containing \"JALAN\" or \"MELAKA\":' . PHP_EOL;
\$branches = App\Models\Branch::where('branch_name', 'like', '%JALAN%')
    ->orWhere('branch_name', 'like', '%MELAKA%')
    ->get(['id', 'branch_name', 'ti_agent_code']);

foreach (\$branches as \$b) {
    echo '  ID: ' . \$b->id . ' | Code: ' . \$b->ti_agent_code . ' | Name: ' . \$b->branch_name . PHP_EOL;
}
"

echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}Diagnosis complete!${NC}"
echo ""
echo -e "${CYAN}Possible causes:${NC}"
echo "  1. Branch IDs don't match between MAP and eForm"
echo "  2. Branch code (ti_agent_code) is NULL or different"
echo "  3. Branch name doesn't match exactly (case-sensitive)"
echo "  4. Branch was created with wrong ID during sync"
echo ""
