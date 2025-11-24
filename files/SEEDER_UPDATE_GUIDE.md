# Seeder Update Guide

## Overview

The `FormManagementSeeder` reads form structures from JSON files in `database/seeders/exports/`:
- `srf_form_export.json` - Service Request Form
- `dar_form_export.json` - Data Access Request Form
- `dcr_form_export.json` - Data Correction Request Form

These JSON files contain the complete form structure including all sections, fields, settings, and configurations.

## Updating the Seeder

### Method 1: Using the Export Script (Recommended)

1. Make your changes to the form in the admin panel
2. Run the export script:
   ```bash
   php database/seeders/export_srf_form.php
   # or for DAR/DCR:
   php database/seeders/export_dar_form.php
   php database/seeders/export_dcr_form.php
   ```
3. Verify the export was successful (files are saved to `database/seeders/exports/`)
4. Test the seeder:
   ```bash
   php artisan migrate:fresh
   php artisan db:seed --class=FormManagementSeeder
   ```

### Method 2: Using Artisan Tinker

1. Make your changes to the form in the admin panel
2. Open tinker:
   ```bash
   php artisan tinker
   ```
3. Run the export code:
   ```php
   $form = \App\Models\Form::where('slug', 'srf')->with(['sections.fields' => function($q) { $q->orderBy('sort_order'); }])->first();
   // ... (use the export code from export_srf_form.php)
   ```

### Method 3: Manual JSON Update

1. Edit the JSON file directly in `database/seeders/exports/`:
   - `srf_form_export.json`
   - `dar_form_export.json`
   - `dcr_form_export.json`
2. Ensure JSON syntax is valid
3. Test the seeder

## What Gets Exported

The export includes:

- **Form metadata**: name, slug, description, status, settings
- **Sections**: key, title, description, sort order, active status
- **Fields**: 
  - Basic info: name, label, type, placeholder
  - Content: description, help text
  - Configuration: required, active, sort order, grid column
  - Advanced: conditional logic, validation rules, field options, field settings
  - **Field settings** include:
    - `description_position` (top/bottom)
    - For repeater fields: `columns`, `min_rows`, `max_rows`, `add_button_text`, `remove_button_text`

## Supported Field Types

All field types are supported:
- `text`, `email`, `phone`, `number`
- `textarea`
- `select`, `radio`, `checkbox`, `multiselect`
- `date`, `time`, `datetime`
- `file`
- `signature`
- `currency`
- `repeater` (with column configurations)

## Verifying the Seeder

After running the seeder, verify the structure:

```bash
php artisan tinker
```

```php
$form = \App\Models\Form::where('slug', 'srf')->with(['sections.fields'])->first();
echo "Sections: " . $form->sections->count() . "\n";
echo "Total fields: " . $form->sections->sum(fn($s) => $s->fields->count()) . "\n";
```

## Notes

- The seeder uses `updateOrCreate()` to avoid duplicates
- Field settings (including repeater configurations) are preserved
- Conditional logic is fully supported
- All validation rules are maintained
- The JSON file should be committed to version control

## Troubleshooting

### JSON Parse Error
- Check JSON syntax using a JSON validator
- Ensure all strings are properly escaped
- Verify no trailing commas

### Missing Fields
- Verify the export script ran successfully
- Check that all fields are active in the database
- Ensure sections are properly linked

### Repeater Fields Not Working
- Verify `field_settings` contains `columns` array
- Check that column definitions include `name`, `label`, and `type`
- Ensure `min_rows` and `max_rows` are set correctly

