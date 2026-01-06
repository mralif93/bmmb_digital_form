# Post-Deployment Commands Guide

## 🚀 After Running `docker-compose down` and `docker-compose up`

When you restart your Docker services, you **MUST** run these commands to fix the error you're seeing.

---

## ⚡ Quick Fix (Run This Now)

```bash
# Enter the container
docker compose exec web bash

# Inside container, run these commands:
php artisan config:clear
composer dump-autoload
php artisan cache:clear
php artisan view:clear
exit

# Restart the container
docker compose restart web
```

---

## 📋 Complete Post-Deployment Checklist

### **Option 1: Use the Automated Script** (Recommended)

```bash
# Make executable (first time only)
chmod +x post-deploy.sh

# Run the script
./post-deploy.sh
```

The script will automatically:
✅ Clear all caches
✅ Regenerate autoload
✅ Fix permissions
✅ Optionally run migrations

---

### **Option 2: Manual Commands**

If you prefer to run commands manually:

#### **1. Clear Configuration Cache** (CRITICAL)
```bash
docker compose exec web php artisan config:clear
```
**Why:** Config cache can be stale after restart

---

#### **2. Regenerate Composer Autoload** (CRITICAL)
```bash
docker compose exec web composer dump-autoload
```
**Why:** Fixes "Class not found" and "File not found" errors

---

#### **3. Clear Application Cache**
```bash
docker compose exec web php artisan cache:clear
```
**Why:** Removes cached data that may be outdated

---

#### **4. Clear View Cache**
```bash
docker compose exec web php artisan view:clear
```
**Why:** Recompiles Blade templates

---

#### **5. Clear Route Cache**
```bash
docker compose exec web php artisan route:clear
```
**Why:** Regenerates route definitions

---

#### **6. Fix Permissions** (If needed)
```bash
docker compose exec web chown -R www-data:www-data /var/www/html/storage
docker compose exec web chown -R www-data:www-data /var/www/html/bootstrap/cache
docker compose exec web chmod -R 775 /var/www/html/storage
docker compose exec web chmod -R 775 /var/www/html/bootstrap/cache
```
**Why:** Ensures Laravel can write to storage directories

---

#### **7. Restart Container**
```bash
docker compose restart web
```

---

## 🎯 Your Specific Error Fix

Based on your error: **"Failed opening required '/var/www/html/config/map.php'"**

Run these commands **in this order**:

```bash
# 1. Clear config cache (MOST IMPORTANT)
docker compose exec web php artisan config:clear

# 2. Verify the file exists
docker compose exec web ls -la /var/www/html/config/map.php

# 3. Regenerate autoload
docker compose exec web composer dump-autoload

# 4. Test if config loads
docker compose exec web php artisan tinker --execute="echo config('map.base_url');"

# 5. Restart
docker compose restart web
```

---

## 🔄 Standard Workflow

Every time you run `docker-compose down` and `docker-compose up`, follow this workflow:

```bash
# 1. Bring services up
docker compose up -d

# 2. Wait for containers to be ready
sleep 5

# 3. Run post-deployment script
./post-deploy.sh

# 4. Check logs
docker compose logs -f web
```

---

## 🐛 Troubleshooting

### Error: "config/map.php not found"

**Solution 1: Verify file exists**
```bash
# On host machine
ls -la config/map.php

# In container
docker compose exec web ls -la /var/www/html/config/map.php
```

**Solution 2: Check volume mounts**
```bash
# Verify volume in docker-compose.yml
docker compose config | grep -A 5 "volumes:"

# Should include:
# - .:/var/www/html
```

**Solution 3: Rebuild container**
```bash
docker compose down
docker compose build --no-cache
docker compose up -d
./post-deploy.sh
```

---

### Error: "Permission denied"

```bash
# Fix all permissions
docker compose exec web chown -R www-data:www-data /var/www/html
docker compose exec web chmod -R 755 /var/www/html
docker compose exec web chmod -R 775 /var/www/html/storage
docker compose exec web chmod -R 775 /var/www/html/bootstrap/cache
```

---

### Error: "Class not found"

```bash
docker compose exec web composer dump-autoload
docker compose restart web
```

---

## 📝 Production Deployment Workflow

For production servers:

```bash
# 1. Pull latest code
git pull origin main

# 2. Take services down
docker compose down

# 3. Rebuild (if Dockerfile changed)
docker compose build

# 4. Bring services up
docker compose up -d

# 5. Run post-deployment
./post-deploy.sh

# 6. Run migrations (if needed)
docker compose exec web php artisan migrate --force

# 7. Optimize for production
docker compose exec web php artisan config:cache
docker compose exec web php artisan route:cache
docker compose exec web php artisan view:cache

# 8. Verify
docker compose exec web php artisan config:show map
```

---

## ✅ Quick Verification

After running post-deployment commands, verify everything works:

```bash
# Check if config loads
docker compose exec web php artisan config:show map

# Should show:
#   sso_secret ...
#   base_url ...
#   database_path ...

# Check if autoload works
docker compose exec web php artisan list | grep map:

# Should show:
#   map:migrate-branches
#   map:migrate-regions
#   map:sync-from-db
#   etc.

# Check logs for errors
docker compose logs web --tail=50
```

---

## 🎬 Copy-Paste Commands

For your current error, just copy and paste this:

```bash
docker compose exec web php artisan config:clear && \
docker compose exec web composer dump-autoload && \
docker compose exec web php artisan cache:clear && \
docker compose restart web && \
echo "✓ Done! Check your application now."
```

---

## 📚 Related Documentation

- `post-deploy.sh` - Automated post-deployment script
- `SERVER-SETUP-GUIDE.md` - Complete server setup
- `ENV-VARIABLES-GUIDE.md` - Environment configuration

---

## 💡 Pro Tips

1. **Always clear config cache first** - Most errors are due to cached config
2. **Run `composer dump-autoload`** after adding new files
3. **Use the script** - `./post-deploy.sh` does everything for you
4. **Check logs** - `docker compose logs -f web` for real-time debugging
5. **Verify permissions** - Storage must be writable

---

## 🆘 Still Having Issues?

Run this diagnostic:

```bash
docker compose exec web bash -c "
    echo '=== Checking config/map.php ==='
    ls -la /var/www/html/config/map.php
    echo ''
    echo '=== Testing PHP syntax ==='
    php -l /var/www/html/config/map.php
    echo ''
    echo '=== Checking permissions ==='
    ls -la /var/www/html/config/
    echo ''
    echo '=== Testing config load ==='
    php artisan tinker --execute=\"print_r(config('map'));\"
"
```

This will show exactly what's wrong.
