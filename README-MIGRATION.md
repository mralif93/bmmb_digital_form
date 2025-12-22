# MAP to eForm Data Migration & Sync Guide

## ⚙️ IMPORTANT: Configure Database Path First

Before running any migration scripts, you **MUST** configure the MAP database path in your `.env` file:

```bash
# Edit .env file
nano .env

# Add/Update this line with your server's MAP database path:
MAP_DATABASE_PATH=/path/to/your/MAP/db.sqlite3
```

**Common production paths:**
- Docker: `/map_db/db.sqlite3`
- Direct filesystem: `/opt/FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3`
- Custom: Update to match your server setup

**Verify the path:**
```bash
# Check if file exists
ls -la $(php artisan tinker --execute="echo config('map.database_path');")

# Or use this command to verify
php artisan tinker --execute="
    \$path = config('map.database_path');
    echo 'Database path: ' . \$path . PHP_EOL;
    echo 'File exists: ' . (file_exists(\$path) ? 'YES ✓' : 'NO ✗') . PHP_EOL;
"
```

> **📖 See `ENV-PRODUCTION-SETUP.md` for detailed configuration instructions**

---

## Quick Start

### First Time Setup (Initial Migration)

```bash
# Make script executable
chmod +x migrate-all-data.sh

# Preview what will be migrated (dry run)
./migrate-all-data.sh --dry-run

# Run actual migration
./migrate-all-data.sh
```

### Regular Updates (Ongoing Sync)

```bash
# Make script executable
chmod +x sync-data.sh

# Preview changes (dry run)
./sync-data.sh --dry-run

# Sync everything (branches + users)
./sync-data.sh

# Sync only users
./sync-data.sh --users-only

# Sync only branches/states/regions
./sync-data.sh --branches-only
```

---

## Migration Scripts

### 📦 `migrate-all-data.sh`
**Purpose:** Initial full migration of all data from MAP to eForm  
**Run:** Once during initial setup  
**What it does:**
1. Migrates all regions (7 regions)
2. Migrates all states (14+ states)
3. Migrates all branches (depends on regions & states)
4. Migrates all users (depends on branches)

**Options:**
- `--dry-run` - Preview without making changes

**Example:**
```bash
./migrate-all-data.sh --dry-run    # Preview
./migrate-all-data.sh              # Execute
```

---

### 🔄 `sync-data.sh`
**Purpose:** Regular synchronization of updated data  
**Run:** Daily/hourly via cron or manually  
**What it does:**
1. Syncs branches, states, and regions from MAP
2. Syncs all users (creates new, updates existing)
3. Handles inactive users
4. Resolves email collisions
5. Preserves admin roles

**Options:**
- `--dry-run` - Preview without making changes
- `--users-only` - Sync only users
- `--branches-only` - Sync only branches/states/regions

**Examples:**
```bash
./sync-data.sh                     # Full sync
./sync-data.sh --dry-run           # Preview
./sync-data.sh --users-only        # Users only
./sync-data.sh --branches-only     # Branches only
```

---

## What Gets Migrated/Synced

### Regions Table
- 7 regions (Central 1, Central 2, Northern, Southern, East Coast, Sabah, Sarawak)
- Credit check links

### States Table
- 14+ Malaysian states (Perak, Selangor, Kedah, etc.)

### Branches Table
- Branch ID, name, address, email
- State and region references
- TI agent codes
- Weekend settings

### Users Table
From MAP | To eForm
---------|----------
`username` | `username`
`email` | `email` (with collision handling)
`first_name` | `first_name`
`last_name` | `last_name`
`position` | `map_position` + `role`
`branch_id` | `branch_id`
`is_active` | `status` (active/inactive)
`is_superuser` | `role` (admin if superuser)

---

## Role Mapping

MAP Position | eForm Role | Description
-------------|------------|-------------
1 | headquarters | HQ staff
2 | branch_manager | Branch Manager
3 | cfe | Customer Financing Executive
4 | headquarters | COD
9, 10 | operation_officer | Operation Officer
Superuser | admin | Admin (overrides position)

