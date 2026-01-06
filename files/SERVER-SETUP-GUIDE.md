# 🚀 Quick Server Setup Guide

## Step-by-Step Setup on Production Server

### 1️⃣ Configure Database Path (CRITICAL!)

```bash
# Edit .env file
nano .env

# Find and update this line with your production path:
MAP_DATABASE_PATH=/path/to/your/production/MAP/db.sqlite3
```

**Need help finding the path?** Run:
```bash
find /opt -name "db.sqlite3" 2>/dev/null
find /home -name "db.sqlite3" -path "*Financing*" 2>/dev/null
```

---

### 2️⃣ Verify Configuration

```bash
# Make scripts executable
chmod +x verify-db-path.sh migrate-all-data.sh sync-data.sh

# Run verification
./verify-db-path.sh
```

**✓ If all checks pass**, continue to step 3  
**✗ If checks fail**, fix the issues shown before continuing

---

### 3️⃣ Initial Migration (First Time Only)

```bash
# Preview what will be migrated (recommended)
./migrate-all-data.sh --dry-run

# Review the output, then run actual migration
./migrate-all-data.sh
```

This will migrate:
- ✓ 7 Regions
- ✓ 14+ States  
- ✓ All Branches
- ✓ All Users

**Time:** ~5-10 minutes depending on user count

---

### 4️⃣ Regular Sync (Daily/Hourly)

```bash
# Full sync (branches + users)
./sync-data.sh

# Or sync only users (faster)
./sync-data.sh --users-only
```

---

### 5️⃣ Setup Automated Sync (Optional)

```bash
# Edit crontab
crontab -e

# Add daily sync at 6 AM
0 6 * * * cd /path/to/eForm && ./sync-data.sh >> /var/log/eform-sync.log 2>&1

# Or use every 4 hours
0 */4 * * * cd /path/to/eForm && ./sync-data.sh --users-only >> /var/log/eform-sync.log 2>&1
```

---

## 📋 Available Scripts

| Script | Purpose | When to Use |
|--------|---------|-------------|
| `verify-db-path.sh` | Verify MAP database path | Before migration/sync |
| `migrate-all-data.sh` | Initial full migration | First time setup only |
| `sync-data.sh` | Regular data sync | Daily/hourly updates |

---

## 🔧 Common Production Paths

### Docker Setup
```bash
MAP_DATABASE_PATH=/map_db/db.sqlite3
```

### Direct File System
```bash
MAP_DATABASE_PATH=/opt/FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3
```

### Shared Drive
```bash
MAP_DATABASE_PATH=/mnt/shared/MAP/db.sqlite3
```

---

## ✅ Verification Commands

```bash
# Check migration status
php artisan tinker --execute="
    echo 'Regions: ' . DB::table('regions')->count() . PHP_EOL;
    echo 'States: ' . DB::table('states')->count() . PHP_EOL;
    echo 'Branches: ' . DB::table('branches')->count() . PHP_EOL;
    echo 'Users (synced): ' . App\Models\User::where('is_map_synced', true)->count() . PHP_EOL;
"

# Check specific user
php artisan tinker --execute="
    \$u = App\Models\User::where('username', 'YOUR_USERNAME')->first();
    if (\$u) {
        echo 'Role: ' . \$u->role . PHP_EOL;
        echo 'Email: ' . \$u->email . PHP_EOL;
        echo 'Branch: ' . (\$u->branch ? \$u->branch->branch_name : 'None') . PHP_EOL;
    } else {
        echo 'User not found' . PHP_EOL;
    }
"
```

---

## 🆘 Troubleshooting

### "MAP database not found"
```bash
# Verify path in .env
cat .env | grep MAP_DATABASE_PATH

# Check file exists
ls -la /path/from/above/command
```

### "Permission denied"
```bash
# Check permissions
ls -la /path/to/db.sqlite3

# Fix (as root/sudo)
chmod 644 /path/to/db.sqlite3
chown www-data:www-data /path/to/db.sqlite3  # Or your web user
```

### Verification fails
```bash
# Run verification script for detailed diagnostics
./verify-db-path.sh
```

---

## 📚 Full Documentation

- `README-MIGRATION.md` - Complete migration guide
- `ENV-PRODUCTION-SETUP.md` - Environment configuration details
- `MAP_SSO_DEPLOYMENT.md` - SSO and deployment guide

---

## 🎯 Quick Commands Reference

```bash
# Verify setup
./verify-db-path.sh

# First migration (dry run)
./migrate-all-data.sh --dry-run

# First migration (actual)
./migrate-all-data.sh

# Regular sync (preview)
./sync-data.sh --dry-run

# Regular sync (actual)
./sync-data.sh

# Sync only users
./sync-data.sh --users-only

# Sync only branches
./sync-data.sh --branches-only

# Check status
php artisan tinker --execute="echo App\Models\User::where('is_map_synced', true)->count() . ' users synced';"
```

---

## ⚡ Production Checklist

Before going live:

- [ ] Updated `.env` with correct `MAP_DATABASE_PATH`
- [ ] Ran `./verify-db-path.sh` successfully
- [ ] Ran `./migrate-all-data.sh --dry-run` to preview
- [ ] Ran `./migrate-all-data.sh` to execute migration
- [ ] Verified user count matches MAP
- [ ] Tested SSO login flow
- [ ] Set up cron job for automated sync
- [ ] Checked logs for any errors

---

Need help? Check the detailed documentation files or run `./verify-db-path.sh` for diagnostics.
