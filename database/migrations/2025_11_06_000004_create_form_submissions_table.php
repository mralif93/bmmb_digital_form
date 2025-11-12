<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Schema::getConnection();
        $driverName = $connection->getDriverName();
        
        // For SQLite, use raw SQL to handle CHECK constraints properly
        if ($driverName === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off;');
            
            DB::statement("
                CREATE TABLE form_submissions (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    form_id INTEGER NOT NULL,
                    user_id INTEGER,
                    branch_id INTEGER,
                    submission_token VARCHAR NOT NULL UNIQUE,
                    status VARCHAR CHECK (status IN ('draft', 'submitted', 'pending_process', 'under_review', 'approved', 'rejected', 'completed', 'expired', 'in_progress', 'cancelled')) NOT NULL DEFAULT 'draft',
                    submission_data TEXT,
                    field_responses TEXT,
                    file_uploads TEXT,
                    ip_address VARCHAR,
                    user_agent TEXT,
                    session_id VARCHAR,
                    started_at DATETIME,
                    submitted_at DATETIME,
                    last_modified_at DATETIME,
                    reviewed_by INTEGER,
                    reviewed_at DATETIME,
                    review_notes TEXT,
                    rejection_reason TEXT,
                    audit_trail TEXT,
                    compliance_checks TEXT,
                    internal_notes TEXT,
                    taken_up_by INTEGER,
                    taken_up_at DATETIME,
                    completed_by INTEGER,
                    completed_at DATETIME,
                    completion_notes TEXT,
                    created_at DATETIME,
                    updated_at DATETIME,
                    deleted_at DATETIME,
                    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY (taken_up_by) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL
                )
            ");
            
            // Create indexes
            DB::statement('CREATE INDEX form_submissions_form_id_index ON form_submissions (form_id);');
            DB::statement('CREATE INDEX form_submissions_user_id_index ON form_submissions (user_id);');
            DB::statement('CREATE INDEX form_submissions_branch_id_index ON form_submissions (branch_id);');
            DB::statement('CREATE INDEX form_submissions_status_index ON form_submissions (status);');
            DB::statement('CREATE INDEX form_submissions_submitted_at_index ON form_submissions (submitted_at);');
            DB::statement('CREATE INDEX form_submissions_taken_up_by_index ON form_submissions (taken_up_by);');
            DB::statement('CREATE INDEX form_submissions_completed_by_index ON form_submissions (completed_by);');
            
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            // For MySQL/MariaDB/PostgreSQL, use Schema builder
            Schema::create('form_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
                $table->string('submission_token')->unique();
                $table->enum('status', [
                    'draft', 'submitted', 'pending_process', 'under_review', 'approved', 'rejected', 
                    'completed', 'expired', 'in_progress', 'cancelled'
                ])->default('draft');
                
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
                
                // Taken Up and Completed Tracking
                $table->foreignId('taken_up_by')->nullable()->after('reviewed_by')->constrained('users')->onDelete('set null');
                $table->timestamp('taken_up_at')->nullable()->after('taken_up_by');
                $table->foreignId('completed_by')->nullable()->after('taken_up_at')->constrained('users')->onDelete('set null');
                $table->timestamp('completed_at')->nullable()->after('completed_by');
                $table->text('completion_notes')->nullable()->after('completed_at');
                
                $table->timestamps();
                $table->softDeletes();
                
                $table->index('form_id');
                $table->index('user_id');
                $table->index('branch_id');
                // Skip submission_token index - it's automatically created by UNIQUE constraint
                $table->index('status');
                $table->index('submitted_at');
                $table->index('taken_up_by');
                $table->index('completed_by');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
