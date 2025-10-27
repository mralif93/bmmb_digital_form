# 🗄️ Digital Form System - Database Structure

## 📋 Overview

This document explains the comprehensive database structure for a digital form system that allows users to create multiple forms with various components and field types.

## 🏗️ Database Schema

### 1. **Users Table** - Authentication & Authorization
```sql
users
├── id (Primary Key)
├── name (User's full name)
├── email (Unique email address)
├── email_verified_at (Email verification timestamp)
├── password (Hashed password)
├── role (admin, user, viewer)
├── avatar (Profile picture path)
├── is_active (Account status)
└── timestamps
```

### 2. **Forms Table** - Main Form Container
```sql
forms
├── id (Primary Key)
├── user_id (Foreign Key → users.id)
├── title (Form title)
├── description (Form description)
├── slug (Unique URL slug)
├── status (draft, published, archived)
├── settings (JSON - Form-level settings)
├── theme_settings (JSON - Custom styling)
├── notification_settings (JSON - Email notifications)
├── submission_limit (Max submissions allowed)
├── is_public (Public/private form)
├── requires_authentication (Login required)
├── allow_multiple_submissions (Multiple submissions allowed)
├── published_at (Publication timestamp)
├── expires_at (Expiration timestamp)
└── timestamps
```

### 3. **Form Fields Table** - Dynamic Form Components
```sql
form_fields
├── id (Primary Key)
├── form_id (Foreign Key → forms.id)
├── field_type (text, email, select, radio, checkbox, file, etc.)
├── field_name (Unique field identifier)
├── field_label (Display label)
├── field_placeholder (Placeholder text)
├── field_description (Help text)
├── is_required (Required field)
├── is_conditional (Conditional display)
├── conditional_logic (JSON - Show/hide rules)
├── validation_rules (JSON - Custom validation)
├── field_options (JSON - Options for select/radio/checkbox)
├── field_settings (JSON - Additional settings)
├── sort_order (Display order)
├── is_active (Field status)
└── timestamps
```

### 4. **Form Submissions Table** - Response Container
```sql
form_submissions
├── id (Primary Key)
├── form_id (Foreign Key → forms.id)
├── user_id (Foreign Key → users.id, nullable for anonymous)
├── submission_token (Unique submission identifier)
├── ip_address (Submitter's IP)
├── user_agent (Browser information)
├── status (pending, completed, failed)
├── submitted_at (Submission timestamp)
└── timestamps
```

### 5. **Form Submission Data Table** - Field Values
```sql
form_submission_data
├── id (Primary Key)
├── submission_id (Foreign Key → form_submissions.id)
├── field_id (Foreign Key → form_fields.id)
├── field_value (Submitted value)
├── file_path (File upload path)
└── created_at
```

## 🔧 Field Types Supported

### **Input Fields**
- `text` - Single line text input
- `email` - Email address input
- `number` - Numeric input
- `tel` - Telephone number input
- `url` - URL input
- `password` - Password input
- `textarea` - Multi-line text input
- `file` - File upload
- `date` - Date picker
- `time` - Time picker
- `datetime` - Date and time picker
- `range` - Slider input
- `color` - Color picker
- `hidden` - Hidden field

### **Choice Fields**
- `select` - Dropdown selection
- `radio` - Radio button group
- `checkbox` - Checkbox group

### **Display Fields**
- `heading` - Section heading
- `paragraph` - Text paragraph
- `divider` - Visual separator

## 📊 JSON Field Structures

### **Form Settings**
```json
{
  "redirect_url": "https://example.com/thank-you",
  "submit_button_text": "Submit Form",
  "success_message": "Thank you for your submission!",
  "confirmation_email": true,
  "auto_responder": {
    "enabled": true,
    "subject": "Thank you for your submission",
    "template": "email-template-id"
  }
}
```

### **Theme Settings**
```json
{
  "primary_color": "#3B82F6",
  "secondary_color": "#6B7280",
  "font_family": "Inter",
  "border_radius": "8px",
  "button_style": "rounded",
  "layout": "single-column"
}
```

### **Field Options (for select/radio/checkbox)**
```json
{
  "options": [
    {"value": "option1", "label": "Option 1"},
    {"value": "option2", "label": "Option 2"},
    {"value": "option3", "label": "Option 3"}
  ],
  "allow_other": true,
  "other_label": "Other (please specify)"
}
```

### **Conditional Logic**
```json
{
  "enabled": true,
  "conditions": [
    {
      "field_id": 5,
      "operator": "equals",
      "value": "yes",
      "action": "show"
    }
  ]
}
```

### **Validation Rules**
```json
{
  "min_length": 3,
  "max_length": 100,
  "pattern": "^[a-zA-Z0-9]+$",
  "custom_message": "Only alphanumeric characters allowed"
}
```

