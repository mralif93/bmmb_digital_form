# Repeater Field Type Guide

## Overview
The **Repeater Table** field type allows users to add multiple rows of data in a table format. Perfect for capturing multiple account entries, addresses, or any repeating data structure.

## How to Create a Repeater Field

### Step 1: Add Field in Admin Form Builder
1. Go to Admin → Form Builder
2. Click "Add Field"
3. Select **"Repeater Table"** from the Field Type dropdown
4. Fill in:
   - **Field Name**: e.g., `account_details`
   - **Field Label**: e.g., `Account Details`
   - **Description** (optional)
   - **Help Text** (optional)

### Step 2: Configure Repeater Settings
After creating the field, you need to configure the columns in the `field_settings` JSON. You can do this by:

**Option A: Direct Database Update**
```php
// In tinker or seeder
$field = FormField::where('field_name', 'account_details')->first();
$field->field_settings = [
    'columns' => [
        [
            'name' => 'account_type',
            'label' => 'Account Type',
            'type' => 'select', // or 'text', 'number', 'email', etc.
            'placeholder' => 'Select account type',
            'options' => [
                'savings' => 'Savings Account',
                'current' => 'Current Account',
                'fixed' => 'Fixed Deposit'
            ]
        ],
        [
            'name' => 'account_no',
            'label' => 'Account No.',
            'type' => 'text',
            'placeholder' => 'Enter account number'
        ]
    ],
    'min_rows' => 1,
    'max_rows' => 10, // null for unlimited
    'add_button_text' => 'Add Row',
    'remove_button_text' => 'Remove',
    'description_position' => 'bottom'
];
$field->save();
```

**Option B: Via Admin UI (Future Enhancement)**
- A UI will be added to configure columns directly in the form builder

## Field Settings Structure

```json
{
    "columns": [
        {
            "name": "column_name",
            "label": "Column Label",
            "type": "text|select|number|email|date",
            "placeholder": "Optional placeholder",
            "options": {
                "key": "Label"
            }
        }
    ],
    "min_rows": 1,
    "max_rows": null,
    "add_button_text": "Add Row",
    "remove_button_text": "Remove",
    "description_position": "bottom"
}
```

### Column Types Supported
- `text` - Text input
- `number` - Number input
- `email` - Email input
- `date` - Date picker
- `select` - Dropdown (requires `options`)

## Example: Account Details Table

```json
{
    "columns": [
        {
            "name": "account_type",
            "label": "Account Type",
            "type": "select",
            "options": {
                "savings": "Savings Account",
                "current": "Current Account",
                "fixed": "Fixed Deposit"
            }
        },
        {
            "name": "account_no",
            "label": "Account No.",
            "type": "text",
            "placeholder": "Enter account number"
        }
    ],
    "min_rows": 1,
    "max_rows": 10,
    "description_position": "bottom"
}
```

## How Data is Stored

Repeater fields store data as a **JSON array** in the database:

```json
[
    {
        "account_type": "savings",
        "account_no": "1234567890"
    },
    {
        "account_type": "current",
        "account_no": "0987654321"
    }
]
```

## Features

✅ **Add/Remove Rows**: Users can dynamically add or remove rows
✅ **Min/Max Rows**: Enforce minimum and maximum number of rows
✅ **Multiple Column Types**: Support for text, select, number, email, date
✅ **Validation**: Required field validation supported
✅ **Dark Mode**: Fully supports dark theme
✅ **Responsive**: Works on mobile and desktop

## Usage in Seeder

```php
FormField::create([
    'form_id' => $formId,
    'section_id' => $sectionId,
    'field_name' => 'account_details',
    'field_label' => 'Account Details',
    'field_type' => 'repeater',
    'field_description' => 'Add multiple account entries',
    'is_required' => true,
    'is_active' => true,
    'sort_order' => 1,
    'grid_column' => 'full',
    'field_settings' => [
        'columns' => [
            [
                'name' => 'account_type',
                'label' => 'Account Type',
                'type' => 'select',
                'options' => [
                    'savings' => 'Savings Account',
                    'current' => 'Current Account',
                ]
            ],
            [
                'name' => 'account_no',
                'label' => 'Account No.',
                'type' => 'text',
            ]
        ],
        'min_rows' => 1,
        'max_rows' => 10,
    ],
]);
```

## Displaying Repeater Data

When viewing submissions, repeater data will be stored as a JSON array in `field_responses`:

```php
$accountDetails = $submission->field_responses['account_details'] ?? [];
// Returns: [['account_type' => 'savings', 'account_no' => '123'], ...]
```

You can loop through it:
```php
@foreach($submission->field_responses['account_details'] ?? [] as $account)
    <p>{{ $account['account_type'] }}: {{ $account['account_no'] }}</p>
@endforeach
```

