<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'form_id',
        'user_id',
        'branch_id',
        'submission_token',
        'reference_number',
        'status',
        'submission_data',
        'field_responses',
        'file_uploads',
        'ip_address',
        'user_agent',
        'session_id',
        'started_at',
        'submitted_at',
        'last_modified_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'rejection_reason',
        'audit_trail',
        'compliance_checks',
        'internal_notes',
        'taken_up_by',
        'taken_up_at',
        'completed_by',
        'completed_at',
        'completion_notes',
    ];

    protected $casts = [
        'submission_data' => 'array',
        'field_responses' => 'array',
        'file_uploads' => 'array',
        'audit_trail' => 'array',
        'compliance_checks' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'last_modified_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'taken_up_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function takenUpBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_up_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function submissionData(): HasMany
    {
        return $this->hasMany(FormSubmissionData::class, 'submission_id');
    }

    /**
     * Generate a human-readable reference number
     * Format: BMMB-YYYYMMDD-HHMMSS-XXXXX
     * Example: BMMB-20251219-160314-A8B9C
     */
    public static function generateReferenceNumber(): string
    {
        $timestamp = now()->format('Ymd-His');
        $random = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5));

        $referenceNumber = "BMMB-{$timestamp}-{$random}";

        // Ensure uniqueness
        $counter = 1;
        $originalRef = $referenceNumber;
        while (self::where('reference_number', $referenceNumber)->exists()) {
            $referenceNumber = $originalRef . '-' . $counter;
            $counter++;
        }

        return $referenceNumber;
    }
}