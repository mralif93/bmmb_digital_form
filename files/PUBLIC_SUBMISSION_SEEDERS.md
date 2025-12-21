# Public Form Submission Seeders

## ✅ Created Seeders

Created 4 new seeders for public form submissions (at least 5 data entries for each form):

1. **PublicRafSubmissionSeeder** - Creates 5 public RAF submissions
2. **PublicDarSubmissionSeeder** - Creates 5 public DAR submissions
3. **PublicDcrSubmissionSeeder** - Creates 5 public DCR submissions
4. **PublicSrfSubmissionSeeder** - Creates 5 public SRF submissions

---

## 📊 Key Characteristics

### Public Submissions vs Admin Submissions

**Public Submissions:**
- ✅ `user_id` = `null` (no user login required)
- ✅ `status` = `'submitted'` (always submitted)
- ✅ `reviewed_by` = `null` (not yet reviewed)
- ✅ `reviewed_at` = `null`
- ✅ May have `branch_id` (if accessed via QR code)
- ✅ Realistic form data matching public form fields
- ✅ Source marked as `'public_form'` in audit trail

**Admin Submissions (existing seeders):**
- `user_id` = assigned user
- Various statuses (draft, submitted, approved, etc.)
- May have reviewer assigned
- More complex data structures

---

## 🚀 How to Run

### Run Individual Seeders

```bash
# Run RAF public submissions
php artisan db:seed --class=PublicRafSubmissionSeeder

# Run DAR public submissions
php artisan db:seed --class=PublicDarSubmissionSeeder

# Run DCR public submissions
php artisan db:seed --class=PublicDcrSubmissionSeeder

# Run SRF public submissions
php artisan db:seed --class=PublicSrfSubmissionSeeder
```

### Run All Public Submissions

```bash
php artisan db:seed --class=PublicRafSubmissionSeeder
php artisan db:seed --class=PublicDarSubmissionSeeder
php artisan db:seed --class=PublicDcrSubmissionSeeder
php artisan db:seed --class=PublicSrfSubmissionSeeder
```

### Run via DatabaseSeeder (Optional)

The seeders are commented out in `DatabaseSeeder.php` by default. To include them:

```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    $this->call([
        // ... other seeders
        PublicRafSubmissionSeeder::class,
        PublicDarSubmissionSeeder::class,
        PublicDcrSubmissionSeeder::class,
        PublicSrfSubmissionSeeder::class,
    ]);
}
```

---

## 📝 Form Data Structure

### RAF (Remittance Application Form)
- Applicant information (name, email, phone, ID, address)
- Remittance details (amount, currency, purpose, frequency)
- Payment method and source
- Beneficiary information (name, relationship, contact, address)

### DAR (Data Access Request Form)
- Requester information
- Data subject information
- Organization details (if applicable)
- Request type and data categories
- Timeframe and urgency level

### DCR (Data Correction Request Form)
- Requester information
- Data subject information
- Correction type and details
- Incorrect vs correct data
- Priority and impact description

### SRF (Service Request Form)
- Customer information
- Account details
- Service type and description
- Requested date and preferred time
- Contact preference and priority

---

## ✅ Verification

To verify public submissions were created:

```bash
php artisan tinker
```

Then run:
```php
// Check RAF public submissions
RafFormSubmission::whereNull('user_id')->count(); // Should be 5

// Check DAR public submissions
DarFormSubmission::whereNull('user_id')->count(); // Should be 5

// Check DCR public submissions
DcrFormSubmission::whereNull('user_id')->count(); // Should be 5

// Check SRF public submissions
SrfFormSubmission::whereNull('user_id')->count(); // Should be 5
```

---

## 🎯 Features

### Realistic Data
- Uses Faker for realistic names, emails, addresses, etc.
- Matches actual form field structures
- Includes all required fields from public forms

### Branch Linking
- Randomly assigns branch_id (50% chance)
- Simulates QR code access flow
- Some submissions will have branch_id, others won't

### Timestamps
- `started_at`: Random time within last 30 days
- `submitted_at`: 10-60 minutes after started_at
- Uses system timezone (via `UsesSystemTimezone` trait)

### Submission Tokens
- Unique tokens generated using `Str::random(32)`
- Format: `{random32chars}-{timestamp}_{index}`
- Ensures uniqueness across all submissions

### Audit Trail
- Records creation and submission actions
- Marks source as `'public_form'`
- Includes timestamps in ISO format

---

## 📌 Summary

**Total Public Submissions Created:**
- ✅ 5 RAF submissions
- ✅ 5 DAR submissions
- ✅ 5 DCR submissions
- ✅ 5 SRF submissions
- **Total: 20 public submissions**

**All submissions:**
- Have `user_id = null` (public users)
- Have `status = 'submitted'`
- Include realistic form data
- May have branch linking (QR code flow)
- Are ready for admin review

