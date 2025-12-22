# 🔄 Reset & Re-Migrate MAP Data Guide

## Purpose

This script completely **resets and re-migrates** all MAP data into eForm. Use it for:

✅ **Recovery** - After data corruption  
✅ **Reset** - Get latest data from MAP  
✅ **Testing** - Test migration process  
✅ **Troubleshooting** - Fix relationship issues  

---

## ⚠️ What It Does

### **DELETES:**
- ❌ All regions
- ❌ All states
- ❌ All branches
- ❌ All MAP-synced users (preserves manually created users)

### **RE-CREATES:**
- ✅ Fresh regions from MAP
- ✅ Fresh states from MAP
- ✅ Fresh branches from MAP
- ✅ Fresh users from MAP

---

## 🚀 Usage

### **Interactive Mode (Recommended)**

```bash
chmod +x reset-migration.sh
./reset-migration.sh
```

You'll be prompted to type `RESET` to confirm.

### **Force Mode (No Confirmation)**

```bash
./reset-migration.sh --force
```

---

## 📋 What Happens

1. **Backup** - Automatic SQL backup
2. **Delete** - Remove old MAP data  
3. **Verify** - Confirm cleanup
4. **Re-Migrate** - Fresh data from MAP
5. **Verify** - Check results

---

## 🔒 Safety Features

✅ **Backup before deletion**  
✅ **Confirmation required**  
✅ **Preserves manual users**  
✅ **Exit on error**  
✅ **Verification checks**  

---

## 💾 Backups

Auto-saved to: `backups/pre_reset_YYYYMMDD_HHMMSS.sql`

**Restore:**
```bash
sqlite3 database/database.sqlite < backups/pre_reset_20241222_211500.sql
```

---

## ✅ After Reset Checklist

- [ ] Regions: 7
- [ ] States: 14+
- [ ] Branches: Match MAP
- [ ] Users: Match MAP
- [ ] Test SSO login
- [ ] Check relationships

---

This is your **emergency reset button**! 🆘
