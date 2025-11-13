# Conditional Logic Examples in Seeders

This document outlines all the conditional logic examples that have been added to the form seeders.

## 📋 Overview

Conditional logic allows fields to show or hide based on the value of other fields. This creates dynamic, user-friendly forms that adapt based on user input.

## 🎯 Conditional Logic Examples by Form

### **RAF (Remittance Application Form)**

#### 1. **Other ID Type Specification**
- **Field:** `applicant_other_id_type`
- **Trigger Field:** `applicant_id_type`
- **Condition:** Show if `applicant_id_type` equals `"other"`
- **Type:** Select → Text Input
- **Use Case:** When user selects "Other" for ID type, show a text field to specify the ID type

#### 2. **Alternate Email Address**
- **Field:** `alternate_email`
- **Trigger Field:** `preferred_contact_method` (checkbox)
- **Condition:** Show if `preferred_contact_method` equals `"email"`
- **Type:** Checkbox → Email Input
- **Use Case:** When user checks "Email" in preferred contact method, show alternate email field

#### 3. **Alternate Phone Number**
- **Field:** `alternate_phone`
- **Trigger Field:** `preferred_contact_method` (checkbox)
- **Condition:** Show if `preferred_contact_method` equals `"phone"`
- **Type:** Checkbox → Phone Input
- **Use Case:** When user checks "Phone" in preferred contact method, show alternate phone field

#### 4. **Remittance Purpose Description**
- **Field:** `remittance_purpose_description`
- **Trigger Field:** `remittance_purpose`
- **Condition:** Show if `remittance_purpose` equals `"other"`
- **Type:** Select → Textarea
- **Use Case:** When user selects "Other" for remittance purpose, show description field

#### 5. **Other Payment Method Details**
- **Field:** `payment_method_other`
- **Trigger Field:** `payment_method`
- **Condition:** Show if `payment_method` equals `"other"`
- **Type:** Select → Text Input
- **Use Case:** When user selects "Other" for payment method, show details field

#### 6. **Other Payment Source Details**
- **Field:** `payment_source_other`
- **Trigger Field:** `payment_source`
- **Condition:** Show if `payment_source` equals `"other"`
- **Type:** Select → Text Input
- **Use Case:** When user selects "Other" for payment source, show details field

---

### **DAR (Data Access Request Form)**

#### 1. **Other Request Type Details**
- **Field:** `request_type_other`
- **Trigger Field:** `request_type`
- **Condition:** Show if `request_type` equals `"other"`
- **Type:** Select → Textarea
- **Use Case:** When user selects "Other" for request type, show details field

#### 2. **Other Data Categories**
- **Field:** `data_categories_other`
- **Trigger Field:** `data_categories` (checkbox)
- **Condition:** Show if `data_categories` equals `"other"`
- **Type:** Checkbox → Textarea
- **Use Case:** When user checks "Other" in data categories, show specification field

---

### **SRF (Service Request Form)**

#### 1. **Other Service Type Details**
- **Field:** `service_type_other`
- **Trigger Field:** `service_type`
- **Condition:** Show if `service_type` equals `"other"`
- **Type:** Select → Textarea
- **Use Case:** When user selects "Other" for service type, show details field

#### 2. **Reason for Urgent Priority**
- **Field:** `urgent_reason`
- **Trigger Field:** `service_priority`
- **Condition:** Show if `service_priority` equals `"urgent"`
- **Type:** Select → Textarea
- **Use Case:** When user selects "Urgent" priority, show reason field

---

## 🔧 Conditional Logic Structure

All conditional logic follows this structure:

```php
'is_conditional' => true,
'conditional_logic' => [
    'show_if' => [  // or 'hide_if'
        'field' => 'field_name',      // The field to check
        'operator' => 'equals',       // Operator: equals, contains, not_equals, checked, not_checked
        'value' => 'target_value',    // Value to match
    ],
],
```

## 📊 Supported Operators

1. **`equals`** - Exact match (works with select, radio, checkbox, text)
2. **`contains`** - Value contains substring (works with text, select)
3. **`not_equals`** - Value does not match
4. **`checked`** - Checkbox is checked (checkbox only)
5. **`not_checked`** - Checkbox is not checked (checkbox only)

## 🎨 Field Type Combinations

### **Select → Text/Textarea**
- Most common pattern
- Example: Show "Other" details when "Other" is selected

### **Checkbox → Email/Phone/Text**
- Show specific input when checkbox is checked
- Example: Show email field when "Email" checkbox is checked

### **Select → Textarea**
- Show detailed description field
- Example: Show reason field when "Urgent" is selected

## 📝 Notes for Assets/Forms

The following fields in the PDF forms could benefit from conditional logic:

### **RAF Form (Appendix I - Remittance Application Form V5.0.pdf)**
- Any "Other" option in dropdowns should show a specification field
- Contact method preferences could show alternate contact fields
- Payment method "Other" should show details field

### **DAR Form (DATA ACCESS REQUEST FORM.pdf)**
- Request type "Other" should show details
- Data categories "Other" should show specification
- Relationship "Other" could show details

### **DCR Form (DATA CORRECTION REQUEST FORM.pdf)**
- ID type "Other" should show specification
- Correction type "Other" could show details

### **SRF Form (Service Request Form v16.0_DEPOSIT.xlsx)**
- Service type "Other" should show details
- Priority "Urgent" should show reason
- Account type "Other" could show specification

## ✅ Implementation Status

All conditional logic examples have been added to the `FormManagementSeeder.php` file and will be created when running:

```bash
php artisan db:seed --class=FormManagementSeeder
```

## 🔄 Testing Conditional Logic

To test conditional logic:

1. Run the seeder to create forms with conditional fields
2. Visit the public form page
3. Interact with trigger fields (select, checkbox, radio)
4. Verify that conditional fields appear/disappear correctly

## 📚 Related Documentation

- See `DYNAMIC_FORM_SYSTEM_GUIDE.md` for form system overview
- See `FORM_SYSTEM_FLOW.md` for form submission flow
- Conditional logic JavaScript implementation: `resources/views/public/forms/dynamic.blade.php`