---

## Scheduled Automation

### Add to Crontab

```bash
# Edit crontab
crontab -e

# Add these lines:

# Full sync daily at 6 AM
0 6 * * * cd /path/to/eForm && ./sync-data.sh >> /var/log/eform-sync.log 2>&1

# Or sync every 4 hours
0 */4 * * * cd /path/to/eForm && ./sync-data.sh --users-only >> /var/log/eform-sync.log 2>&1
```

### Using Laravel Scheduler (Already Configured)

The following are already scheduled in `app/Console/Kernel.php`:

Schedule | Command | Purpose
---------|---------|--------
Daily 5 AM | `map:sync-branches --all` | Sync regions, states, branches
Daily 6 AM | `map:sync-from-db` | Full user sync
Every 4 hours | `map:sync-from-db` | Lighter user sync

Just ensure Laravel scheduler is running:
```bash
* * * * * cd /path/to/eForm && php artisan schedule:run >> /dev/null 2>&1
```

---

## Verification Commands

```bash
# Check migration status
php artisan tinker --execute="
    echo 'Regions: ' . DB::table('regions')->count();
    echo '\nStates: ' . DB::table('states')->count();
    echo '\nBranches: ' . DB::table('branches')->count();
    echo '\nUsers (synced): ' . App\Models\User::where('is_map_synced', true)->count();
    echo '\nUsers (total): ' . App\Models\User::count();
"

# Check specific user
php artisan tinker --execute="
    \$u = App\Models\User::where('username', 'naziha')->first();
    echo \$u->role . ' - ' . \$u->email . ' - Branch: ' . \$u->branch_id;
"

# Check branch with relationships
php artisan tinker --execute="
    \$b = App\Models\Branch::with('region', 'state')->first();
    echo \$b->branch_name . ' - ' . \$b->state->state_name . ' - ' . \$b->region->region_name;
"
```

---

## Troubleshooting

### Issue: "MAP database not found"
**Solution:** Ensure MAP database path is correct in `.env`:
```env
MAP_DATABASE_PATH=/path/to/FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3
```

### Issue: "Branch not found for user"
**Solution:** Run branch sync before user sync:
```bash
./sync-data.sh --branches-only
./sync-data.sh --users-only
```

### Issue: Email collisions
**Solution:** The `sync-data.sh` script handles this automatically by setting duplicate emails to NULL.

### Issue: Users losing admin role
**Solution:** The `sync-data.sh` script preserves admin roles. Manual admins won't be downgraded.

---

## Migration Order (CRITICAL!)

Always follow this order:

1. **Regions** ← No dependencies
2. **States** ← No dependencies  
3. **Branches** ← Depends on regions & states
4. **Users** ← Depends on branches

Both scripts follow this order automatically.

---

## Log Files

Location | Purpose
---------|--------
`storage/logs/laravel.log` | General Laravel logs
`storage/logs/map-user-sync.log` | User sync logs
`storage/logs/map-branch-sync.log` | Branch sync logs
`/var/log/eform-sync.log` | Cron job logs (if configured)

---

## Manual Commands (Advanced)

If you need more control, you can run individual commands:

```bash
# Regions
php artisan map:migrate-regions --dry-run
php artisan map:migrate-regions

# States
php artisan map:migrate-states --dry-run
php artisan map:migrate-states

# Branches
php artisan map:sync-branches --all --dry-run
php artisan map:sync-branches --all

# Users
php artisan map:sync-from-db --dry-run
php artisan map:sync-from-db
php artisan map:sync-from-db --username=naziha  # Specific user
```

---

## Support

For issues or questions:
1. Check logs: `tail -f storage/logs/laravel.log`
2. Run with `--dry-run` to preview
3. Verify MAP database connectivity
4. Check MAP_SSO_DEPLOYMENT.md for detailed documentation
