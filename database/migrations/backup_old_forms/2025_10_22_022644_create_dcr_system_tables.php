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
        // 1. Create Data Correction Request Forms Table (Main Table)
        Schema::create('data_correction_request_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('request_number')->unique(); // DCR-YYYY-XXXXXX format
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed', 'partially_approved'])->default('draft');
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
            
            // Data Subject Information (Person whose data needs correction)
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
            
            // Data Correction Request Details
            $table->enum('correction_type', [
                'personal_info', 'contact_info', 'financial_info', 'demographic_info',
                'preferences', 'account_info', 'transaction_data', 'other'
            ]);
            $table->text('correction_description'); // Detailed description of what needs to be corrected
            $table->json('incorrect_data_items')->nullable(); // Current incorrect data
            $table->json('corrected_data_items')->nullable(); // What the data should be
            $table->json('data_sources')->nullable(); // Where the incorrect data is stored
            $table->date('data_period_from')->nullable(); // Time period of incorrect data
            $table->date('data_period_to')->nullable();
            $table->text('reason_for_correction')->nullable(); // Why correction is needed
            $table->enum('urgency_level', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('impact_description')->nullable(); // How incorrect data affects the person
            
            // Legal Basis and Compliance
            $table->enum('legal_basis', [
                'consent', 'contract', 'legal_obligation', 'vital_interests',
                'public_task', 'legitimate_interests', 'data_accuracy_obligation', 'other'
            ]);
            $table->text('legal_basis_description')->nullable();
            $table->boolean('consent_obtained')->default(false);
            $table->date('consent_date')->nullable();
            $table->text('consent_method')->nullable(); // Written, verbal, electronic, etc.
            $table->boolean('gdpr_applicable')->default(false);
            $table->boolean('ccpa_applicable')->default(false);
            $table->boolean('other_privacy_law_applicable')->default(false);
            $table->text('applicable_privacy_laws')->nullable();
            
            // Data Accuracy and Verification
            $table->json('verification_documents')->nullable(); // Documents proving correct data
            $table->text('verification_method')->nullable(); // How accuracy will be verified
            $table->boolean('third_party_verification_required')->default(false);
            $table->text('third_party_verification_details')->nullable();
            $table->boolean('data_source_verification_required')->default(false);
            $table->text('data_source_verification_details')->nullable();
            $table->enum('verification_status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->text('verification_notes')->nullable();
            
            // Correction Implementation
            $table->json('affected_systems')->nullable(); // Systems where data needs correction
            $table->json('correction_actions')->nullable(); // Specific actions to be taken
            $table->text('implementation_plan')->nullable(); // How corrections will be implemented
            $table->date('target_correction_date')->nullable(); // When corrections should be completed
            $table->boolean('notify_third_parties')->default(false);
            $table->json('third_parties_to_notify')->nullable(); // Who needs to be notified
            $table->text('notification_method')->nullable(); // How third parties will be notified
            
            // Supporting Documents
            $table->json('supporting_documents')->nullable(); // Array of document paths
            $table->string('identity_document_path')->nullable();
            $table->string('proof_of_correct_data_path')->nullable();
            $table->string('authorization_document_path')->nullable();
            $table->string('verification_document_path')->nullable();
            $table->string('legal_basis_document_path')->nullable();
            $table->string('other_documents_path')->nullable();
            
            // Processing and Response
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('correction_started_at')->nullable();
            $table->timestamp('correction_completed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->date('response_deadline')->nullable(); // Legal deadline for response
            $table->text('response_summary')->nullable(); // Summary of response provided
            $table->text('correction_summary')->nullable(); // What corrections were made
            $table->text('rejection_reason')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Compliance and Verification
            $table->boolean('identity_verified')->default(false);
            $table->boolean('authorization_verified')->default(false);
            $table->boolean('legal_basis_verified')->default(false);
            $table->boolean('data_accuracy_verified')->default(false);
            $table->boolean('correction_feasible')->default(true);
            $table->text('feasibility_notes')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->text('compliance_notes')->nullable();
            
            // Tracking and Audit
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('form_data')->nullable(); // Complete form data as JSON backup
            $table->json('audit_trail')->nullable(); // Track all changes and actions
            $table->json('communication_log')->nullable(); // Track all communications
            $table->json('correction_log')->nullable(); // Track correction implementation
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('request_number');
            $table->index('status');
            $table->index('correction_type');
            $table->index('data_subject_name');
            $table->index('submitted_at');
            $table->index('assigned_to');
            $table->index('reviewed_by');
            $table->index('response_deadline');
            $table->index('verification_status');
            $table->index('created_at');
        });

        // 2. Create DCR Form Fields Table (Dynamic Form Structure)
        Schema::create('dcr_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dcr_form_id')->constrained('data_correction_request_forms')->onDelete('cascade');
            $table->string('field_section'); // requester_info, data_subject_info, correction_details, etc.
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
            
            $table->index('dcr_form_id');
            $table->index('field_section');
            $table->index('field_type');
            $table->index('sort_order');
            $table->index('is_active');
        });

        // 3. Create DCR Form Submissions Table (Submission Tracking)
        Schema::create('dcr_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dcr_form_id')->constrained('data_correction_request_forms')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
            $table->string('submission_token')->unique();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed', 'partially_approved'])->default('draft');
            
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
            
            $table->index('dcr_form_id');
            $table->index('user_id');
            $table->index('branch_id');
            $table->index('submission_token');
            $table->index('status');
            $table->index('submitted_at');
            $table->index('reviewed_by');
        });

        // 4. Create DCR Correction Actions Table (Track specific corrections made)
        Schema::create('dcr_correction_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dcr_submission_id')->constrained('dcr_form_submissions')->onDelete('cascade');
            $table->string('action_type'); // update, delete, add, modify, etc.
            $table->string('data_field'); // Specific field that was corrected
            $table->text('old_value')->nullable(); // Previous incorrect value
            $table->text('new_value')->nullable(); // New correct value
            $table->string('data_source')->nullable(); // Where the correction was made
            $table->string('system_affected')->nullable(); // Which system was updated
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->text('implementation_notes')->nullable();
            $table->foreignId('implemented_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('implemented_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index('dcr_submission_id');
            $table->index('action_type');
            $table->index('data_field');
            $table->index('status');
            $table->index('implemented_by');
            $table->index('verified_by');
        });

        // 5. Create DCR Verification Records Table (Track verification process)
        Schema::create('dcr_verification_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dcr_submission_id')->constrained('dcr_form_submissions')->onDelete('cascade');
            $table->string('verification_type'); // document_verification, third_party_verification, etc.
            $table->text('verification_description');
            $table->json('verification_documents')->nullable(); // Documents used for verification
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->text('verification_result')->nullable();
            $table->text('verification_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->json('metadata')->nullable(); // Additional verification metadata
            $table->timestamps();
            
            $table->index('dcr_submission_id');
            $table->index('verification_type');
            $table->index('status');
            $table->index('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order due to foreign key constraints
        Schema::dropIfExists('dcr_verification_records');
        Schema::dropIfExists('dcr_correction_actions');
        Schema::dropIfExists('dcr_form_submissions');
        Schema::dropIfExists('dcr_form_fields');
        Schema::dropIfExists('data_correction_request_forms');
    }
};