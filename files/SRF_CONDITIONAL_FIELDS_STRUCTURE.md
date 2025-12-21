# SRF Form - Conditional Fields Structure (Checkbox with Sub-Fields)

## 📋 Understanding the Pattern

Based on the form image, the pattern is:
- **Main Field:** A checkbox option (e.g., "Transfer of fund")
- **Sub-Fields:** Related input fields that appear when the checkbox is checked
- **Implementation:** Use conditional logic to show/hide sub-fields based on checkbox state

---

## 🎯 Example from the Form Image

### **Pattern 1: Transfer of Fund**
- **Main Field:** `transfer_of_fund` (checkbox)
- **Sub-Fields (shown when checked):**
  1. `transfer_to_account_number` (text) - "To account no."
  2. `transfer_to_account_name` (text) - "Under the name/company of"
  3. `transfer_amount` (currency) - "Amount"

### **Pattern 2: Cancellation/Repurchase of Cashier's Order**
- **Main Field:** `cancel_repurchase_cashiers_order` (checkbox)
- **Sub-Fields (shown when checked):**
  1. `cashiers_order_cheque_number` (text) - "Cheque no."
  2. `cashiers_order_amount` (currency) - "Amount"
  3. `cashiers_order_reason` (textarea) - "Reason"

### **Pattern 3: Stop Payment on Cheque**
- **Main Field:** `stop_payment_cheque` (checkbox)
- **Sub-Fields (shown when checked):**
  1. `stop_payment_cheque_number` (text) - "Cheque no."
  2. `stop_payment_cheque_name` (text) - "Under the name/company of"
  3. `stop_payment_reason` (textarea) - "Reason"

### **Pattern 4: Bank Account Statement**
- **Main Field:** `bank_account_statement` (checkbox)
- **Sub-Fields (shown when checked):**
  1. `statement_month` (date or select) - "for the month of"

---

## 🔧 Implementation Structure

For each checkbox option with sub-fields, we create:

1. **Main Checkbox Field** (field_type: `checkbox`)
   - Single checkbox (not array)
   - Field name: e.g., `transfer_of_fund`
   - Label: e.g., "Transfer of fund / Pemindahan wang"

2. **Sub-Fields** (each as separate fields with conditional logic)
   - Each sub-field has `is_conditional = true`
   - Conditional logic: `show_if` when main checkbox is checked
   - Field names: e.g., `transfer_to_account_number`, `transfer_to_account_name`, `transfer_amount`

---

## 📝 Field Structure Example

### **Transfer of Fund - Complete Structure:**

```php
// Main checkbox field
[
    'field_name' => 'transfer_of_fund',
    'field_label' => 'Transfer of fund / Pemindahan wang',
    'field_type' => 'checkbox',  // Single checkbox
    'field_description' => 'Note: Not Applicable for Foreign Currency Account & External Account',
    'is_required' => false,
    'is_active' => true,
    'sort_order' => 1,
],

// Sub-field 1: To account number
[
    'field_name' => 'transfer_to_account_number',
    'field_label' => 'To account no. / Ke akaun bernombor',
    'field_type' => 'text',
    'is_required' => false,
    'is_active' => true,
    'is_conditional' => true,
    'conditional_logic' => [
        'show_if' => [
            'field' => 'transfer_of_fund',
            'operator' => 'checked',  // Special operator for checkbox
            'value' => '',  // Not needed for 'checked' operator
        ],
    ],
    'sort_order' => 2,
],

// Sub-field 2: Account name
[
    'field_name' => 'transfer_to_account_name',
    'field_label' => 'Under the name/company of: / Di atas nama/syarikat',
    'field_type' => 'text',
    'is_required' => false,
    'is_active' => true,
    'is_conditional' => true,
    'conditional_logic' => [
        'show_if' => [
            'field' => 'transfer_of_fund',
            'operator' => 'checked',
            'value' => '',
        ],
    ],
    'sort_order' => 3,
],

// Sub-field 3: Amount
[
    'field_name' => 'transfer_amount',
    'field_label' => 'Amount / Jumlah',
    'field_type' => 'currency',
    'is_required' => false,
    'is_active' => true,
    'is_conditional' => true,
    'conditional_logic' => [
        'show_if' => [
            'field' => 'transfer_of_fund',
            'operator' => 'checked',
            'value' => '',
        ],
    ],
    'field_settings' => ['currency' => 'MYR'],
    'sort_order' => 4,
],
```

---

## 🎨 Visual Structure in Form

When rendered, it will look like:

```
☐ Transfer of fund / Pemindahan wang
  (Note: Not Applicable for Foreign Currency Account & External Account)
  
  [When checked, shows:]
  To account no. / Ke akaun bernombor: [___________]
  Under the name/company of: / Di atas nama/syarikat: [___________]
  Amount / Jumlah: [___________]
```

---

## ✅ Conditional Logic Operators for Checkboxes

For checkbox sub-fields, we use:

1. **`checked`** operator - Show when checkbox is checked
   ```php
   'conditional_logic' => [
       'show_if' => [
           'field' => 'transfer_of_fund',
           'operator' => 'checked',
           'value' => '',  // Not needed
       ],
   ],
   ```

2. **`equals` with value "1" or "true"** - Alternative way
   ```php
   'conditional_logic' => [
       'show_if' => [
           'field' => 'transfer_of_fund',
           'operator' => 'equals',
           'value' => '1',  // or 'true'
       ],
   ],
   ```

---

## 📋 Section A - Expected Structure

Based on the form pattern, Section A likely has:

### **Option 1: Multiple Checkbox Options (Each with Sub-Fields)**

If Section A has checkbox options like:
1. Transfer of fund (with 3 sub-fields)
2. Cancellation/Repurchase (with 3 sub-fields)
3. Stop payment (with 3 sub-fields)
4. Bank statement (with 1 sub-field)
5. ... (other options)

**Total fields = Number of checkboxes + Sum of all sub-fields**

### **Option 2: Mixed Structure**

Section A might have:
- Some regular fields (name, phone, email, etc.)
- Some checkbox options with sub-fields

---

## 🔄 Next Steps

Please provide:

1. **List all 13 items in Section A:**
   - Which are regular fields (text, email, etc.)?
   - Which are checkbox options?
   - For each checkbox option, list its sub-fields

2. **Example format:**
   ```
   Section A Items:
   1. Customer Name (regular text field)
   2. Phone Number (regular phone field)
   3. Transfer of fund (checkbox)
      - Sub-fields: To account no., Account name, Amount
   4. Cancellation/Repurchase (checkbox)
      - Sub-fields: Cheque no., Amount, Reason
   ... and so on
   ```

3. **I will then:**
   - Create the main checkbox fields
   - Create all sub-fields with conditional logic
   - Ensure proper ordering and grouping
   - Update the seeder

---

## 💡 Important Notes

- Each checkbox option = 1 main field
- Each sub-field = 1 separate field with conditional logic
- Sub-fields are hidden by default, shown when checkbox is checked
- All fields in the same section, ordered sequentially
- The JavaScript conditional logic we implemented will handle the show/hide automatically

**Please provide the 13 items from Section A with their structure (regular fields vs checkbox options with sub-fields)!**

