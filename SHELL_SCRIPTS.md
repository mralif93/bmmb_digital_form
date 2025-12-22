# Shell Scripts Permissions

This document lists all shell scripts in the eForm application and their purpose.

## Automatic Permission Setup

All `.sh` files are automatically given executable permissions during Docker build via the Dockerfile:

```dockerfile
# Make all shell scripts executable
RUN find /var/www/html -type f -name "*.sh" -exec chmod +x {} \;
```

## Available Shell Scripts

### 1. **deploy.sh**
**Purpose:** Main deployment script  
**Usage:** `./deploy.sh`

### 2. **diagnose-branch.sh**
**Purpose:** Diagnose branch-related issues in the database  
**Usage:** `./diagnose-branch.sh`

### 3. **fix-permissions.sh**
**Purpose:** Fix file and directory permissions for Laravel  
**Usage:** `./fix-permissions.sh`

### 4. **fix-schema.sh**
**Purpose:** Drop and recreate all database tables with correct schema  
**Usage:** `./fix-schema.sh`

### 5. **migrate-all-data.sh**
**Purpose:** Migrate all data from MAP database to eForm  
**Usage:** `./migrate-all-data.sh`

### 6. **post-deploy.sh**
**Purpose:** Run post-deployment tasks (cache clearing, optimization, etc.)  
**Usage:** `./post-deploy.sh`

### 7. **reset-migration.sh**
**Purpose:** Reset database and re-run all migrations  
**Usage:** `./reset-migration.sh`

### 8. **sync-data.sh**
**Purpose:** Sync data from MAP database to eForm incrementally  
**Usage:** `./sync-data.sh`

### 9. **verify-db-path.sh**
**Purpose:** Verify database paths and connections  
**Usage:** `./verify-db-path.sh`

## Manual Permission Fix (if needed)

If for any reason the automatic permissions don't apply, you can manually set them:

```bash
# Inside the Docker container
find /var/www/html -type f -name "*.sh" -exec chmod +x {} \;

# Or one by one
chmod +x deploy.sh
chmod +x diagnose-branch.sh
chmod +x fix-permissions.sh
chmod +x fix-schema.sh
chmod +x migrate-all-data.sh
chmod +x post-deploy.sh
chmod +x reset-migration.sh
chmod +x sync-data.sh
chmod +x verify-db-path.sh
```

## Running Scripts in Docker

```bash
# Enter the container
docker exec -it eform_web bash

# Run any script
./script-name.sh

# Or run directly from outside
docker exec eform_web ./script-name.sh
```

## Notes

- All scripts are automatically made executable during Docker build
- This ensures scripts work immediately after container startup
- No manual `chmod` needed after deployment
