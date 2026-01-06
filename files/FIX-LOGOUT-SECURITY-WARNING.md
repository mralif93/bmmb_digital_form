# 🔒 FIX: Logout Security Warning

## Problem

When logging out, you see a security warning:
> "The information that you're about to submit is not secure"

## Root Cause

Your eForm is running on **HTTPS** but `MAP_LOGOUT_URL` in `.env` is set to **HTTP**.

---

## ✅ Solution

Update your `.env` file to use **HTTPS** for all MAP URLs:

```bash
# Edit .env
nano .env
```

**Change these lines from HTTP to HTTPS:**

```bash
# BEFORE (HTTP - causes security warning):
MAP_BASE_URL=http://127.0.0.1:8000
MAP_REDIRECT_URL=http://127.0.0.1:8000/redirect/eform/
MAP_LOGOUT_URL=http://127.0.0.1:8000/pengurusan/logout/

# AFTER (HTTPS - no warning):
MAP_BASE_URL=https://map.stg.muamalat.com.my
MAP_REDIRECT_URL=https://map.stg.muamalat.com.my/redirect/eform/
MAP_LOGOUT_URL=https://map.stg.muamalat.com.my/pengurusan/logout/
```

---

## 🔧 Quick Fix (Copy & Paste)

Run these commands on your server:

```bash
cd /path/to/eForm

# Update .env with production HTTPS URLs
sed -i 's|MAP_BASE_URL=http://127.0.0.1:8000|MAP_BASE_URL=https://map.stg.muamalat.com.my|g' .env
sed -i 's|MAP_REDIRECT_URL=http://127.0.0.1:8000/redirect/eform/|MAP_REDIRECT_URL=https://map.stg.muamalat.com.my/redirect/eform/|g' .env
sed -i 's|MAP_LOGOUT_URL=http://127.0.0.1:8000/pengurusan/logout/|MAP_LOGOUT_URL=https://map.stg.muamalat.com.my/pengurusan/logout/|g' .env

# Clear config cache
docker compose exec web php artisan config:clear

# Restart container
docker compose restart web
```

---

## ✅ Verify Fix

After updating, check the config:

```bash
docker compose exec web php artisan config:show map
```

Should show:
```
base_url ................................. https://map.stg.muamalat.com.my
redirect_url .................. https://map.stg.muamalat.com.my/redirect/eform/
logout_url .................. https://map.stg.muamalat.com.my/pengurusan/logout/
```

---

## 🎯 Production .env Template

Your production `.env` should have:

```bash
################################################################################
# MAP SSO Configuration - PRODUCTION
################################################################################

# MAP Base URL - Production URL with HTTPS
MAP_BASE_URL=https://map.stg.muamalat.com.my

# MAP SSO Shared Secret - Must match MAP's EFORM_SSO_SECRET
MAP_SSO_SECRET=your-production-secret-here

# SSO Token Expiry
MAP_SSO_TOKEN_EXPIRY=60

# MAP Redirect URL - HTTPS
MAP_REDIRECT_URL=https://map.stg.muamalat.com.my/redirect/eform/

# MAP Logout URL - HTTPS (fixes the security warning)
MAP_LOGOUT_URL=https://map.stg.muamalat.com.my/pengurusan/logout/

# MAP API Verify Endpoint
MAP_VERIFY_URL=https://map.stg.muamalat.com.my/api/eform/verify/
```

---

## 📋 Why This Happens

1. Your eForm runs on **HTTPS** (`https://map.stg.muamalat.com.my/eform/`)
2. Logout form submits to `/map/logout` route
3. Controller redirects to `MAP_LOGOUT_URL` 
4. But `MAP_LOGOUT_URL` is **HTTP** not HTTPS
5. Browser warns: "Submitting from HTTPS to HTTP is insecure!"

**Fix:** Use HTTPS for all MAP URLs in production.

---

## 🔄 After Fixing

1. ✅ No more security warning
2. ✅ Seamless logout redirect
3. ✅ Federated logout works (logs out from both eForm and MAP)

---

## 💡 Development vs Production

### Development (.env.example)
```bash
MAP_BASE_URL=http://127.0.0.1:8000
MAP_LOGOUT_URL=http://127.0.0.1:8000/pengurusan/logout/
```

### Production (.env)
```bash
MAP_BASE_URL=https://map.stg.muamalat.com.my
MAP_LOGOUT_URL=https://map.stg.muamalat.com.my/pengurusan/logout/
```

**Rule:** Always use HTTPS in production!

---

## ✅ Complete Fix Checklist

- [ ] Update `MAP_BASE_URL` to HTTPS
- [ ] Update `MAP_REDIRECT_URL` to HTTPS  
- [ ] Update `MAP_LOGOUT_URL` to HTTPS
- [ ] Run `php artisan config:clear`
- [ ] Restart container
- [ ] Test logout - no warning should appear
- [ ] Verify redirects to MAP properly

---

This will completely fix your logout security warning! 🔒
