<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Data Access Request Forms Table (Main Table)
        Schema::create('data_access_request_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('request_number')->unique(); // DAR-YYYY-XXXXXX format
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed', 'expired'])->default('draft');
            $table->string('version')->default('1.0');
            
            // Requester Information
            $table->string('requester_name');
            $table->string('requester_phone');
            $table->string('requester_email');
            $table->text('requester_address');
            $table->string('requester_city');
            $table->string('requester_state');
            $table->string('requester_postal_code');
            $table->string('requester_country');
            $table->string('requester_id_type'); // Passport, National ID, Driver's License, etc.
            $table->string('requester_id_number');
            $table->date('requester_id_expiry_date')->nullable();
            $table->string('requester_organization')->nullable(); // Company/Organization name
            $table->string('requester_position')->nullable(); // Job title/position
            $table->string('requester_organization_type')->nullable(); // Government, Private, NGO, etc.
            
            // Data Subject Information (Person whose data is being requested)
            $table->string('data_subject_name');
            $table->string('data_subject_phone')->nullable();
            $table->string('data_subject_email')->nullable();
            $table->text('data_subject_address')->nullable();
            $table->string('data_subject_city')->nullable();
            $table->string('data_subject_state')->nullable();
            $table->string('data_subject_postal_code')->nullable();
            $table->string('data_subject_country')->nullable();
            $table->string('data_subject_id_type')->nullable();
            $table->string('data_subject_id_number')->nullable();
            $table->date('data_subject_id_expiry_date')->nullable();
            $table->string('relationship_to_data_subject'); // Self, Legal Guardian, Authorized Representative, etc.
            $table->text('data_subject_authorization_document_path')->nullable(); // If acting on behalf of someone
            
            // Data Access Request Details
            $table->enum('request_type', [
                'access', 'rectification', 'erasure', 'portability', 
                'restriction', 'objection', 'complaint', 'other'
            ]);
            $table->text('request_description'); // Detailed description of what data is being requested
            $table->json('data_categories')->nullable(); // Categories of personal data requested
            $table->json('data_sources')->nullable(); // Where the data is stored/processed
            $table->date('data_period_from')->nullable(); // Time period of data
            $table->date('data_period_to')->nullable();
            $table->text('specific_data_items')->nullable(); // Specific data fields requested
            $table->enum('urgency_level', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('justification')->nullable(); // Why this data is needed
            
            // Legal Basis and Compliance
            $table->enum('legal_basis', [
                'consent', 'contract', 'legal_obligation', 'vital_interests',
                'public_task', 'legitimate_interests', 'other'
            ]);
            $table->text('legal_basis_description')->nullable();
            $table->boolean('consent_obtained')->default(false);
            $table->date('consent_date')->nullable();
            $table->text('consent_method')->nullable(); // Written, verbal, electronic, etc.
            $table->boolean('gdpr_applicable')->default(false);
            $table->boolean('ccpa_applicable')->default(false);
            $table->boolean('other_privacy_law_applicable')->default(false);
            $table->text('applicable_privacy_laws')->nullable();
            
            // Data Processing Information
            $table->json('data_controllers')->nullable(); // Who controls the data
            $table->json('data_processors')->nullable(); // Who processes the data
            $table->json('data_retention_periods')->nullable(); // How long data is kept
            $table->text('data_security_measures')->nullable(); // Security measures in place
            $table->boolean('data_transferred_third_countries')->default(false);
            $table->json('third_countries_list')->nullable(); // List of countries data is transferred to
            $table->text('safeguards_description')->nullable(); // Safeguards for international transfers
            
            // Supporting Documents
            $table->json('supporting_documents')->nullable(); // Array of document paths
            $table->string('identity_document_path')->nullable();
            $table->string('authorization_document_path')->nullable();
            $table->string('proof_of_relationship_path')->nullable();
            $table->string('legal_basis_document_path')->nullable();
            $table->string('consent_document_path')->nullable();
            $table->string('other_documents_path')->nullable();
            
            // Processing and Response
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->date('response_deadline')->nullable(); // Legal deadline for response
            $table->text('response_summary')->nullable(); // Summary of response provided
            $table->text('data_provided_summary')->nullable(); // What data was provided
            $table->text('rejection_reason')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Compliance and Verification
            $table->boolean('identity_verified')->default(false);
            $table->boolean('authorization_verified')->default(false);
            $table->boolean('legal_basis_verified')->default(false);
            $table->boolean('data_existence_confirmed')->default(false);
            $table->text('verification_notes')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->text('compliance_notes')->nullable();
            
            // Tracking and Audit
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('form_data')->nullable(); // Complete form data as JSON backup
            $table->json('audit_trail')->nullable(); // Track all changes and actions
            $table->json('communication_log')->nullable(); // Track all communications
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('request_number');
            $table->index('status');
            $table->index('request_type');
            $table->index('data_subject_name');
            $table->index('submitted_at');
            $table->index('assigned_to');
            $table->index('reviewed_by');
            $table->index('response_deadline');
            $table->index('created_at');
        });

        // 2. Create DAR Form Fields Table (Dynamic Form Structure)
        Schema::create('dar_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dar_form_id')->constrained('data_access_request_forms')->onDelete('cascade');
            $table->string('field_section'); // requester_info, data_subject_info, request_details, etc.
            $table->string('field_name'); // Unique identifier within the form
            $table->string('field_label');
            $table->text('field_description')->nullable();
            $table->enum('field_type', [
                'text', 'email', 'phone', 'number', 'textarea', 'select', 
                'radio', 'checkbox', 'date', 'file', 'signature', 'multiselect'
            ]);
            $table->string('field_placeholder')->nullable();
            $table->text('field_help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_conditional')->default(false);
            $table->json('conditional_logic')->nullable(); // Show/hide based on other fields
            $table->json('validation_rules')->nullable(); // Custom validation rules
            $table->json('field_options')->nullable(); // For select, radio, checkbox options
            $table->json('field_settings')->nullable(); // Additional field-specific settings
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('css_class')->nullable();
            $table->json('custom_attributes')->nullable();
            $table->timestamps();
            
            $table->index('dar_form_id');
            $table->index('field_section');
            $table->index('field_type');
            $table->index('sort_order');
            $table->index('is_active');
        });

        // 3. Create DAR Form Submissions Table (Submission Tracking)
        Schema::create('dar_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dar_form_id')->constrained('data_access_request_forms')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
            $table->string('submission_token')->unique();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed', 'expired'])->default('draft');
            
            // Submission Data
            $table->json('submission_data')->nullable(); // Complete form submission data
            $table->json('field_responses')->nullable(); // Individual field responses
            $table->json('file_uploads')->nullable(); // File upload information
            
            // Tracking Information
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('last_modified_at')->nullable();
            
            // Processing Information
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Audit Trail
            $table->json('audit_trail')->nullable(); // Track all changes and actions
            $table->json('compliance_checks')->nullable(); // Privacy law compliance checks
            $table->text('internal_notes')->nullable();
            
            $table->timestamps();
            
            $table->index('dar_form_id');
            $table->index('user_id');
            $table->index('branch_id');
            $table->index('submission_token');
            $table->index('status');
            $table->index('submitted_at');
            $table->index('reviewed_by');
        });

        // 4. Create DAR Response Data Table (Data provided in response)
        Schema::create('dar_response_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dar_submission_id')->constrained('dar_form_submissions')->onDelete('cascade');
            $table->string('data_category'); // Personal data, contact info, financial data, etc.
            $table->string('data_field'); // Specific field name
            $table->text('data_value')->nullable(); // The actual data value
            $table->string('data_source')->nullable(); // Where this data came from
            $table->string('data_format')->nullable(); // JSON, CSV, PDF, etc.
            $table->string('file_path')->nullable(); // If data is in a file
            $table->boolean('is_sensitive')->default(false); // Mark sensitive data
            $table->text('redaction_notes')->nullable(); // Notes about any redactions
            $table->json('metadata')->nullable(); // Additional metadata about the data
            $table->timestamp('provided_at')->nullable();
            $table->timestamps();
            
            $table->index('dar_submission_id');
            $table->index('data_category');
            $table->index('data_field');
            $table->index('is_sensitive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order due to foreign key constraints
        Schema::dropIfExists('dar_response_data');
        Schema::dropIfExists('dar_form_submissions');
        Schema::dropIfExists('dar_form_fields');
        Schema::dropIfExists('data_access_request_forms');
    }
};