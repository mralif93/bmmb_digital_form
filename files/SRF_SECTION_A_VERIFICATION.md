# SRF Section A - Field Verification

## 📋 Section A: Customer Information

**Total Items in Excel:** 13 items  
**Currently Implemented:** 6 fields  
**Missing:** 7 fields

---

## ✅ Currently Implemented (6 fields):

1. ✅ Customer Name
2. ✅ Phone Number
3. ✅ Email Address
4. ✅ Address
5. ✅ ID Type
6. ✅ ID Number

---

## 📝 How Fields with Sub-Fields Work:

In this system, **fields with sub-fields are handled as separate individual fields** that are logically grouped together. For example:

### **Example: Address Field with Sub-Fields**
Instead of one "Address" field with sub-fields, we create:
- `customer_address` (Street Address - textarea)
- `customer_city` (City - text)
- `customer_state` (State/Province - text)
- `customer_postal_code` (Postal Code - text)
- `customer_country` (Country - text or select)

### **Example: ID Information with Sub-Fields**
Instead of one "ID Information" field, we create:
- `customer_id_type` (ID Type - select)
- `customer_id_number` (ID Number - text)
- `customer_id_expiry_date` (ID Expiry Date - date)
- `customer_id_issuing_authority` (Issuing Authority - text, if needed)

### **Example: Employment with Sub-Fields**
Instead of one "Employment" field, we create:
- `customer_occupation` (Occupation - text)
- `customer_employer` (Employer - text)
- `customer_employer_address` (Employer Address - textarea)
- `customer_annual_income` (Annual Income - currency)

---

## ❓ Please List the 13 Items from Section A:

**Important:** If any item has sub-fields, please list them separately. For example:
- If "Address" has sub-fields: Street, City, State, Postal Code, Country → list as 5 separate items
- If "ID Information" has sub-fields: ID Type, ID Number, Expiry Date → list as 3 separate items

**Section A Items (Please list all 13, including sub-fields as separate items):**

1. _____________________________
   - Sub-fields (if any): _____________________________
2. _____________________________
   - Sub-fields (if any): _____________________________
3. _____________________________
   - Sub-fields (if any): _____________________________
4. _____________________________
   - Sub-fields (if any): _____________________________
5. _____________________________
   - Sub-fields (if any): _____________________________
6. _____________________________
   - Sub-fields (if any): _____________________________
7. _____________________________
   - Sub-fields (if any): _____________________________
8. _____________________________
   - Sub-fields (if any): _____________________________
9. _____________________________
   - Sub-fields (if any): _____________________________
10. _____________________________
    - Sub-fields (if any): _____________________________
11. _____________________________
    - Sub-fields (if any): _____________________________
12. _____________________________
    - Sub-fields (if any): _____________________________
13. _____________________________
    - Sub-fields (if any): _____________________________

---

## 📝 Expected Structure (Based on Banking Forms):

### **Common Field Groups with Sub-Fields:**

#### **1. Address Information (5 sub-fields):**
- Street Address (textarea)
- City (text)
- State/Province (text)
- Postal Code (text)
- Country (text/select)

#### **2. Identification Information (3-4 sub-fields):**
- ID Type (select)
- ID Number (text)
- ID Expiry Date (date)
- ID Issuing Authority (text, optional)

#### **3. Personal Information (4-5 sub-fields):**
- Date of Birth (date)
- Gender (select)
- Nationality (text)
- Marital Status (select)
- Place of Birth (text, optional)

#### **4. Employment Information (4 sub-fields):**
- Occupation (text)
- Employer (text)
- Employer Address (textarea)
- Annual Income (currency)

#### **5. Contact Information (3 sub-fields):**
- Phone Number (phone)
- Email Address (email)
- Alternate Phone (phone, optional)

---

## 🔄 Next Steps:

1. **You provide:** 
   - List all 13 items from Section A
   - Indicate which items have sub-fields
   - List all sub-fields for each item

2. **I will:**
   - Create separate fields for each item and sub-field
   - Group them logically in the same section
   - Set proper field types and validation
   - Update the seeder

3. **We verify:** 
   - All 13 items (and their sub-fields) are correctly implemented
   - Fields are properly grouped and ordered

---

## 💡 Example Format:

If Section A has:
- **Item 1:** Customer Name (no sub-fields)
- **Item 2:** Address (has sub-fields: Street, City, State, Postal Code, Country)
- **Item 3:** ID Information (has sub-fields: ID Type, ID Number, Expiry Date)

Then I will create:
1. `customer_name` - text field
2. `customer_address` - textarea field
3. `customer_city` - text field
4. `customer_state` - text field
5. `customer_postal_code` - text field
6. `customer_country` - text/select field
7. `customer_id_type` - select field
8. `customer_id_number` - text field
9. `customer_id_expiry_date` - date field

**Please provide the 13 items with their sub-fields so I can structure them correctly!**

