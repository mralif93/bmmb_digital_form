# Server Deployment Guide - Shell Script Permissions

This guide explains how to ensure all shell scripts have executable permissions on the SERVER (outside Docker).

## Quick Start

After deploying code to the server, run:

```bash
./set-permissions.sh
```

This will automatically make ALL `.sh` files executable.

---

## Complete Setup Instructions

### 1. **Initial Setup (First Time Only)**

After cloning the repository on the server:

```bash
# Make the permission script executable first
chmod +x set-permissions.sh

# Run it to set permissions for all other scripts
./set-permissions.sh
```

### 2. **Automatic Setup with Git Hooks (Recommended)**

Install the git hook to automatically set permissions after every `git pull`:

```bash
# Copy the hook template to .git/hooks
cp git-hooks/post-merge .git/hooks/post-merge

# Make the hook executable
chmod +x .git/hooks/post-merge
```

**Now, every time you run `git pull`, permissions will be set automatically!**

---

## Manual Permission Setting

If you need to manually set permissions for all shell scripts:

```bash
# Method 1: Use the helper script
./set-permissions.sh

# Method 2: Direct command
find . -type f -name "*.sh" ! -path "./vendor/*" -exec chmod +x {} \;

# Method 3: Set individually
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

---

## Deployment Workflow

### **Standard Deployment:**

```bash
# 1. SSH to server
ssh user@server

# 2. Navigate to project directory
cd /opt/eform  # or your installation path

# 3. Pull latest code
git pull origin main

# 4. Set permissions (if git hook not installed)
./set-permissions.sh

# 5. Rebuild Docker containers
docker-compose build
docker-compose up -d

# 6. Run post-deployment tasks
./post-deploy.sh
```

### **With Git Hook Installed:**

```bash
# 1. SSH to server
ssh user@server

# 2. Navigate to project directory
cd /opt/eform

# 3. Pull latest code (permissions set automatically)
git pull origin main

# 4. Rebuild Docker containers
docker-compose build
docker-compose up -d

# 5. Run post-deployment tasks
./post-deploy.sh
```

---

## Troubleshooting

### **Problem: "Permission denied" when running scripts**

**Cause:** Scripts don't have executable permissions

**Solution:**
```bash
./set-permissions.sh
```

### **Problem: `set-permissions.sh` itself not executable**

**Solution:**
```bash
chmod +x set-permissions.sh
./set-permissions.sh
```

### **Problem: Git hook not working**

**Check if hook is installed:**
```bash
ls -la .git/hooks/post-merge
```

**If missing, install it:**
```bash
cp git-hooks/post-merge .git/hooks/post-merge
chmod +x .git/hooks/post-merge
```

---

## Environment-Specific Notes

### **Local Development**
- Scripts should already have executable permissions in the repository
- If not, run `./set-permissions.sh` once

### **SIT / Staging / Production Servers**
- **IMPORTANT:** Always run `./set-permissions.sh` after initial deployment
- **RECOMMENDED:** Install the git hook for automatic permission management
- Scripts inside Docker containers get permissions automatically (via Dockerfile)

---

## What Happens Where?

| Location | Automatic? | How? |
|----------|-----------|------|
| **Inside Docker** | ✅ Yes | Dockerfile runs `find ... -exec chmod +x` during build |
| **Server Filesystem** | ⚠️ Manual | Run `./set-permissions.sh` after git pull |
| **Server with Git Hook** | ✅ Yes | Hook runs `set-permissions.sh` after git pull |

---

## Files Reference

- **`set-permissions.sh`** - Main script to set all shell script permissions (server-side)
- **`git-hooks/post-merge`** - Template for git hook (auto-runs after git pull)
- **`Dockerfile`** (line 49-50) - Auto-sets permissions inside Docker containers

---

## Summary

✅ **Inside Docker:** Permissions set automatically during build  
✅ **Server (with hook):** Permissions set automatically after git pull  
⚠️ **Server (without hook):** Run `./set-permissions.sh` manually after git pull

---

## Quick Command Reference

```bash
# Set all permissions manually
./set-permissions.sh

# Install git hook (one-time setup)
cp git-hooks/post-merge .git/hooks/post-merge && chmod +x .git/hooks/post-merge

# Verify permissions
ls -la *.sh

# Test a script
./deploy.sh --help  # Should work if permissions are correct
```
