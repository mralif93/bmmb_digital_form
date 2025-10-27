<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SrfServiceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'srf_submission_id',
        'event_type',
        'event_description',
        'old_value',
        'new_value',
        'performed_by',
        'performed_at',
        'event_notes',
        'event_data',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'event_data' => 'array',
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
    public function scopeByEventType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('performed_by', $userId);
    }

    public function scopeAfter($query, $date)
    {
        return $query->where('performed_at', '>=', $date);
    }

    public function scopeBefore($query, $date)
    {
        return $query->where('performed_at', '<=', $date);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('performed_at', '>=', now()->subDays($days));
    }

    // Accessors & Mutators
    public function getEventTypeLabelAttribute(): string
    {
        return match($this->event_type) {
            'status_change' => 'Status Change',
            'assignment' => 'Assignment',
            'review' => 'Review',
            'approval' => 'Approval',
            'rejection' => 'Rejection',
            'completion' => 'Completion',
            'cancellation' => 'Cancellation',
            'modification' => 'Modification',
            'notification' => 'Notification',
            'other' => 'Other Event',
            default => 'Unknown'
        };
    }

    public function getEventTypeBadgeAttribute(): string
    {
        return match($this->event_type) {
            'status_change' => 'bg-blue-100 text-blue-800',
            'assignment' => 'bg-purple-100 text-purple-800',
            'review' => 'bg-yellow-100 text-yellow-800',
            'approval' => 'bg-green-100 text-green-800',
            'rejection' => 'bg-red-100 text-red-800',
            'completion' => 'bg-green-100 text-green-800',
            'cancellation' => 'bg-red-100 text-red-800',
            'modification' => 'bg-orange-100 text-orange-800',
            'notification' => 'bg-gray-100 text-gray-800',
            'other' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->performed_at->diffForHumans();
    }

    // Helper Methods
    public function isStatusChange(): bool
    {
        return $this->event_type === 'status_change';
    }

    public function isAssignment(): bool
    {
        return $this->event_type === 'assignment';
    }

    public function isReview(): bool
    {
        return $this->event_type === 'review';
    }

    public function isApproval(): bool
    {
        return $this->event_type === 'approval';
    }

    public function isRejection(): bool
    {
        return $this->event_type === 'rejection';
    }

    public function isCompletion(): bool
    {
        return $this->event_type === 'completion';
    }

    public function isCancellation(): bool
    {
        return $this->event_type === 'cancellation';
    }

    public function hasChange(): bool
    {
        return !empty($this->old_value) || !empty($this->new_value);
    }

    public function getEventDataValue($key, $default = null)
    {
        return $this->event_data[$key] ?? $default;
    }

    public function setEventDataValue($key, $value): void
    {
        $data = $this->event_data ?? [];
        $data[$key] = $value;
        $this->event_data = $data;
    }

    // Static Methods
    public static function getEventTypes(): array
    {
        return [
            'status_change' => 'Status Change',
            'assignment' => 'Assignment',
            'review' => 'Review',
            'approval' => 'Approval',
            'rejection' => 'Rejection',
            'completion' => 'Completion',
            'cancellation' => 'Cancellation',
            'modification' => 'Modification',
            'notification' => 'Notification',
            'other' => 'Other Event',
        ];
    }

    public static function logEvent($submissionId, $eventType, $description, $oldValue = null, $newValue = null, $notes = null, $data = []): self
    {
        return static::create([
            'srf_submission_id' => $submissionId,
            'event_type' => $eventType,
            'event_description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'performed_by' => auth()->id(),
            'performed_at' => now(),
            'event_notes' => $notes,
            'event_data' => $data,
        ]);
    }
}