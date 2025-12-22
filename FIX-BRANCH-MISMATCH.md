# 🔍 Branch Mismatch Issue - User has Wrong Branch

## Problem

User `mralif93` shows different branch in MAP vs eForm:
- **MAP:** Jalan Melaka
- **eForm:** JALAN TUANKU ABD RAHMAN

## Root Cause

This happens when **branch IDs don't match** between MAP and eForm databases. The sync uses this resolution order:

1. **By `ti_agent_code`** (branch code) - Most reliable
2. **By `branch_name`** (exact match) - Case-sensitive fallback
3. **By `branch_id`** - Last resort (assumes 1:1 ID mapping)

---

## 🔍 Diagnose the Issue

Run this diagnostic script:

```bash
# Make executable
chmod +x diagnose-branch.sh

# Check specific user
./diagnose-branch.sh mralif93
```

This will show:
1. What branch the user has in eForm
2. What branch the user should have from MAP
3. How the branch resolution logic is working
4. All branches with similar names

---

## 📋 Common Causes

### **Cause 1: Branch IDs Don't Match**

**Example:**
- MAP: Branch ID `5` = "Jalan Melaka"
- eForm: Branch ID `5` = "JALAN TUANKU ABD RAHMAN"

**Why it happens:**
- Branches were created in different order
- MAP database was rebuilt with new IDs
- Manual branch creation in eForm

---

### **Cause 2: Missing or NULL ti_agent_code**

If branches don't have `ti_agent_code` set, the sync falls back to ID matching which can be wrong.

**Check:**
```bash
docker compose exec web php artisan tinker --execute="
    App\Models\Branch::whereNull('ti_agent_code')
        ->orWhere('ti_agent_code', '')
        ->get(['id', 'branch_name', 'ti_agent_code']);
"
```

---

### **Cause 3: Branch Name Mismatch**

Branch names might be different in MAP vs eForm (case, spacing, etc.):
- MAP: "Jalan Melaka"
- eForm: "JALAN MELAKA" (uppercase)

---

## ✅ Solution 1: Use Branches Sync with Correct IDs

The best solution is to ensure branches are synced **with matching IDs** from MAP:

```bash
# Step 1: Delete all branches and users
./reset-migration.sh

# Step 2: This will sync branches WITH their original MAP IDs
# The sync command preserves MAP branch IDs
```

---

## ✅ Solution 2: Fix Individual User

If only a few users have wrong branches:

```bash
docker compose exec web php artisan tinker
```

```php
// Find user
$user = App\Models\User::where('username', 'mralif93')->first();

// Find correct branch by name
$correctBranch = App\Models\Branch::where('branch_name', 'like', '%Jalan Melaka%')->first();

// Update user
$user->branch_id = $correctBranch->id;
$user->save();

echo "Updated: {$user->username} -> {$correctBranch->branch_name}";
```

---

## ✅ Solution 3: Fix All Users (Re-sync)

Re-run the user sync to fix all branch assignments:

```bash
# This will update all user -> branch relationships
docker compose exec web php artisan map:sync-from-db
```

The sync command will:
1. Look up each user's branch from MAP
2. Resolve the correct eForm branch using:
   - Branch code (if available)
   - Branch name (exact match)
   - Branch ID (if IDs match)
3. Update the user's `branch_id`

---

## 🔧 Fix Branch Codes (Recommended)

Ensure all branches have `ti_agent_code` populated:

```bash
docker compose exec web php artisan tinker
```

```php
// Check which branches are missing codes
$missing = App\Models\Branch::whereNull('ti_agent_code')
    ->orWhere('ti_agent_code', '')
    ->get();

echo "Branches without ti_agent_code: " . $missing->count();

foreach ($missing as $branch) {
    echo "ID: {$branch->id} | Name: {$branch->branch_name}\n";
}
```

If branches are missing codes, re-sync from MAP:

```bash
docker compose exec web php artisan map:sync-branches --all
```

---

## 📊 Verification

After fixing, verify the user has the correct branch:

```bash
docker compose exec web php artisan tinker --execute="
\$user = App\Models\User::where('username', 'mralif93')->first();
echo 'User: ' . \$user->full_name . PHP_EOL;
echo 'Branch: ' . (\$user->branch ? \$user->branch->branch_name : 'NULL') . PHP_EOL;
"
```

---

## 🎯 Prevention

To prevent this issue in the future:

1. ✅ **Always sync branches BEFORE users**
2. ✅ **Use `reset-migration.sh`** to ensure clean data
3. ✅ **Verify branch codes** are populated
4. ✅ **Don't manually create branches** in eForm

---

## 📝 Branch Resolution Logic (from sync code)

```php
private function resolveBranchId($mapBranchId, $branchCode, $branchName): ?int
{
    // 1. Try by branch code (most reliable)
    if ($branchCode) {
        $branch = Branch::where('ti_agent_code', $branchCode)->first();
        if ($branch) return $branch->id;
    }

    // 2. Try by branch name (exact match)
    if ($branchName) {
        $branch = Branch::where('branch_name', $branchName)->first();
        if ($branch) return $branch->id;
    }

    // 3. Try by MAP ID (assumes 1:1 mapping)
    if ($mapBranchId && Branch::where('id', $mapBranchId)->exists()) {
        return $mapBranchId;
    }

    return null;
}
```

**Priority:**
1. 🥇 `ti_agent_code` - Most reliable
2. 🥈 `branch_name` - Fallback
3. 🥉 `branch_id` - Last resort

---

## 🔍 Example Diagnostic Output

```bash
./diagnose-branch.sh mralif93

Step 1: Check user in eForm database...
✓ User found in eForm:
  Name: MUHAMMAD ALIF BIN RAMLI
  Branch ID: 8
  Branch Name: JALAN TUANKU ABD RAHMAN
  Branch Code: KL01

Step 2: Check user in MAP database...
✓ User found in MAP:
  Name: MUHAMMAD ALIF BIN RAMLI
  Branch ID (MAP): 5
  Branch Name (MAP): Jalan Melaka
  Branch Code (MAP): ML01

Step 3: Check branch resolution...
Resolving branch using sync logic:
  MAP branch_id: 5
  MAP branch_code: ML01
  MAP branch_name: Jalan Melaka

  ✓ Found by CODE: Jalan Melaka (ID: 12)  ← Should use this!
  ✗ Not found by NAME
  ✓ Found by ID: JALAN TUANKU ABD RAHMAN (ID: 5)  ← Wrong!

Step 4: List all branches with similar names...
Branches containing "JALAN" or "MELAKA":
  ID: 5  | Code: NULL        | Name: JALAN TUANKU ABD RAHMAN
  ID: 12 | Code: ML01        | Name: Jalan Melaka
```

**Diagnosis:** Branch ID `5` in eForm doesn't match MAP's branch ID `5`. The correct branch is ID `12` (found by code `ML01`).

---

## 🆘 Quick Fix Commands

```bash
# Fix single user
docker compose exec web php artisan tinker --execute="
\$user = App\Models\User::where('username', 'mralif93')->first();
\$branch = App\Models\Branch::where('ti_agent_code', 'ML01')->first();
if (\$user && \$branch) {
    \$user->branch_id = \$branch->id;
    \$user->save();
    echo 'Fixed: ' . \$user->username . ' -> ' . \$branch->branch_name;
}
"

# Or re-sync from MAP
docker compose exec web php artisan map:sync-from-db --username=mralif93
```

---

This should help you identify and fix the branch mismatch! 🎯
