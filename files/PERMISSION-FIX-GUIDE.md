# 🔧 Complete Fix for eForm Permission Issues

## ✅ THE SOLUTION

The problem is **file permissions** in the Docker container. Use this script to fix everything:

```bash
chmod +x fix-permissions.sh
./fix-permissions.sh
```

---

## 📋 What the Script Does

The script runs these commands **in the correct order**:

### 1. **Fix Ownership**
```bash
sudo docker exec eform_web chown -R www-data:www-data /var/www/html
```
**Why:** All files must be owned by `www-data` (the web server user)

### 2. **Fix Directory Permissions (755)**
```bash
sudo docker exec eform_web find /var/www/html -type d -exec chmod 755 {} \;
```
**Why:** Directories need `rwxr-xr-x` (read, write, execute for owner; read, execute for others)

### 3. **Fix File Permissions (644)**
```bash
sudo docker exec eform_web find /var/www/html -type f -exec chmod 644 {} \;
```
**Why:** Files need `rw-r--r--` (read, write for owner; read-only for others)

### 4. **Fix Storage & Cache (775)**
```bash
sudo docker exec eform_web chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
```
**Why:** Storage and cache need write permissions for the web server

### 5. **Clear All Caches**
```bash
sudo docker exec eform_web php artisan config:clear
sudo docker exec eform_web php artisan cache:clear
sudo docker exec eform_web php artisan view:clear
sudo docker exec eform_web composer dump-autoload
```
**Why:** Remove cached data that may be causing errors

### 6. **Restart Container**
```bash
docker compose restart web
```
**Why:** Apply all changes

---

## ⚡ Quick Manual Fix

If you want to run the commands manually:

```bash
# Fix permissions
sudo docker exec eform_web chown -R www-data:www-data /var/www/html
sudo docker exec eform_web find /var/www/html -type d -exec chmod 755 {} \;
sudo docker exec eform_web find /var/www/html -type f -exec chmod 644 {} \;
sudo docker exec eform_web chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear caches
sudo docker exec eform_web php artisan config:clear
sudo docker exec eform_web composer dump-autoload
sudo docker exec eform_web php artisan cache:clear
sudo docker exec eform_web php artisan view:clear

# Restart
docker compose restart web

# Verify
sudo docker exec eform_web php artisan config:show map
```

---

## 🎯 After Running the Fix

### Verify It Worked

```bash
# Check if config loads
sudo docker exec eform_web php artisan config:show map

# Should show all MAP configuration:
#   sso_secret ....
#   base_url ......
#   database_path .
```

### Check Permissions

```bash
# Should show: www-data www-data ... map.php
sudo docker exec eform_web ls -la /var/www/html/config/map.php
```

### Test the Application

Visit your eForm URL in the browser. The error should be gone!

---

## 📊 Permission Breakdown

| Location | Owner | Group | Permissions | Meaning |
|----------|-------|-------|-------------|---------|
| All files | www-data | www-data | 644 | `-rw-r--r--` |
| All directories | www-data | www-data | 755 | `drwxr-xr-x` |
| `storage/` | www-data | www-data | 775 | `drwxrwxr-x` |
| `bootstrap/cache/` | www-data | www-data | 775 | `drwxrwxr-x` |

---

## 🔄 When to Run This

Run `./fix-permissions.sh` after:

✅ `docker-compose down` and `up`  
✅ Pulling new code from git  
✅ Adding new files to the project  
✅ Seeing "Permission denied" errors  
✅ Seeing "config/map.php not found" errors  
✅ Laravel showing 500 errors

---

## 🚀 Add to Your Deployment Workflow

Update your deployment script:

```bash
#!/bin/bash

# Pull latest code
git pull origin main

# Rebuild containers
docker compose down
docker compose build
docker compose up -d

# FIX PERMISSIONS (ADD THIS)
./fix-permissions.sh

# Done!
echo "Deployment complete!"
```

---

## 💡 Why This Problem Happens

When you:
1. Build Docker images
2. Copy files into the container
3. Mount volumes from the host

The files may have **incorrect ownership/permissions** because:
- Host user (e.g., `alif`) owns the files
- Container needs `www-data` to own them
- Docker doesn't automatically fix this

**The fix:** Explicitly set correct permissions after every deployment.

---

## 📝 Automated Fix in Dockerfile (Future Prevention)

Add this to your `Dockerfile` to automatically fix permissions on build:

```dockerfile
# ... existing Dockerfile content ...

# Fix permissions
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["php-fpm"]
```

But you'll still need to run the script after mounting volumes!

---

## ✅ Success Checklist

After running `./fix-permissions.sh`, verify:

- [ ] Script completed without errors
- [ ] `php artisan config:show map` works
- [ ] Application loads in browser without errors
- [ ] No "Permission denied" in logs
- [ ] `ls -la /var/www/html/config/map.php` shows `www-data`

---

## 🆘 Troubleshooting

### Script says "Permission denied"
**Solution:** Add `sudo` to the script commands or run as root

### Config still not loading
**Solution:** Check the file actually exists:
```bash
sudo docker exec eform_web ls -la /var/www/html/config/map.php
```

### Container won't start
**Solution:** Check logs:
```bash
docker compose logs web --tail=100
```

---

## 🎬 One-Liner Complete Fix

Copy and paste this entire command:

```bash
sudo docker exec eform_web chown -R www-data:www-data /var/www/html && \
sudo docker exec eform_web find /var/www/html -type d -exec chmod 755 {} \; && \
sudo docker exec eform_web find /var/www/html -type f -exec chmod 644 {} \; && \
sudo docker exec eform_web chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && \
sudo docker exec eform_web php artisan config:clear && \
sudo docker exec eform_web composer dump-autoload && \
sudo docker exec eform_web php artisan cache:clear && \
docker compose restart web && \
echo "✅ Fix complete! Waiting 5 seconds..." && \
sleep 5 && \
sudo docker exec eform_web php artisan config:show map
```

---

## 📚 Related Documentation

- `fix-permissions.sh` - Automated fix script
- `post-deploy.sh` - Post-deployment commands
- `POST-DEPLOYMENT-GUIDE.md` - Detailed deployment guide

---

This should completely solve your `config/map.php` error! 🎉
