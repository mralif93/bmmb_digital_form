# Complete MAP Data Migration Guide

## ✅ Migration Order (CRITICAL!)

**Must run in this exact order:**

1. **Regions** - Lookup table
2. **States** - Lookup table  
3. **Branches** - References regions & states
4. **Users** - References branches

## Commands

### 1. Migrate Regions (7 regions)

```bash
cd /Users/alif/Desktop/Project/GCP/bm-gc-repo-map-stg/eForm

# Dry run
php artisan map:migrate-regions --dry-run

# Actual
php artisan map:migrate-regions
```

**Data:** Central 1, Central 2, Northern, Southern, East Coast, Sabah, Sarawak

### 2. Migrate States (14+ states)

```bash
# Dry run
php artisan map:migrate-states --dry-run

# Actual
php artisan map:migrate-states
```

**Data:** Perak, Selangor, Kedah, Pulau Pinang, WP KL, Johor, Terengganu, etc.

### 3. Migrate Branches

```bash
# Dry run
php artisan map:migrate-branches --dry-run

# Actual
php artisan map:migrate-branches
```

### 4. Migrate All Users

```bash
# Dry run
php artisan map:migrate-users --dry-run

# Actual (all users)
php artisan map:migrate-users --batch=100

# Or only HQ/BM/CFE
php artisan map:migrate-users --filter=1,2,3 --batch=100
```

## All-in-One Script

```bash
#!/bin/bash
cd /Users/alif/Desktop/Project/GCP/bm-gc-repo-map-stg/eForm

echo "=== MAP Data Migration ==="
echo ""

# Step 1
echo "1/4: Migrating regions..."
php artisan map:migrate-regions --force
echo ""

# Step 2
echo "2/4: Migrating states..."
php artisan map:migrate-states --force
echo ""

# Step 3
echo "3/4: Migrating branches..."
php artisan map:migrate-branches --force
echo ""

# Step 4
echo "4/4: Migrating users..."
php artisan map:migrate-users --batch=100 --force
echo ""

echo "=== Migration Complete ==="
```

Save as `migrate-all.sh` and run:
```bash
chmod +x migrate-all.sh
./migrate-all.sh
```

## What Gets Migrated

### Regions Table
- ID (1-7)
- Name (Central 1, Northern, etc.)
- Links (credit check URLs)

### States Table
- ID
- Name (Perak, Selangor, etc.)

### Branches Table
- ID
- Branch name
- Weekend start day
- TI agent code
- Address, Email
- **State ID** ← References states
- **Region ID** ← References regions

### Users Table
- MAP user ID
- Username, Email, Name
- Position → mapped to role
- **Branch ID** ← References branches

## Verification

```bash
php artisan tinker

# Check regions
>>> DB::table('regions')->count()

# Check states
>>> DB::table('states')->count()

# Check branches with relationships
>>> Branch::with('region', 'state')->first()

# Check users with branch
>>> User::with('branch')->where('is_map_synced', true)->first()
```

## Default Behavior

- **Regions**: Migrates ALL (7 regions)
- **States**: Migrates ALL (14+ states)
- **Branches**: Migrates ALL
- **Users**: Migrates ALL (use `--filter` to limit)
