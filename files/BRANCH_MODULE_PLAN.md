# Branch Module Implementation Plan

## Overview
This document outlines the implementation plan for the Branch Management module with CRUD functionality.

## Database Structure

### 1. Migration: `create_branches_table.php`
```php
Schema::create('branches', function (Blueprint $table) {
    $table->id();
    $table->string('branch_name'); // "ALAM DAMAI"
    $table->enum('weekend_start_day', ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'])->default('SATURDAY');
    $table->string('ti_agent_code'); // "FN12984"
    $table->text('address'); // Full address
    $table->string('email'); // "sgar@muamalat.com.my"
    $table->string('state'); // "Wilayah Persekutuan Kuala Lumpur"
    $table->string('region'); // "Central 1"
    $table->timestamps();
    
    $table->index('branch_name');
    $table->index('state');
    $table->index('region');
});
```

// Note: State and Region are now simple string fields in the branches table

## Model Structure

### Branch Model (`app/Models/Branch.php`)
```php
class Branch extends Model
{
    protected $fillable = [
        'branch_name',
        'weekend_start_day',
        'ti_agent_code',
        'address',
        'email',
        'state',
        'region',
    ];
}
```

## Controller Structure

### BranchController (`app/Http/Controllers/Admin/BranchController.php`)
- `index()` - List all branches with pagination
- `create()` - Show create form
- `store()` - Store new branch
- `show($id)` - View branch details
- `edit($id)` - Show edit form
- `update($id)` - Update branch
- `destroy($id)` - Delete branch

Note: State and Region are now simple text input fields, no separate controllers needed.

## Routes Structure

```php
// Branch Management
Route::resource('branches', BranchController::class);
```

## Views Structure

### Branch Views
- `resources/views/admin/branches/index.blade.php` - List all branches
- `resources/views/admin/branches/create.blade.php` - Create form
- `resources/views/admin/branches/edit.blade.php` - Edit form
- `resources/views/admin/branches/show.blade.php` - View details

### Form Fields (as shown in image):
1. **Branch** - Text input
2. **Weekend Start Day** - Dropdown (MONDAY-SUNDAY)
3. **TI Agent Code** - Text input
4. **Address** - Textarea (resizable)
5. **Email** - Email input
6. **State** - Text input field
7. **Region** - Text input field

## Sidebar Addition

Add "Branches" menu item to sidebar (after Users or Forms):
```php
<a href="{{ route('admin.branches.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
    <i class='bx bx-building mr-3 text-base'></i>
    <span class="font-medium">Branches</span>
</a>
```

## Features

1. **Full CRUD** for branches
2. **Form Validation**:
   - Branch name: required
   - Weekend start day: required, enum
   - TI Agent Code: required, unique
   - Address: required
   - Email: required, email format, unique
   - State: required
   - Region: required
5. **Search/Filter** - Optional enhancement for branch list
6. **Responsive Design** - Mobile-friendly forms

## File Structure

```
database/migrations/
  - YYYY_MM_DD_create_branches_table.php

app/Models/
  - Branch.php

app/Http/Controllers/Admin/
  - BranchController.php

resources/views/admin/branches/
  - index.blade.php
  - create.blade.php
  - edit.blade.php
  - show.blade.php
```

## Implementation Order

1. Create migration (branches)
2. Create model (Branch)
3. Create controller (BranchController)
4. Create routes
5. Create views
6. Update sidebar
7. Test CRUD operations

