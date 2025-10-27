<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DcrFormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'dcr_form_id',
        'user_id',
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
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'last_modified_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'audit_trail' => 'array',
        'compliance_checks' => 'array',
    ];

    // Relationships
    public function dcrForm(): BelongsTo
    {
        return $this->belongsTo(DataCorrectionRequestForm::class, 'dcr_form_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function correctionActions(): HasMany
    {
        return $this->hasMany(DcrCorrectionAction::class, 'dcr_submission_id');
    }

    public function verificationRecords(): HasMany
    {
        return $this->hasMany(DcrVerificationRecord::class, 'dcr_submission_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSubmittedAfter($query, $date)
    {
        return $query->where('submitted_at', '>=', $date);
    }

    public function scopeSubmittedBefore($query, $date)
    {
        return $query->where('submitted_at', '<=', $date);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByForm($query, $formId)
    {
        return $query->where('dcr_form_id', $formId);
    }

    // Accessors & Mutators
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'under_review' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'completed' => 'bg-purple-100 text-purple-800',
            'partially_approved' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getSubmissionTimeAttribute(): ?int
    {
        if ($this->started_at && $this->submitted_at) {
            return $this->started_at->diffInMinutes($this->submitted_at);
        }
        return null;
    }

    public function getDaysSinceSubmissionAttribute(): ?int
    {
        if ($this->submitted_at) {
            return $this->submitted_at->diffInDays(now());
        }
        return null;
    }

    // Helper Methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPartiallyApproved(): bool
    {
        return $this->status === 'partially_approved';
    }

    public function hasFileUploads(): bool
    {
        return !empty($this->file_uploads) && is_array($this->file_uploads);
    }

    public function getFileUploads(): array
    {
        return $this->file_uploads ?? [];
    }

    public function getFieldResponse($fieldName)
    {
        return $this->field_responses[$fieldName] ?? null;
    }

    public function setFieldResponse($fieldName, $value): void
    {
        $responses = $this->field_responses ?? [];
        $responses[$fieldName] = $value;
        $this->field_responses = $responses;
    }

    public function addAuditEntry($action, $details = []): void
    {
        $auditTrail = $this->audit_trail ?? [];
        $auditTrail[] = [
            'action' => $action,
            'details' => $details,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
        ];
        $this->audit_trail = $auditTrail;
    }

    public function addComplianceCheck($checkType, $result, $details = []): void
    {
        $checks = $this->compliance_checks ?? [];
        $checks[] = [
            'check_type' => $checkType,
            'result' => $result,
            'details' => $details,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
        ];
        $this->compliance_checks = $checks;
    }

    // Static Methods
    public static function generateSubmissionToken(): string
    {
        return 'dcr_' . uniqid() . '_' . time();
    }

    // Boot method to auto-generate submission token
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->submission_token)) {
                $model->submission_token = static::generateSubmissionToken();
            }
        });
    }
}