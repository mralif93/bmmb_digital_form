<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SrfServiceAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'srf_submission_id',
        'action_type',
        'action_description',
        'status',
        'action_notes',
        'action_data',
        'performed_by',
        'started_at',
        'completed_at',
        'completion_notes',
        'attachments',
    ];

    protected $casts = [
        'action_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'attachments' => 'array',
    ];

    // Relationships
    public function srfSubmission(): BelongsTo
    {
        return $this->belongsTo(SrfFormSubmission::class, 'srf_submission_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Scopes
    public function scopeByActionType($query, $type)
    {
        return $query->where('action_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Accessors & Mutators
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getActionTypeLabelAttribute(): string
    {
        return match($this->action_type) {
            'account_opening' => 'Account Opening',
            'deposit_processing' => 'Deposit Processing',
            'withdrawal_processing' => 'Withdrawal Processing',
            'transfer_processing' => 'Transfer Processing',
            'verification' => 'Verification',
            'compliance_check' => 'Compliance Check',
            'document_review' => 'Document Review',
            'approval' => 'Approval',
            'notification' => 'Notification',
            'other' => 'Other Action',
            default => 'Unknown'
        };
    }

    public function getDurationAttribute(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        return null;
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments) && is_array($this->attachments);
    }

    public function getAttachments(): array
    {
        return $this->attachments ?? [];
    }

    public function getActionDataValue($key, $default = null)
    {
        return $this->action_data[$key] ?? $default;
    }

    public function setActionDataValue($key, $value): void
    {
        $data = $this->action_data ?? [];
        $data[$key] = $value;
        $this->action_data = $data;
    }

    // Static Methods
    public static function getActionTypes(): array
    {
        return [
            'account_opening' => 'Account Opening',
            'deposit_processing' => 'Deposit Processing',
            'withdrawal_processing' => 'Withdrawal Processing',
            'transfer_processing' => 'Transfer Processing',
            'verification' => 'Verification',
            'compliance_check' => 'Compliance Check',
            'document_review' => 'Document Review',
            'approval' => 'Approval',
            'notification' => 'Notification',
            'other' => 'Other Action',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
        ];
    }
}