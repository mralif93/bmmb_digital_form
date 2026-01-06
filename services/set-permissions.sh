#!/bin/bash

################################################################################
# Set Executable Permissions for All Shell Scripts
# This script should be run on the SERVER after git pull/clone
################################################################################

set -e

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}=========================================${NC}"
echo -e "${BLUE}  Setting Shell Script Permissions${NC}"
echo -e "${BLUE}=========================================${NC}"
echo ""

# Get the script's directory (project root)
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd "$SCRIPT_DIR"

echo -e "${YELLOW}→ Searching for all .sh files...${NC}"
echo ""

# Find and set permissions for all .sh files
SH_FILES=$(find . -type f -name "*.sh" ! -path "./vendor/*")

if [ -z "$SH_FILES" ]; then
    echo -e "${YELLOW}No .sh files found (excluding vendor directory)${NC}"
    exit 0
fi

COUNT=0
while IFS= read -r file; do
    echo -e "${GREEN}✓${NC} Making executable: $file"
    chmod +x "$file"
    ((COUNT++))
done <<< "$SH_FILES"

echo ""
echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}  Success!${NC}"
echo -e "${GREEN}  Made $COUNT shell scripts executable${NC}"
echo -e "${GREEN}=========================================${NC}"
echo ""

# List all executable .sh files for verification
echo -e "${BLUE}Verification (all .sh files):${NC}"
ls -lh *.sh 2>/dev/null || echo "No .sh files in root directory"
echo ""

echo -e "${YELLOW}TIP: Run this script after every git pull:${NC}"
echo -e "     ${BLUE}./set-permissions.sh${NC}"
echo ""
