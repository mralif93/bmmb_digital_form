# Service Request Form (SRF) v16.0 - Complete Field List for Review

## 📋 Document Information
**Source File:** `Service Request Form (SRF)_v16.0_DEPOSIT.xlsx`  
**Version:** 16.0  
**Form Type:** Deposit Service Request  
**Based on:** Backup migration + Current seeder + Banking standards

---

## ✅ CURRENTLY IMPLEMENTED (12 fields)

### Section 1: Customer Information (6 fields)
1. ✅ `customer_name` - text - Required
2. ✅ `customer_phone` - phone - Required  
3. ✅ `customer_email` - email - Required
4. ✅ `customer_address` - textarea - Required
5. ✅ `customer_id_type` - select - Required (Passport, National ID, Driver's License, Other)
6. ✅ `customer_id_number` - text - Required

### Section 2: Account Information (2 fields)
1. ✅ `account_number` - text - Required
2. ✅ `account_type` - select - Required (Savings, Current, Fixed Deposit, Other)

### Section 3: Service Details (4 fields)
1. ✅ `service_type` - select - Required (Deposit, Withdrawal, Transfer, Statement Request, Other)
2. ✅ `service_description` - textarea - Required
3. ✅ `service_amount` - currency - Optional (MYR)
4. ✅ `service_priority` - select - Required (Low, Normal, High, Urgent)

### Conditional Fields (2 fields)
1. ✅ `service_type_other` - textarea - Shows if service_type = "other"
2. ✅ `urgent_reason` - textarea - Shows if priority = "urgent"

---

## 📝 RECOMMENDED ADDITIONAL FIELDS (Based on Backup Migration)

### **Section 1: Customer Information - Additional Fields**

#### **Address Details (4 fields):**
7. ⚠️ `customer_city` - text - Required
8. ⚠️ `customer_state` - text - Required
9. ⚠️ `customer_postal_code` - text - Required
10. ⚠️ `customer_country` - text - Required

#### **Personal Details (6 fields):**
11. ⚠️ `customer_id_expiry_date` - date - Optional
12. ⚠️ `customer_dob` - date - Optional (Date of Birth)
13. ⚠️ `customer_gender` - select - Optional
    - Options: Male, Female, Other, Prefer not to say
14. ⚠️ `customer_nationality` - text - Optional
15. ⚠️ `customer_marital_status` - select - Optional
    - Options: Single, Married, Divorced, Widowed, Separated

#### **Employment Information (4 fields):**
16. ⚠️ `customer_occupation` - text - Optional
17. ⚠️ `customer_employer` - text - Optional
18. ⚠️ `customer_employer_address` - textarea - Optional
19. ⚠️ `customer_annual_income` - currency - Optional

#### **Conditional Fields:**
20. ⚠️ `customer_id_type_other` - text - Shows if customer_id_type = "other"

---

### **Section 2: Account Information - Additional Fields**

#### **Account Details (6 fields):**
3. ⚠️ `account_currency` - select - Optional
    - Options: USD, EUR, GBP, MYR, SGD, AUD, JPY, CNY, Other
4. ⚠️ `account_balance` - currency - Optional (Current balance - read-only info)
5. ⚠️ `account_opening_date` - date - Optional
6. ⚠️ `account_status` - select - Optional
    - Options: Active, Dormant, Frozen, Closed, Pending
7. ⚠️ `account_notes` - textarea - Optional
8. ⚠️ `account_type_other` - text - Conditional (shows if account_type = "other")

---

### **Section 3: Service Details - Additional Fields**

#### **Service Category (2 fields):**
5. ⚠️ `service_category` - select - Required
    - Options: Banking, Investment, Insurance, Loan, Credit Card, Foreign Exchange, International Transfer, Other
6. ⚠️ `service_subcategories` - checkbox - Optional
    - Options: (Dependent on service_category - multiple selections)

#### **Service Timing & Instructions (3 fields):**
7. ⚠️ `preferred_completion_date` - date - Optional
8. ⚠️ `special_instructions` - textarea - Optional
9. ⚠️ `reason_for_request` - textarea - Optional

#### **Service Currency:**
10. ⚠️ `service_currency` - select - Optional
    - Options: USD, EUR, GBP, MYR, SGD, AUD, JPY, CNY, Other
    - Default: Same as account_currency

---

### **Section 3: Deposit-Specific Fields (Conditional - Shows if service_type = "deposit")**

#### **Deposit Type & Method (3 fields):**
11. ⚠️ `deposit_type` - select - Conditional
    - Options: Cash, Check, Wire Transfer, ACH Transfer, Mobile Deposit, ATM Deposit, In Person, Online, Other
12. ⚠️ `deposit_method` - select - Conditional
    - Options: Branch, ATM, Online Banking, Mobile App, Phone Banking, Mail, Other
13. ⚠️ `deposit_source` - text - Conditional
    - Description: Where funds are coming from

#### **Check Deposit Details (4 fields - Shows if deposit_type = "check"):**
14. ⚠️ `check_number` - text - Conditional
15. ⚠️ `check_bank` - text - Conditional
16. ⚠️ `check_account` - text - Conditional
17. ⚠️ `check_date` - date - Conditional

#### **Wire Transfer Details (3 fields - Shows if deposit_type = "wire_transfer"):**
18. ⚠️ `wire_reference` - text - Conditional
19. ⚠️ `wire_originator` - text - Conditional
20. ⚠️ `wire_beneficiary` - text - Conditional

#### **Deposit Amount & Currency (3 fields):**
21. ⚠️ `deposit_amount` - currency - Conditional
22. ⚠️ `deposit_currency` - select - Conditional
23. ⚠️ `deposit_exchange_rate` - number - Conditional (if deposit_currency ≠ account_currency)

#### **Deposit Notes:**
24. ⚠️ `deposit_notes` - textarea - Conditional
25. ⚠️ `deposit_source_details` - textarea - Conditional

---

### **Section 4: Financial Information - Additional Fields**

#### **Source of Funds (2 fields):**
1. ⚠️ `source_of_funds` - select - Required
    - Options: Salary, Business Income, Investment Returns, Gift, Inheritance, Sale of Property, Loan, Other
2. ⚠️ `source_of_funds_other` - textarea - Conditional (shows if source_of_funds = "other")

#### **Transaction Details (4 fields):**
3. ⚠️ `transaction_amount` - currency - Optional
4. ⚠️ `transaction_currency` - select - Optional
5. ⚠️ `exchange_rate` - number - Optional (if transaction_currency ≠ account_currency)
6. ⚠️ `fees` - currency - Optional (Service fees)

#### **Transaction Information (3 fields):**
7. ⚠️ `expected_transaction_frequency` - select - Optional
    - Options: One-time, Monthly, Quarterly, Annually, Irregular
8. ⚠️ `transaction_purpose` - textarea - Optional
9. ⚠️ `total_amount` - currency - Optional (Calculated: transaction_amount + fees)

#### **Payment Method (2 fields):**
10. ⚠️ `payment_method` - select - Optional
    - Options: Bank Transfer, Cash, Check, Credit Card, Debit Card, Other
11. ⚠️ `payment_details` - textarea - Optional

#### **Beneficiary Information (3 fields - If applicable):**
12. ⚠️ `beneficiary_name` - text - Optional
13. ⚠️ `beneficiary_account` - text - Optional
14. ⚠️ `beneficiary_bank` - text - Optional

---

### **Section 5: Compliance & Risk - Additional Fields**

#### **KYC/AML Status (3 fields):**
1. ⚠️ `kyc_status` - select - Optional
    - Options: Verified, Pending Verification, Not Verified, Expired
2. ⚠️ `aml_verified` - checkbox - Optional
3. ⚠️ `sanctions_checked` - checkbox - Optional

#### **Risk Assessment (3 fields):**
4. ⚠️ `risk_level` - select - Optional
    - Options: Low, Medium, High
5. ⚠️ `risk_assessment_notes` - textarea - Optional
6. ⚠️ `requires_approval` - checkbox - Optional

#### **Compliance Notes:**
7. ⚠️ `compliance_notes` - textarea - Optional
8. ⚠️ `approval_reason` - textarea - Conditional (shows if requires_approval = true)

---

### **Section 6: Supporting Documents - Additional Fields**

#### **Required Documents (5 fields):**
1. ⚠️ `identity_document` - file - Optional
    - Accept: PDF, JPG, PNG (Max 5MB)
2. ⚠️ `proof_of_address` - file - Optional
    - Accept: PDF, JPG, PNG (Max 5MB)
3. ⚠️ `proof_of_income` - file - Optional
    - Accept: PDF, JPG, PNG (Max 5MB)
4. ⚠️ `bank_statement` - file - Optional
    - Accept: PDF, JPG, PNG (Max 10MB)
5. ⚠️ `deposit_slip` - file - Conditional (shows if deposit_type = "cash" or "check")
    - Accept: PDF, JPG, PNG (Max 5MB)

#### **Additional Documents (3 fields):**
6. ⚠️ `check_image` - file - Conditional (shows if deposit_type = "check")
7. ⚠️ `wire_confirmation` - file - Conditional (shows if deposit_type = "wire_transfer")
8. ⚠️ `other_documents` - file - Optional (Multiple files allowed)
    - Accept: PDF, JPG, PNG, DOC, DOCX (Max 10MB each)

#### **Document Notes:**
9. ⚠️ `document_notes` - textarea - Optional

---

### **Section 7: Service Delivery - Additional Fields**

#### **Delivery Method (2 fields):**
1. ⚠️ `preferred_delivery_method` - select - Optional
    - Options: Email, Mail, In-Person Pickup, Online Portal, SMS, Phone Call
2. ⚠️ `delivery_address` - textarea - Conditional (shows if preferred_delivery_method = "Mail")

#### **Contact Preferences (2 fields):**
3. ⚠️ `contact_preference` - checkbox - Optional
    - Options: Email, Phone, SMS, Mail
4. ⚠️ `delivery_contact` - text - Optional

#### **Delivery Details (4 fields):**
5. ⚠️ `delivery_phone` - phone - Optional
6. ⚠️ `delivery_date` - date - Optional
7. ⚠️ `delivery_time` - time - Optional
8. ⚠️ `special_delivery_instructions` - textarea - Optional

#### **Delivery Confirmation:**
9. ⚠️ `delivery_instructions` - textarea - Optional

---

## 📊 FIELD SUMMARY

### **Current Implementation:**
- **Total Fields:** 12 fields
- **Sections:** 3 sections
- **Conditional Fields:** 2 fields

### **Recommended Complete Implementation:**
- **Total Fields:** ~80-90 fields
- **Sections:** 7 sections (as defined)
- **Conditional Fields:** ~15-20 fields

---

## 🎯 PRIORITY CLASSIFICATION

### **🔴 HIGH PRIORITY (Essential for Deposit Service Request):**
1. Customer address details (city, state, postal code, country) - **4 fields**
2. Deposit-specific fields (deposit_type, deposit_method, deposit_source) - **3 fields**
3. Source of funds - **1 field**
4. Identity document upload - **1 field**
5. Account currency - **1 field**

**Total High Priority: 10 fields**

### **🟡 MEDIUM PRIORITY (Important for Compliance):**
1. Customer personal details (DOB, gender, nationality, marital status) - **4 fields**
2. Employment information (occupation, employer, income) - **3 fields**
3. KYC/AML fields (kyc_status, aml_verified, sanctions_checked) - **3 fields**
4. Risk assessment (risk_level, risk_assessment_notes) - **2 fields**
5. Supporting documents (proof_of_address, proof_of_income, bank_statement) - **3 fields**
6. Service delivery preferences - **2 fields**

**Total Medium Priority: 17 fields**

### **🟢 LOW PRIORITY (Nice to Have / Optional):**
1. Account balance and status - **2 fields**
2. Transaction frequency and purpose - **2 fields**
3. Beneficiary information - **3 fields**
4. Check/Wire specific details - **7 fields**
5. Delivery time and confirmation - **3 fields**
6. Additional document notes - **1 field**

**Total Low Priority: 18 fields**

---

## ✅ VERIFICATION CHECKLIST

Please review and confirm:

### **Customer Information:**
- [ ] Are all address fields (city, state, postal code, country) required?
- [ ] Do we need DOB, gender, nationality, marital status?
- [ ] Is employment information mandatory?
- [ ] Should ID expiry date be required?

### **Account Information:**
- [ ] Is account currency needed?
- [ ] Should we show account balance (read-only)?
- [ ] Is account status needed?
- [ ] Do we need account opening date?

### **Service Details:**
- [ ] Is service_category required?
- [ ] Do we need service_subcategories?
- [ ] Is preferred_completion_date needed?
- [ ] Should we have special_instructions and reason_for_request?

### **Deposit-Specific Fields:**
- [ ] Which deposit types are applicable? (Cash, Check, Wire, etc.)
- [ ] Do we need check-specific fields (check_number, check_bank, etc.)?
- [ ] Do we need wire-specific fields (wire_reference, wire_originator, etc.)?
- [ ] Is deposit_exchange_rate needed?

### **Financial Information:**
- [ ] Is source_of_funds mandatory?
- [ ] Do we need transaction_amount separate from service_amount?
- [ ] Should we track fees?
- [ ] Is beneficiary information needed?

### **Compliance:**
- [ ] Are KYC/AML fields mandatory?
- [ ] Should risk_level be auto-calculated or manual?
- [ ] Do we need compliance_notes field?

### **Documents:**
- [ ] Which documents are mandatory?
- [ ] Should deposit_slip be required for cash/check deposits?
- [ ] Do we need check_image and wire_confirmation uploads?
- [ ] What file size limits?

### **Service Delivery:**
- [ ] Is preferred_delivery_method needed?
- [ ] Should we collect delivery_address?
- [ ] Do we need delivery_date and delivery_time?

---

## 📌 RECOMMENDED MINIMUM IMPLEMENTATION (High + Medium Priority)

### **Total: ~27 additional fields**

This would bring the total to **39 fields** (12 current + 27 new), which covers:
- ✅ Complete customer information
- ✅ Essential deposit details
- ✅ Basic compliance requirements
- ✅ Required document uploads
- ✅ Service delivery preferences

---

## 🔄 NEXT STEPS

1. **Review this list** - Check all fields against the Excel file
2. **Confirm priorities** - Mark which fields are High/Medium/Low priority
3. **Verify conditional logic** - Confirm which fields should show/hide based on selections
4. **Approve field list** - Give final approval before I update the seeder
5. **Update seeder** - I'll add all approved fields to FormManagementSeeder.php

---

## 📎 NOTES

- Fields marked with ⚠️ are **not yet implemented**
- Fields marked with ✅ are **currently implemented**
- Conditional fields will automatically show/hide based on user selections
- File upload fields will have appropriate validation and size limits
- All currency fields will default to MYR unless specified otherwise

---

**Please review this list and let me know:**
1. Which fields to add (High/Medium/Low priority)
2. Which fields to skip
3. Any additional fields from the Excel file not listed here
4. Any changes to field types or options

