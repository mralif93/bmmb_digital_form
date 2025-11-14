# SRF Section A - Field Structure with Conditional Sub-Fields

## 📋 Pattern Understanding

Based on the form image, Section A uses this pattern:
- **Checkbox Options** → When checked, show related input fields (sub-fields)
- **Regular Fields** → Always visible (name, phone, email, etc.)

---

## 🎯 Example from Form Image

### **Pattern: Checkbox with Conditional Sub-Fields**

**Example 1: Transfer of Fund**
```
☐ Transfer of fund / Pemindahan wang
  (Note: Not Applicable for Foreign Currency Account & External Account)
  
  [When checked, shows:]
  - To account no. / Ke akaun bernombor: [input field]
  - Under the name/company of: / Di atas nama/syarikat: [input field]
  - Amount / Jumlah: [input field]
```

**Example 2: Cancellation/Repurchase of Cashier's Order**
```
☐ Cancellation/Repurchase of Cashier's Order / Pembatalan/Belian Semula Cek Bank
  
  [When checked, shows:]
  - Cheque no. / Cek bernombor: [input field]
  - Amount / Jumlah: [input field]
  - Reason / Atas sebab: [input field]
```

**Example 3: Stop Payment on Cheque**
```
☐ Stop payment on cheque / Menghentikan pembayaran cek
  
  [When checked, shows:]
  - Cheque no. / Cek bernombor: [input field]
  - Under the name/company of: / Di atas nama/syarikat: [input field]
  - Reason / Atas sebab: [input field]
```

**Example 4: Bank Account Statement**
```
☐ Bank account statement for the month of / Penyata akaun Bank bagi bulan
  
  [When checked, shows:]
  - [Month/Date input field]
```

---

## 📝 Section A - Complete Field Structure

### **Please provide the 13 items from Section A:**

**Format:**
- If it's a regular field (always visible): Just list the field name
- If it's a checkbox option: List the checkbox name + all its sub-fields

---

## ✅ Implementation Structure

For each checkbox option with sub-fields:

### **1. Main Checkbox Field**
- Type: `checkbox` (single checkbox, NOT array)
- Conditional: No (it's the trigger)
- Example: `transfer_of_fund`

### **2. Sub-Fields (Conditional)**
- Type: `text`, `currency`, `textarea`, `date`, etc.
- Conditional: Yes (`is_conditional = true`)
- Conditional Logic: Show when main checkbox is `checked`
- Example: `transfer_to_account_number`, `transfer_to_account_name`, `transfer_amount`

---

## 🔧 Conditional Logic Configuration

For sub-fields, use this structure:

```php
[
    'field_name' => 'transfer_to_account_number',
    'field_label' => 'To account no. / Ke akaun bernombor',
    'field_type' => 'text',
    'is_required' => false,  // Usually optional unless specified
    'is_active' => true,
    'is_conditional' => true,
    'conditional_logic' => [
        'show_if' => [
            'field' => 'transfer_of_fund',  // Main checkbox field name
            'operator' => 'checked',         // Special operator for checkbox
            'value' => '',                  // Not needed for 'checked' operator
        ],
    ],
],
```

---

## 📋 Section A Items Template

Please fill in the 13 items:

### **Item 1:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 2:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 3:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 4:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 5:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 6:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 7:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 8:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 9:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 10:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 11:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 12:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

### **Item 13:**
- Type: [ ] Regular Field  [ ] Checkbox Option
- Field Name/Label: _____________________________
- Sub-fields (if checkbox): 
  1. _____________________________
  2. _____________________________
  3. _____________________________

---

## 💡 Quick Example Based on Image

If Section A has these items:

1. **Customer Name** (Regular text field)
2. **Phone Number** (Regular phone field)
3. **Email Address** (Regular email field)
4. **Transfer of fund** (Checkbox)
   - Sub-fields: To account no., Account name, Amount
5. **Cancellation/Repurchase** (Checkbox)
   - Sub-fields: Cheque no., Amount, Reason
6. **Stop payment on cheque** (Checkbox)
   - Sub-fields: Cheque no., Account name, Reason
7. **Bank account statement** (Checkbox)
   - Sub-fields: Month/Date
8. ... (other items)

Then total fields = 3 regular + 1 checkbox + 3 sub-fields + 1 checkbox + 3 sub-fields + 1 checkbox + 3 sub-fields + 1 checkbox + 1 sub-field = **17 fields**

But if we count "items" (main options), it's: 3 regular + 4 checkbox options = **7 items**

**Please clarify: Are the 13 items the main options (including checkboxes), or are they counting all individual fields (including sub-fields)?**

---

## 🔄 Next Steps

1. **You provide:** The 13 items from Section A with their structure
2. **I will:** 
   - Create all fields (main + sub-fields)
   - Set up conditional logic for sub-fields
   - Ensure proper ordering
   - Update the seeder
3. **We test:** Verify checkboxes show/hide sub-fields correctly

---

**Please provide the 13 items from Section A with their structure!**

