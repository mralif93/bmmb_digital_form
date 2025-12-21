# Database Backup & Restore Instructions

## Backup Created
- **Date**: 2025-11-18 10:54
- **Location**: `database/backups/database.sqlite.backup_20251118_105413`
- **Size**: ~940KB

## How to Restore Database

### Option 1: Restore from backup file
```bash
# Stop your application/server first
# Then restore:
cp database/backups/database.sqlite.backup_20251118_105413 database/database.sqlite
```

### Option 2: List all backups and restore
```bash
# List all backups
ls -lh database/backups/

# Restore a specific backup (replace TIMESTAMP with actual timestamp)
cp database/backups/database.sqlite.backup_TIMESTAMP database/database.sqlite
```

### Option 3: Restore from root directory backup
```bash
# If backup exists in database/ directory
cp database/database.sqlite.backup_TIMESTAMP database/database.sqlite
```

## After Restoring
1. Clear application cache (if needed):
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

2. Verify the restore:
   ```bash
   php artisan tinker
   # Then check: \App\Models\Form::count();
   ```

## Create New Backup
```bash
# Manual backup command
cp database/database.sqlite database/backups/database.sqlite.backup_$(date +%Y%m%d_%H%M%S)
```

