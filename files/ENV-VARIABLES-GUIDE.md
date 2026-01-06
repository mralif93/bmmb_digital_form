# ✅ Complete .env Configuration Guide

## All MAP Configuration Variables Explained

I've configured all the settings from `config/map.php` in your `.env` file. Here's what each one does:

---

## 📋 Current .env MAP Configuration

### 1. **MAP_BASE_URL**
```bash
# Development
MAP_BASE_URL=http://127.0.0.1:8000

# Production - UPDATE THIS
MAP_BASE_URL=https://map.muamalat.com.my
```
**What it does:** Main URL of your MAP application (Django)

---

### 2. **MAP_SSO_SECRET** ⚠️ CRITICAL
```bash
# Development (test secret)
MAP_SSO_SECRET=map-eform-sso-shared-secret-2024

# Production - MUST CHANGE
MAP_SSO_SECRET=your-secure-random-secret-here
```
**What it does:** Shared secret for SSO token signing/verification  
**IMPORTANT:** This **MUST match** the `EFORM_SSO_SECRET` in MAP's `.env`

**Generate secure secret:**
```bash
openssl rand -base64 32
```

---

### 3. **MAP_SSO_TOKEN_EXPIRY**
```bash
MAP_SSO_TOKEN_EXPIRY=60
```
**What it does:** How long SSO tokens are valid (in seconds)  
**Default:** 60 seconds (1 minute)  
**Increase to:** 300 (5 minutes) if users get "token expired" errors

---

### 4. **MAP_REDIRECT_URL**
```bash
# Development
MAP_REDIRECT_URL=http://127.0.0.1:8000/redirect/eform/

# Production - UPDATE THIS
MAP_REDIRECT_URL=https://map.muamalat.com.my/redirect/eform/
```
**What it does:** Where MAP redirects users after successful login

---

### 5. **MAP_LOGOUT_URL**
```bash
# Development
MAP_LOGOUT_URL=http://127.0.0.1:8000/pengurusan/logout/

# Production - UPDATE THIS
MAP_LOGOUT_URL=https://map.muamalat.com.my/pengurusan/logout/
```
**What it does:** Federated logout - when users logout from eForm, they also logout from MAP

---

### 6. **MAP_VERIFY_URL**
```bash
MAP_VERIFY_URL=https://map.muamalat.com.my/api/eform/verify/
```
**What it does:** API endpoint for SSO token verification (production only)  
**Note:** Usually keep as production URL even in development

---

### 7. **MAP_DATABASE_PATH** ⚠️ CRITICAL FOR SYNC
```bash
# Development
MAP_DATABASE_PATH=../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3

# Production - MUST UPDATE based on your server
# Option 1: Docker
MAP_DATABASE_PATH=/map_db/db.sqlite3

# Option 2: Direct filesystem
MAP_DATABASE_PATH=/opt/FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3

# Option 3: Custom path
MAP_DATABASE_PATH=/your/custom/path/to/db.sqlite3
```
**What it does:** Path to MAP's SQLite database for syncing users/branches  
**REQUIRED FOR:** Migration and sync scripts to work

---

### 8. **MAP_DB_PATH**
```bash
# Development
MAP_DB_PATH=./database

# Production
MAP_DB_PATH=/opt/eform/eform_db
```
**What it does:** Path for eForm's own database storage (Docker volume mounting)

---

## 🚀 Production Setup Steps

### Step 1: Update .env File on Server

```bash
# SSH to your production server
ssh user@your-server

# Navigate to eForm directory
cd /path/to/eForm

# Edit .env
nano .env
```

### Step 2: Update These Values

**Required Changes:**
```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://eform.muamalat.com.my

# MAP URLs (replace all http://127.0.0.1:8000 with production URL)
MAP_BASE_URL=https://map.muamalat.com.my
MAP_REDIRECT_URL=https://map.muamalat.com.my/redirect/eform/
MAP_LOGOUT_URL=https://map.muamalat.com.my/pengurusan/logout/

# Security - Generate new secret
MAP_SSO_SECRET=your-new-secure-secret-here

# Database Path - Find on your server
MAP_DATABASE_PATH=/opt/FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3
```

### Step 3: Generate Secrets

```bash
# Generate APP_KEY
php artisan key:generate

# Generate SSO Secret (copy to MAP_SSO_SECRET)
openssl rand -base64 32
```

### Step 4: Update MAP's .env

In MAP application's `.env`, add/update:
```bash
EFORM_SSO_SECRET=same-secret-as-eForm-MAP_SSO_SECRET
EFORM_BASE_URL=https://eform.muamalat.com.my
```

### Step 5: Find MAP Database Path

```bash
# Search for database
find /opt -name "db.sqlite3" 2>/dev/null | grep -i financ

# Or check MAP's settings
ls -la /opt/FinancingApp/FinancingApp_Backend/FinancingApp/
```

### Step 6: Verify

```bash
./verify-db-path.sh
```

---

## 📊 Configuration Summary

### Variables You MUST Change for Production:

| Variable | Change Required | Priority |
|----------|----------------|----------|
| `APP_ENV` | Yes → `production` | 🔴 High |
| `APP_DEBUG` | Yes → `false` | 🔴 High |
| `APP_URL` | Yes → Production URL | 🔴 High |
| `MAP_BASE_URL` | Yes → Production URL | 🔴 High |
| `MAP_SSO_SECRET` | Yes → Secure secret | 🔴 Critical |
| `MAP_REDIRECT_URL` | Yes → Production URL | 🔴 High |
| `MAP_LOGOUT_URL` | Yes → Production URL | 🟡 Medium |
| `MAP_DATABASE_PATH` | Yes → Server path | 🔴 Critical |
| `MAP_VERIFY_URL` | Maybe keep as is | 🟢 Low |
| `MAP_SSO_TOKEN_EXPIRY` | Optional | 🟢 Low |
| `MAP_DB_PATH` | Maybe → `/opt/eform/eform_db` | 🟡 Medium |

---

## ✅ Checklist

Before going to production:

- [ ] All URLs updated from `http://127.0.0.1:8000` to production domain
- [ ] `MAP_SSO_SECRET` generated and matches MAP's `EFORM_SSO_SECRET`
- [ ] `MAP_DATABASE_PATH` points to correct database file
- [ ] Database file is readable: `ls -la $(echo $MAP_DATABASE_PATH)`
- [ ] Ran `./verify-db-path.sh` successfully
- [ ] `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Generated new `APP_KEY` with `php artisan key:generate`

---

## 🔍 Quick Verification Commands

```bash
# Check all MAP config values
php artisan tinker --execute="
    echo 'Base URL: ' . config('map.base_url') . PHP_EOL;
    echo 'SSO Secret: ' . substr(config('map.sso_secret'), 0, 10) . '...' . PHP_EOL;
    echo 'Token Expiry: ' . config('map.token_expiry') . ' sec' . PHP_EOL;
    echo 'Database: ' . config('map.database_path') . PHP_EOL;
    echo 'DB Exists: ' . (file_exists(config('map.database_path')) ? 'YES' : 'NO') . PHP_EOL;
"

# Or use the verification script
./verify-db-path.sh
```

---

## 📁 Files Created

1. **`.env`** - Your main environment file (updated with all MAP variables)
2. **`.env.example`** - Template for new installations
3. **`.env.production.template`** - Production-specific template with examples

---

## Need Help?

- See `.env.production.template` for detailed production example
- Run `./verify-db-path.sh` for diagnostics
- Check `ENV-PRODUCTION-SETUP.md` for troubleshooting
- See `SERVER-SETUP-GUIDE.md` for complete setup steps
