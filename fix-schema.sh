#!/bin/bash

################################################################################
# Fix Database Schema - Re-run Migrations
################################################################################
# This script drops and recreates all tables with the correct schema
#
# Usage:
#   chmod +x fix-schema.sh
#   ./fix-schema.sh
################################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Determine if running in Docker or local
if [ -f "docker-compose.yml" ]; then
    DOCKER_MODE=true
    EXEC_PREFIX="docker compose exec web"
else
    DOCKER_MODE=false
    EXEC_PREFIX=""
fi

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}║     Fix Database Schema (Fresh Migrations)                ║${NC}"
echo -e "${BLUE}║                                                            ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${YELLOW}⚠️  This will DROP all tables and recreate them!${NC}"
echo -e "${YELLOW}⚠️  All data will be lost!${NC}"
echo ""
read -p "Type 'YES' to continue: " CONFIRM

if [ "$CONFIRM" != "YES" ]; then
    echo -e "${GREEN}Cancelled.${NC}"
    exit 0
fi

echo ""
echo -e "${BLUE}Running fresh migrations...${NC}"

$EXEC_PREFIX php artisan migrate:fresh --force

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✓ Database schema recreated successfully!${NC}"
    echo ""
    echo -e "${BLUE}Next step:${NC}"
    echo "  Run ./reset-migration.sh to populate data from MAP"
else
    echo ""
    echo -e "${RED}✗ Migration failed!${NC}"
    exit 1
fi
