<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DcrCorrectionAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'dcr_submission_id',
        'action_type',
        'data_field',
        'old_value',
        'new_value',
        'data_source',
        'system_affected',
        'status',
        'implementation_notes',
        'implemented_by',
        'implemented_at',
        'verification_notes',
        'verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'implemented_at' => 'datetime',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function dcrSubmission(): BelongsTo
    {
        return $this->belongsTo(DcrFormSubmission::class, 'dcr_submission_id');
    }

    public function implementedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'implemented_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
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

    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
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

    public function isVerified(): bool
    {
        return $this->verified;
    }

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
            'update' => 'Update',
            'delete' => 'Delete',
            'add' => 'Add',
            'modify' => 'Modify',
            default => 'Unknown'
        };
    }

    // Static Methods
    public static function getActionTypes(): array
    {
        return [
            'update' => 'Update',
            'delete' => 'Delete',
            'add' => 'Add',
            'modify' => 'Modify',
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