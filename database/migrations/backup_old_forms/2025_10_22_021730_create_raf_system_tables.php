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
        // 1. Create Remittance Application Forms Table (Main Table)
        Schema::create('remittance_application_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('application_number')->unique(); // RAF-YYYY-XXXXXX format
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed'])->default('draft');
            $table->string('version')->default('5.0'); // Based on V5.0 in filename
            
            // Applicant Information
            $table->string('applicant_name');
            $table->string('applicant_phone');
            $table->string('applicant_email');
            $table->text('applicant_address');
            $table->string('applicant_city');
            $table->string('applicant_state');
            $table->string('applicant_postal_code');
            $table->string('applicant_country');
            $table->string('applicant_id_type'); // Passport, National ID, etc.
            $table->string('applicant_id_number');
            $table->date('applicant_id_expiry_date')->nullable();
            
            // Remittance Details
            $table->decimal('remittance_amount', 15, 2); // Amount to be remitted
            $table->string('remittance_currency', 3); // USD, EUR, etc.
            $table->string('remittance_purpose'); // Family support, education, medical, etc.
            $table->text('remittance_purpose_description')->nullable();
            $table->string('remittance_frequency'); // One-time, monthly, quarterly, etc.
            
            // Beneficiary Information
            $table->string('beneficiary_name');
            $table->string('beneficiary_relationship'); // Spouse, parent, child, etc.
            $table->text('beneficiary_address');
            $table->string('beneficiary_city');
            $table->string('beneficiary_state');
            $table->string('beneficiary_postal_code');
            $table->string('beneficiary_country');
            $table->string('beneficiary_phone')->nullable();
            $table->string('beneficiary_email')->nullable();
            $table->string('beneficiary_bank_name')->nullable();
            $table->string('beneficiary_bank_account')->nullable();
            $table->string('beneficiary_bank_routing')->nullable();
            $table->string('beneficiary_bank_swift')->nullable();
            
            // Payment Information
            $table->string('payment_method'); // Bank transfer, cash, check, etc.
            $table->string('payment_source'); // Personal savings, salary, etc.
            $table->string('payment_currency', 3);
            $table->decimal('exchange_rate', 10, 4)->nullable();
            $table->decimal('service_fee', 10, 2)->nullable();
            $table->decimal('total_amount', 15, 2); // Total including fees
            
            // Supporting Documents
            $table->json('supporting_documents')->nullable(); // Array of document paths
            $table->string('id_document_path')->nullable();
            $table->string('proof_of_income_path')->nullable();
            $table->string('beneficiary_id_path')->nullable();
            $table->string('bank_statement_path')->nullable();
            $table->string('purpose_document_path')->nullable();
            
            // Compliance and Verification
            $table->boolean('aml_verified')->default(false); // Anti-Money Laundering
            $table->boolean('kyc_verified')->default(false); // Know Your Customer
            $table->boolean('sanctions_checked')->default(false);
            $table->text('compliance_notes')->nullable();
            $table->string('risk_level')->default('low'); // low, medium, high
            
            // Processing Information
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Tracking and Audit
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('form_data')->nullable(); // Complete form data as JSON backup
            $table->json('audit_trail')->nullable(); // Track all changes and actions
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('application_number');
            $table->index('status');
            $table->index('submitted_at');
            $table->index('processed_by');
            $table->index('remittance_amount');
            $table->index('remittance_currency');
            $table->index('beneficiary_country');
            $table->index('created_at');
        });

        // 2. Create RAF Form Fields Table (Dynamic Form Structure)
        Schema::create('raf_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raf_form_id')->constrained('remittance_application_forms')->onDelete('cascade');
            $table->string('field_section'); // applicant_info, remittance_details, beneficiary_info, etc.
            $table->string('field_name'); // Unique identifier within the form
            $table->string('field_label');
            $table->text('field_description')->nullable();
            $table->enum('field_type', [
                'text', 'email', 'phone', 'number', 'textarea', 'select', 
                'radio', 'checkbox', 'date', 'file', 'signature', 'currency'
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
            
            $table->index('raf_form_id');
            $table->index('field_section');
            $table->index('field_type');
            $table->index('sort_order');
            $table->index('is_active');
        });

        // 3. Create RAF Form Submissions Table (Submission Tracking)
        Schema::create('raf_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raf_form_id')->constrained('remittance_application_forms')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
            $table->string('submission_token')->unique();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed'])->default('draft');
            
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
            $table->json('compliance_checks')->nullable(); // AML, KYC, sanctions checks
            $table->text('internal_notes')->nullable();
            
            $table->timestamps();
            
            $table->index('raf_form_id');
            $table->index('user_id');
            $table->index('branch_id');
            $table->index('submission_token');
            $table->index('status');
            $table->index('submitted_at');
            $table->index('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order due to foreign key constraints
        Schema::dropIfExists('raf_form_submissions');
        Schema::dropIfExists('raf_form_fields');
        Schema::dropIfExists('remittance_application_forms');
    }
};