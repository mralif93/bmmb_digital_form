<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'user_id',
        'branch_id',
        'submission_token',
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

    public function submissionData(): HasMany
    {
        return $this->hasMany(FormSubmissionData::class, 'submission_id');
    }
}