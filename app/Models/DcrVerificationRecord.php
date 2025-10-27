<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DcrVerificationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'dcr_submission_id',
        'verification_type',
        'verification_description',
        'verification_documents',
        'status',
        'verification_result',
        'verification_notes',
        'verified_by',
        'verified_at',
        'metadata',
    ];

    protected $casts = [
        'verification_documents' => 'array',
        'verified_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function dcrSubmission(): BelongsTo
    {
        return $this->belongsTo(DcrFormSubmission::class, 'dcr_submission_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeByVerificationType($query, $type)
    {
        return $query->where('verification_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
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
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getVerificationTypeLabelAttribute(): string
    {
        return match($this->verification_type) {
            'document_verification' => 'Document Verification',
            'third_party_verification' => 'Third Party Verification',
            'data_source_verification' => 'Data Source Verification',
            'identity_verification' => 'Identity Verification',
            'authorization_verification' => 'Authorization Verification',
            'legal_basis_verification' => 'Legal Basis Verification',
            'other' => 'Other Verification',
            default => 'Unknown'
        };
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

    public function hasDocuments(): bool
    {
        return !empty($this->verification_documents) && is_array($this->verification_documents);
    }

    public function getDocuments(): array
    {
        return $this->verification_documents ?? [];
    }

    public function getMetadataValue($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetadataValue($key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
    }

    // Static Methods
    public static function getVerificationTypes(): array
    {
        return [
            'document_verification' => 'Document Verification',
            'third_party_verification' => 'Third Party Verification',
            'data_source_verification' => 'Data Source Verification',
            'identity_verification' => 'Identity Verification',
            'authorization_verification' => 'Authorization Verification',
            'legal_basis_verification' => 'Legal Basis Verification',
            'other' => 'Other Verification',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ];
    }
}