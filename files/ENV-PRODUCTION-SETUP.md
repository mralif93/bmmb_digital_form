# Environment Configuration Template for Production Server

## Copy this to your production .env file and update the paths

```bash
# MAP SSO Configuration
MAP_BASE_URL=https://your-map-domain.com  # Or http://127.0.0.1:8000 for local
MAP_SSO_SECRET=your-production-secret-here
MAP_LOGOUT_URL=https://your-map-domain.com/pengurusan/logout/

# MAP Database Path for Sync Operations
# **IMPORTANT**: Update this path to match your server setup
# 
# Common production paths:
# - Docker mounted volume: /map_db/db.sqlite3
# - Local file system: /opt/FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3
# - Shared drive: /mnt/shared/MAP/db.sqlite3
#
MAP_DATABASE_PATH=/path/to/your/MAP/db.sqlite3

# Database Path for Docker Volume (eForm database)
MAP_DB_PATH=/opt/eform/eform_db
```

## Finding Your MAP Database Path

Run this command on your server to find the MAP database:

```bash
# Search for db.sqlite3 in FinancingApp directories
find / -name "db.sqlite3" -path "*FinancingApp*" 2>/dev/null

# Or check the MAP application directory
ls -la /path/to/FinancingApp/FinancingApp_Backend/FinancingApp/
```

## Common Production Setups

### 1. Docker Compose Setup
If both MAP and eForm are running in Docker:

```yaml
# In docker-compose.yml
services:
  eform_web:
    volumes:
      - /path/to/MAP/db.sqlite3:/map_db/db.sqlite3:ro  # Mount MAP DB as read-only
```

Then in `.env`:
```bash
MAP_DATABASE_PATH=/map_db/db.sqlite3
```

### 2. Direct File System Access
If MAP and eForm are on the same server:

```bash
# Find MAP database
ls -la /opt/FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3

# Set in .env
MAP_DATABASE_PATH=/opt/FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3
```

### 3. Network Mounted Drive
If MAP database is on a network share:

```bash
MAP_DATABASE_PATH=/mnt/map_data/db.sqlite3
```

## Verification

After setting `MAP_DATABASE_PATH`, verify it's correct:

```bash
# Check if file exists and is readable
php artisan tinker --execute="
    \$path = config('map.database_path');
    echo 'Database path: ' . \$path . PHP_EOL;
    echo 'File exists: ' . (file_exists(\$path) ? 'YES' : 'NO') . PHP_EOL;
    echo 'File readable: ' . (is_readable(\$path) ? 'YES' : 'NO') . PHP_EOL;
    if (file_exists(\$path)) {
        echo 'File size: ' . filesize(\$path) . ' bytes' . PHP_EOL;
    }
"
```

## Testing Database Connection

```bash
# Test connection with dry-run
php artisan map:sync-from-db --dry-run

# If successful, you should see:
# "Found X users to sync"
```

## Troubleshooting

### Error: "MAP database not found"
- Check the path is absolute, not relative
- Verify file permissions (must be readable by web server user)
- Ensure the file actually exists at that location

### Error: "Permission denied"
```bash
# Check file permissions
ls -la /path/to/db.sqlite3

# Fix if needed (as root/sudo)
chmod 644 /path/to/db.sqlite3
chown www-data:www-data /path/to/db.sqlite3  # Or your web server user
```

### Error: "Failed to connect to MAP database"
- Verify SQLite3 extension is installed: `php -m | grep sqlite`
- Check file is not corrupted: `sqlite3 /path/to/db.sqlite3 "SELECT 1;"`
