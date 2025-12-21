# E-form Quick Deployment Script

#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting eForm deployment...${NC}"

# Navigate to eForm directory
cd /opt/map/bm-gc-repo-map-stg/eForm

# Ensure network exists
echo -e "${YELLOW}Creating Docker network (if not exists)...${NC}"
sudo docker network create -d bridge map_bom_rev_network 2>/dev/null || true

# Set permissions
echo -e "${YELLOW}Setting permissions...${NC}"
sudo chmod -R 755 /opt/map/bm-gc-repo-map-stg/eForm/storage
sudo chmod -R 755 /opt/map/bm-gc-repo-map-stg/eForm/bootstrap/cache

# Build and start containers
echo -e "${YELLOW}Building and starting Docker containers...${NC}"
sudo docker compose up --build -d

# Wait for services to be ready
echo -e "${YELLOW}Waiting for services to start...${NC}"
sleep 5

# Run migrations
echo -e "${YELLOW}Running database migrations...${NC}"
sudo docker compose exec -T web php artisan migrate --force

# Cache optimization
echo -e "${YELLOW}Optimizing caches...${NC}"
sudo docker compose exec -T web php artisan config:cache
sudo docker compose exec -T web php artisan route:cache
sudo docker compose exec -T web php artisan view:cache

# Check status
echo -e "${YELLOW}Checking service status...${NC}"
sudo docker compose ps

echo -e "${GREEN}eForm deployment complete!${NC}"
echo -e "Access at: http://localhost:9001 (or configured domain)"
