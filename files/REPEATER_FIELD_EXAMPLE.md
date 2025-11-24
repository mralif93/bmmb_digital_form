# Repeater Field Example - Account Details Table

## Quick Example for Your Account Table

Based on your image showing "ACCOUNT TYPE" and "ACCOUNT NO." columns, here's how to add it to your SRF form:

### Using Tinker (Quick Test)

```php
php artisan tinker

// Get the SRF form and a section
$form = \App\Models\Form::where('slug', 'srf')->first();
$section = $form->sections()->where('section_key', 'account_type')->first();

// Create the repeater field
\App\Models\FormField::create([
    'form_id' => $form->id,
    'section_id' => $section->id,
    'field_name' => 'account_details',
    'field_label' => 'Account Details',
    'field_type' => 'repeater',
    'field_description' => 'Add multiple account entries',
    'is_required' => false,
    'is_active' => true,
    'sort_order' => 100, // Adjust based on where you want it
    'grid_column' => 'full',
    'field_settings' => [
        'columns' => [
            [
                'name' => 'account_type',
                'label' => 'Account Type',
                'type' => 'text',
                'placeholder' => 'e.g., Savings, Current, Fixed Deposit'
            ],
            [
                'name' => 'account_no',
                'label' => 'Account No.',
                'type' => 'text',
                'placeholder' => 'Enter account number'
            ]
        ],
        'min_rows' => 1,
        'max_rows' => null, // Unlimited
        'add_button_text' => 'Add Account',
        'remove_button_text' => 'Remove',
        'description_position' => 'bottom'
    ],
]);
```

### With Select Dropdown for Account Type

If you want Account Type to be a dropdown:

```php
\App\Models\FormField::create([
    'form_id' => $form->id,
    'section_id' => $section->id,
    'field_name' => 'account_details',
    'field_label' => 'Account Details',
    'field_type' => 'repeater',
    'field_description' => 'Add multiple account entries',
    'is_required' => false,
    'is_active' => true,
    'sort_order' => 100,
    'grid_column' => 'full',
    'field_settings' => [
        'columns' => [
            [
                'name' => 'account_type',
                'label' => 'Account Type',
                'type' => 'select',
                'placeholder' => 'Select account type',
                'options' => [
                    'savings' => 'Savings Account',
                    'current' => 'Current Account',
                    'fixed_deposit' => 'Fixed Deposit',
                    'investment' => 'Investment Account',
                    'other' => 'Other'
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
        'max_rows' => 10,
        'add_button_text' => 'Add Account',
        'remove_button_text' => 'Remove',
        'description_position' => 'bottom'
    ],
]);
```

## Result

This will create a table in your form with:
- **Header Row**: "Account Type" | "Account No." | "Action"
- **Data Rows**: Users can add multiple rows
- **Add Button**: "Add Account" button to add new rows
- **Remove Button**: Each row has a remove button

## Data Storage

When submitted, data will be stored as:
```json
[
    {"account_type": "Savings Account", "account_no": "1234567890"},
    {"account_type": "Current Account", "account_no": "0987654321"}
]
```

## Next Steps

1. Run the tinker command above to add the field
2. Visit your SRF form to see the repeater table
3. Test adding/removing rows
4. Submit the form to see how data is stored

