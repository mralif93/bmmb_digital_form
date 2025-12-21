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
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('submission_token')->unique();
            $table->enum('status', [
                'draft', 'submitted', 'pending_process', 'under_review', 'approved', 'rejected', 
                'completed', 'expired', 'in_progress', 'cancelled'
            ])->default('draft');
            
            // Submission Data
            $table->json('submission_data')->nullable();
            $table->json('field_responses')->nullable();
            $table->json('file_uploads')->nullable();
            
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
            $table->json('audit_trail')->nullable();
            $table->json('compliance_checks')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Taken Up and Completed Tracking
            $table->foreignId('taken_up_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('taken_up_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('form_id');
            $table->index('user_id');
            $table->index('branch_id');
            $table->index('status');
            $table->index('submitted_at');
            $table->index('taken_up_by');
            $table->index('completed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