## 🔗 Model Relationships

### **Form Model**
```php
// Belongs to User
public function user(): BelongsTo

// Has many FormFields
public function fields(): HasMany

// Has many FormSubmissions
public function submissions(): HasMany

// Has many active FormFields
public function activeFields(): HasMany
```

### **FormField Model**
```php
// Belongs to Form
public function form(): BelongsTo

// Has many FormSubmissionData
public function submissionData(): HasMany
```

### **FormSubmission Model**
```php
// Belongs to Form
public function form(): BelongsTo

// Belongs to User (nullable)
public function user(): BelongsTo

// Has many FormSubmissionData
public function data(): HasMany
```

### **FormSubmissionData Model**
```php
// Belongs to FormSubmission
public function submission(): BelongsTo

// Belongs to FormField
public function field(): BelongsTo
```

## 🚀 Usage Examples

### **Creating a Form with Fields**
```php
// Create form
$form = Form::create([
    'user_id' => auth()->id(),
    'title' => 'Contact Form',
    'description' => 'Get in touch with us',
    'slug' => 'contact-form',
    'status' => 'published',
    'is_public' => true,
]);

// Add fields
$form->fields()->createMany([
    [
        'field_type' => 'text',
        'field_name' => 'name',
        'field_label' => 'Full Name',
        'is_required' => true,
        'sort_order' => 1,
    ],
    [
        'field_type' => 'email',
        'field_name' => 'email',
        'field_label' => 'Email Address',
        'is_required' => true,
        'sort_order' => 2,
    ],
    [
        'field_type' => 'select',
        'field_name' => 'subject',
        'field_label' => 'Subject',
        'field_options' => [
            'options' => [
                ['value' => 'general', 'label' => 'General Inquiry'],
                ['value' => 'support', 'label' => 'Technical Support'],
                ['value' => 'sales', 'label' => 'Sales Question'],
            ]
        ],
        'sort_order' => 3,
    ],
    [
        'field_type' => 'textarea',
        'field_name' => 'message',
        'field_label' => 'Message',
        'is_required' => true,
        'sort_order' => 4,
    ],
]);
```

### **Processing Form Submission**
```php
// Create submission
$submission = FormSubmission::create([
    'form_id' => $form->id,
    'user_id' => auth()->id(),
    'submission_token' => Str::uuid(),
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'status' => 'completed',
]);

// Save field data
foreach ($request->all() as $fieldName => $value) {
    $field = $form->fields()->where('field_name', $fieldName)->first();
    
    if ($field) {
        FormSubmissionData::create([
            'submission_id' => $submission->id,
            'field_id' => $field->id,
            'field_value' => is_array($value) ? json_encode($value) : $value,
        ]);
    }
}
```

### **Retrieving Form Data**
```php
// Get form with fields
$form = Form::with('activeFields')->find($id);

// Get submissions with data
$submissions = FormSubmission::with(['data.field'])
    ->where('form_id', $form->id)
    ->get();

// Get submission data as array
foreach ($submissions as $submission) {
    $data = $submission->getDataAsArray();
    // $data['name'], $data['email'], etc.
}
```

## 📈 Performance Considerations

### **Indexes**
- `forms`: user_id, status, slug
- `form_fields`: form_id, sort_order, field_type
- `form_submissions`: form_id, submitted_at, user_id
- `form_submission_data`: submission_id, field_id

### **Query Optimization**
- Use `with()` for eager loading relationships
- Add database indexes for frequently queried columns
- Consider pagination for large datasets
- Use JSON indexes for JSON column queries (MySQL 5.7+)

### **Storage Considerations**
- File uploads stored in `storage/app/public`
- Large JSON fields may impact performance
- Consider archiving old submissions
- Implement soft deletes for data retention

## 🔒 Security Considerations

### **Data Validation**
- Validate all form inputs server-side
- Sanitize user-generated content
- Implement CSRF protection
- Rate limiting for form submissions

### **File Uploads**
- Validate file types and sizes
- Store files outside web root
- Scan uploaded files for malware
- Generate unique filenames

### **Access Control**
- Implement proper authorization
- Check form permissions before access
- Validate user ownership of forms
- Secure API endpoints

## 🎯 Next Steps

1. **Run Migrations**: `php artisan migrate`
2. **Create Seeders**: Add sample data for testing
3. **Build Controllers**: Handle form CRUD operations
4. **Implement Validation**: Add form validation logic
5. **Create Views**: Build form builder interface
6. **Add API Endpoints**: For frontend integration
7. **Implement Analytics**: Track form performance
8. **Add Notifications**: Email confirmations and alerts

This database structure provides a solid foundation for a comprehensive digital form system that can handle complex forms with various field types, conditional logic, and detailed analytics.
