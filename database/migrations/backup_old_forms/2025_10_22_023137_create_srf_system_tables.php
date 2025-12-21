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
        // 1. Create Service Request Forms Table (Main Table)
        Schema::create('service_request_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('request_number')->unique(); // SRF-YYYY-XXXXXX format
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->string('version')->default('16.0'); // Based on v16.0 in filename
            $table->enum('service_type', ['deposit', 'withdrawal', 'transfer', 'account_opening', 'account_closure', 'other'])->default('deposit');
            
            // Customer Information
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email');
            $table->text('customer_address');
            $table->string('customer_city');
            $table->string('customer_state');
            $table->string('customer_postal_code');
            $table->string('customer_country');
            $table->string('customer_id_type'); // Passport, National ID, Driver's License, etc.
            $table->string('customer_id_number');
            $table->date('customer_id_expiry_date')->nullable();
            $table->date('customer_dob')->nullable();
            $table->string('customer_gender')->nullable();
            $table->string('customer_nationality')->nullable();
            $table->string('customer_occupation')->nullable();
            $table->string('customer_employer')->nullable();
            $table->text('customer_employer_address')->nullable();
            $table->string('customer_annual_income')->nullable();
            $table->string('customer_marital_status')->nullable();
            
            // Account Information (if applicable)
            $table->string('account_number')->nullable();
            $table->string('account_type')->nullable(); // Savings, Checking, Business, etc.
            $table->string('account_currency', 3)->default('USD');
            $table->decimal('account_balance', 15, 2)->nullable();
            $table->date('account_opening_date')->nullable();
            $table->string('account_status')->nullable();
            $table->text('account_notes')->nullable();
            
            // Service Request Details
            $table->text('service_description'); // Detailed description of service requested
            $table->enum('service_category', [
                'banking', 'investment', 'insurance', 'loan', 'credit_card',
                'foreign_exchange', 'international_transfer', 'other'
            ]);
            $table->json('service_subcategories')->nullable(); // Specific service types
            $table->decimal('service_amount', 15, 2)->nullable(); // Amount involved in service
            $table->string('service_currency', 3)->default('USD');
            $table->enum('urgency_level', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->date('preferred_completion_date')->nullable();
            $table->text('special_instructions')->nullable();
            $table->text('reason_for_request')->nullable();
            
            // Deposit Specific Fields (based on DEPOSIT in filename)
            $table->enum('deposit_type', [
                'cash', 'check', 'wire_transfer', 'ach_transfer', 'mobile_deposit',
                'atm_deposit', 'in_person', 'online', 'other'
            ])->nullable();
            $table->string('deposit_method')->nullable();
            $table->string('deposit_source')->nullable(); // Where funds are coming from
            $table->text('deposit_source_details')->nullable();
            $table->string('check_number')->nullable(); // If deposit is by check
            $table->string('check_bank')->nullable();
            $table->string('check_account')->nullable();
            $table->date('check_date')->nullable();
            $table->string('wire_reference')->nullable(); // If deposit is by wire
            $table->string('wire_originator')->nullable();
            $table->string('wire_beneficiary')->nullable();
            $table->text('deposit_notes')->nullable();
            
            // Financial Information
            $table->decimal('transaction_amount', 15, 2)->nullable();
            $table->string('transaction_currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 4)->nullable();
            $table->decimal('fees', 10, 2)->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->text('payment_details')->nullable();
            
            // Compliance and Risk Assessment
            $table->boolean('aml_verified')->default(false); // Anti-Money Laundering
            $table->boolean('kyc_verified')->default(false); // Know Your Customer
            $table->boolean('sanctions_checked')->default(false);
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->text('risk_assessment_notes')->nullable();
            $table->text('compliance_notes')->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->text('approval_reason')->nullable();
            
            // Supporting Documents
            $table->json('supporting_documents')->nullable(); // Array of document paths
            $table->string('identity_document_path')->nullable();
            $table->string('proof_of_address_path')->nullable();
            $table->string('proof_of_income_path')->nullable();
            $table->string('bank_statement_path')->nullable();
            $table->string('deposit_slip_path')->nullable();
            $table->string('check_image_path')->nullable();
            $table->string('wire_confirmation_path')->nullable();
            $table->string('other_documents_path')->nullable();
            
            // Processing Information
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Service Delivery
            $table->enum('delivery_method', ['in_person', 'online', 'phone', 'email', 'mail'])->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('delivery_contact')->nullable();
            $table->string('delivery_phone')->nullable();
            $table->date('delivery_date')->nullable();
            $table->time('delivery_time')->nullable();
            $table->boolean('delivery_confirmed')->default(false);
            $table->timestamp('delivery_confirmed_at')->nullable();
            
            // Tracking and Audit
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('form_data')->nullable(); // Complete form data as JSON backup
            $table->json('audit_trail')->nullable(); // Track all changes and actions
            $table->json('communication_log')->nullable(); // Track all communications
            $table->json('service_log')->nullable(); // Track service delivery steps
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('request_number');
            $table->index('status');
            $table->index('service_type');
            $table->index('service_category');
            $table->index('customer_name');
            $table->index('account_number');
            $table->index('submitted_at');
            $table->index('assigned_to');
            $table->index('reviewed_by');
            $table->index('approved_by');
            $table->index('preferred_completion_date');
            $table->index('created_at');
        });

        // 2. Create SRF Form Fields Table (Dynamic Form Structure)
        Schema::create('srf_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('srf_form_id')->constrained('service_request_forms')->onDelete('cascade');
            $table->string('field_section'); // customer_info, service_details, financial_info, etc.
            $table->string('field_name'); // Unique identifier within the form
            $table->string('field_label');
            $table->text('field_description')->nullable();
            $table->enum('field_type', [
                'text', 'email', 'phone', 'number', 'textarea', 'select', 
                'radio', 'checkbox', 'date', 'file', 'signature', 'currency',
                'multiselect', 'time', 'datetime'
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
            
            $table->index('srf_form_id');
            $table->index('field_section');
            $table->index('field_type');
            $table->index('sort_order');
            $table->index('is_active');
        });

        // 3. Create SRF Form Submissions Table (Submission Tracking)
        Schema::create('srf_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('srf_form_id')->constrained('service_request_forms')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
            $table->string('submission_token')->unique();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'])->default('draft');
            
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
            $table->json('compliance_checks')->nullable(); // Compliance verification checks
            $table->text('internal_notes')->nullable();
            
            $table->timestamps();
            
            $table->index('srf_form_id');
            $table->index('user_id');
            $table->index('branch_id');
            $table->index('submission_token');
            $table->index('status');
            $table->index('submitted_at');
            $table->index('reviewed_by');
        });

        // 4. Create SRF Service Actions Table (Track service delivery steps)
        Schema::create('srf_service_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('srf_submission_id')->constrained('srf_form_submissions')->onDelete('cascade');
            $table->string('action_type'); // account_opening, deposit_processing, verification, etc.
            $table->text('action_description');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('action_notes')->nullable();
            $table->json('action_data')->nullable(); // Additional action-specific data
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->json('attachments')->nullable(); // Files related to this action
            $table->timestamps();
            
            $table->index('srf_submission_id');
            $table->index('action_type');
            $table->index('status');
            $table->index('performed_by');
            $table->index('started_at');
        });

        // 5. Create SRF Service History Table (Track service history and changes)
        Schema::create('srf_service_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('srf_submission_id')->constrained('srf_form_submissions')->onDelete('cascade');
            $table->string('event_type'); // status_change, assignment, review, approval, etc.
            $table->text('event_description');
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('performed_at');
            $table->text('event_notes')->nullable();
            $table->json('event_data')->nullable(); // Additional event-specific data
            $table->timestamps();
            
            $table->index('srf_submission_id');
            $table->index('event_type');
            $table->index('performed_by');
            $table->index('performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order due to foreign key constraints
        Schema::dropIfExists('srf_service_history');
        Schema::dropIfExists('srf_service_actions');
        Schema::dropIfExists('srf_form_submissions');
        Schema::dropIfExists('srf_form_fields');
        Schema::dropIfExists('service_request_forms');
    }
};