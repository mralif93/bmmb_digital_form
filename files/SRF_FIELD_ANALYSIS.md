# Service Request Form (SRF) v16.0 - Field Analysis

## 📋 Document Reference
**File:** `Service Request Form (SRF)_v16.0_DEPOSIT.xlsx`  
**Version:** 16.0  
**Type:** Deposit Service Request

---

## 🔍 Current Implementation Status

### **Currently Implemented Fields (12 fields):**

#### **Section 1: Customer Information (6 fields)**
1. ✅ `customer_name` - text - Required
2. ✅ `customer_phone` - phone - Required
3. ✅ `customer_email` - email - Required
4. ✅ `customer_address` - textarea - Required
5. ✅ `customer_id_type` - select - Required (Passport, National ID, Driver's License, Other)
6. ✅ `customer_id_number` - text - Required

#### **Section 2: Account Information (2 fields)**
1. ✅ `account_number` - text - Required
2. ✅ `account_type` - select - Required (Savings, Current, Fixed Deposit, Other)

#### **Section 3: Service Details (6 fields)**
1. ✅ `service_type` - select - Required (Deposit, Withdrawal, Transfer, Statement Request, Other)
2. ✅ `service_description` - textarea - Required
3. ✅ `service_amount` - currency - Optional (MYR)
4. ✅ `service_priority` - select - Required (Low, Normal, High, Urgent)
5. ✅ `service_type_other` - textarea - Conditional (shows if service_type = "other")
6. ✅ `urgent_reason` - textarea - Conditional (shows if priority = "urgent")

---

## 📊 Recommended Additional Fields (Based on Backup Migration & Banking Standards)

### **Section 1: Customer Information - Additional Fields**

#### **Personal Details:**
7. ⚠️ `customer_city` - text - Required
8. ⚠️ `customer_state` - text - Required
9. ⚠️ `customer_postal_code` - text - Required
10. ⚠️ `customer_country` - text - Required
11. ⚠️ `customer_id_expiry_date` - date - Optional
12. ⚠️ `customer_dob` - date - Optional (Date of Birth)
13. ⚠️ `customer_gender` - select - Optional (Male, Female, Other)
14. ⚠️ `customer_nationality` - text - Optional
15. ⚠️ `customer_marital_status` - select - Optional (Single, Married, Divorced, Widowed)

#### **Employment Information:**
16. ⚠️ `customer_occupation` - text - Optional
17. ⚠️ `customer_employer` - text - Optional
18. ⚠️ `customer_employer_address` - textarea - Optional
19. ⚠️ `customer_annual_income` - currency - Optional

---

### **Section 2: Account Information - Additional Fields**

#### **Account Details:**
3. ⚠️ `account_currency` - select - Optional (USD, EUR, GBP, MYR, SGD, etc.)
4. ⚠️ `account_balance` - currency - Optional (Current balance)
5. ⚠️ `account_opening_date` - date - Optional
6. ⚠️ `account_status` - select - Optional (Active, Dormant, Frozen, Closed)
7. ⚠️ `account_notes` - textarea - Optional
8. ⚠️ `account_type_other` - text - Conditional (shows if account_type = "other")

---

### **Section 3: Service Details - Additional Fields**

#### **Service Category & Subcategories:**
7. ⚠️ `service_category` - select - Required
   - Options: Banking, Investment, Insurance, Loan, Credit Card, Foreign Exchange, International Transfer, Other
8. ⚠️ `service_subcategories` - checkbox - Optional (Multiple selections possible)
9. ⚠️ `service_currency` - select - Optional (If different from account currency)
10. ⚠️ `preferred_completion_date` - date - Optional
11. ⚠️ `special_instructions` - textarea - Optional
12. ⚠️ `reason_for_request` - textarea - Optional

#### **Deposit-Specific Fields (Based on filename: DEPOSIT):**
13. ⚠️ `deposit_type` - select - Conditional (shows if service_type = "deposit")
    - Options: Cash, Check, Wire Transfer, ACH Transfer, Mobile Deposit, ATM Deposit, In Person, Online, Other
14. ⚠️ `deposit_method` - select - Conditional (shows if service_type = "deposit")
15. ⚠️ `deposit_source` - text - Conditional (shows if service_type = "deposit")
    - Where funds are coming from
16. ⚠️ `deposit_reference_number` - text - Conditional (shows if service_type = "deposit")
17. ⚠️ `deposit_date` - date - Conditional (shows if service_type = "deposit")
18. ⚠️ `deposit_currency` - select - Conditional (shows if service_type = "deposit")
19. ⚠️ `deposit_exchange_rate` - number - Conditional (shows if deposit_currency ≠ account_currency)

---

### **Section 4: Financial Information - Additional Fields**

1. ⚠️ `source_of_funds` - select - Required
   - Options: Salary, Business Income, Investment Returns, Gift, Inheritance, Sale of Property, Other
2. ⚠️ `source_of_funds_other` - textarea - Conditional (shows if source_of_funds = "other")
3. ⚠️ `expected_transaction_frequency` - select - Optional
   - Options: One-time, Monthly, Quarterly, Annually, Irregular
4. ⚠️ `transaction_purpose` - textarea - Optional
5. ⚠️ `beneficiary_name` - text - Optional (If applicable)
6. ⚠️ `beneficiary_account` - text - Optional (If applicable)
7. ⚠️ `beneficiary_bank` - text - Optional (If applicable)

---

### **Section 5: Compliance & Risk - Additional Fields**

1. ⚠️ `kyc_status` - select - Optional
   - Options: Verified, Pending, Not Verified
2. ⚠️ `aml_check_required` - checkbox - Optional
3. ⚠️ `risk_level` - select - Optional
   - Options: Low, Medium, High
4. ⚠️ `compliance_notes` - textarea - Optional
5. ⚠️ `regulatory_requirements` - checkbox - Optional
   - Options: Tax Reporting, AML Compliance, KYC Update, Other

---

### **Section 6: Supporting Documents - Additional Fields**

1. ⚠️ `identity_document` - file - Optional
2. ⚠️ `proof_of_address` - file - Optional
3. ⚠️ `proof_of_income` - file - Optional
4. ⚠️ `bank_statement` - file - Optional
5. ⚠️ `additional_documents` - file - Optional (Multiple files)
6. ⚠️ `document_notes` - textarea - Optional

---

### **Section 7: Service Delivery - Additional Fields**

1. ⚠️ `preferred_delivery_method` - select - Optional
   - Options: Email, Mail, In-Person Pickup, Online Portal, SMS
2. ⚠️ `delivery_address` - textarea - Conditional (shows if preferred_delivery_method = "Mail")
3. ⚠️ `contact_preference` - checkbox - Optional
   - Options: Email, Phone, SMS, Mail
4. ⚠️ `special_delivery_instructions` - textarea - Optional
5. ⚠️ `expected_delivery_date` - date - Optional

---

## 📝 Field Summary

### **Current Implementation:**
- **Total Fields:** 12 fields
- **Sections:** 3 sections (Customer Info, Account Info, Service Details)
- **Conditional Fields:** 2 fields

### **Recommended Complete Implementation:**
- **Total Fields:** ~60-70 fields
- **Sections:** 7 sections (as defined in seeder)
- **Conditional Fields:** ~10-15 fields

---

## 🎯 Priority Classification

### **High Priority (Essential for Deposit Service Request):**
1. Customer address details (city, state, postal code, country)
2. Deposit-specific fields (deposit_type, deposit_method, deposit_source)
3. Source of funds
4. Identity document upload
5. Account currency

### **Medium Priority (Important for Compliance):**
1. Customer personal details (DOB, gender, nationality)
2. Employment information
3. KYC/AML fields
4. Supporting documents
5. Service delivery preferences

### **Low Priority (Nice to Have):**
1. Account balance and status
2. Transaction frequency
3. Beneficiary information
4. Special delivery instructions

---

## ✅ Verification Checklist

Please review and confirm:

- [ ] Are all customer information fields needed?
- [ ] Which deposit-specific fields are required?
- [ ] Are compliance fields mandatory?
- [ ] Which documents need to be uploaded?
- [ ] Are there any additional fields in the Excel file not listed here?
- [ ] Should we implement all fields or prioritize certain sections?

---

## 📌 Next Steps

1. **Review this list** - Confirm which fields are needed
2. **Check Excel file** - Verify against actual form structure
3. **Prioritize fields** - Decide which fields are essential vs optional
4. **Update seeder** - Add confirmed fields to FormManagementSeeder
5. **Test form** - Verify all fields work correctly with conditional logic

---

## 🔗 Related Files

- Current Seeder: `database/seeders/FormManagementSeeder.php` (lines 1346-1563)
- Backup Migration: `database/migrations/backup_old_forms/2025_10_22_023137_create_srf_system_tables.php`
- Form Excel: `assets/forms/Service Request Form (SRF)_v16.0_DEPOSIT.xlsx`

