<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataAccessRequestForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_number',
        'status',
        'version',
        'requester_name',
        'requester_phone',
        'requester_email',
        'requester_address',
        'requester_city',
        'requester_state',
        'requester_postal_code',
        'requester_country',
        'requester_id_type',
        'requester_id_number',
        'requester_id_expiry_date',
        'requester_organization',
        'requester_position',
        'requester_organization_type',
        'data_subject_name',
        'data_subject_phone',
        'data_subject_email',
        'data_subject_address',
        'data_subject_city',
        'data_subject_state',
        'data_subject_postal_code',
        'data_subject_country',
        'data_subject_id_type',
        'data_subject_id_number',
        'data_subject_id_expiry_date',
        'relationship_to_data_subject',
        'data_subject_authorization_document_path',
        'request_type',
        'request_description',
        'data_categories',
        'data_sources',
        'data_period_from',
        'data_period_to',
        'specific_data_items',
        'urgency_level',
        'justification',
        'legal_basis',
        'legal_basis_description',
        'consent_obtained',
        'consent_date',
        'consent_method',
        'gdpr_applicable',
        'ccpa_applicable',
        'other_privacy_law_applicable',
        'applicable_privacy_laws',
        'data_controllers',
        'data_processors',
        'data_retention_periods',
        'data_security_measures',
        'data_transferred_third_countries',
        'third_countries_list',
        'safeguards_description',
        'supporting_documents',
        'identity_document_path',
        'authorization_document_path',
        'proof_of_relationship_path',
        'legal_basis_document_path',
        'consent_document_path',
        'other_documents_path',
        'assigned_to',
        'reviewed_by',
        'submitted_at',
        'acknowledged_at',
        'reviewed_at',
        'responded_at',
        'completed_at',
        'response_deadline',
        'response_summary',
        'data_provided_summary',
        'rejection_reason',
        'internal_notes',
        'identity_verified',
        'authorization_verified',
        'legal_basis_verified',
        'data_existence_confirmed',
        'verification_notes',
        'risk_level',
        'compliance_notes',
        'ip_address',
        'user_agent',
        'form_data',
        'audit_trail',
        'communication_log',
    ];

    protected $casts = [
        'requester_id_expiry_date' => 'date',
        'data_subject_id_expiry_date' => 'date',
        'data_categories' => 'array',
        'data_sources' => 'array',
        'data_period_from' => 'date',
        'data_period_to' => 'date',
        'consent_obtained' => 'boolean',
        'consent_date' => 'date',
        'gdpr_applicable' => 'boolean',
        'ccpa_applicable' => 'boolean',
        'other_privacy_law_applicable' => 'boolean',
        'data_controllers' => 'array',
        'data_processors' => 'array',
        'data_retention_periods' => 'array',
        'data_transferred_third_countries' => 'boolean',
        'third_countries_list' => 'array',
        'supporting_documents' => 'array',
        'submitted_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'responded_at' => 'datetime',
        'completed_at' => 'datetime',
        'response_deadline' => 'date',
        'identity_verified' => 'boolean',
        'authorization_verified' => 'boolean',
        'legal_basis_verified' => 'boolean',
        'data_existence_confirmed' => 'boolean',
        'form_data' => 'array',
        'audit_trail' => 'array',
        'communication_log' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(DarFormField::class, 'dar_form_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(DarFormSubmission::class, 'dar_form_id');
    }

    public function responseData(): HasMany
    {
        return $this->hasMany(DarResponseData::class, 'dar_submission_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByRequestType($query, $type)
    {
        return $query->where('request_type', $type);
    }

    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }

    public function scopeGdprApplicable($query)
    {
        return $query->where('gdpr_applicable', true);
    }

    public function scopeCcpaApplicable($query)
    {
        return $query->where('ccpa_applicable', true);
    }

    public function scopeSubmittedAfter($query, $date)
    {
        return $query->where('submitted_at', '>=', $date);
    }

    public function scopeSubmittedBefore($query, $date)
    {
        return $query->where('submitted_at', '<=', $date);
    }

    public function scopeOverdue($query)
    {
        return $query->where('response_deadline', '<', now())
            ->whereIn('status', ['submitted', 'under_review']);
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
            'expired' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getUrgencyBadgeAttribute(): string
    {
        return match($this->urgency_level) {
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getRiskLevelBadgeAttribute(): string
    {
        return match($this->risk_level) {
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getRequestTypeLabelAttribute(): string
    {
        return match($this->request_type) {
            'access' => 'Data Access',
            'rectification' => 'Data Rectification',
            'erasure' => 'Data Erasure',
            'portability' => 'Data Portability',
            'restriction' => 'Processing Restriction',
            'objection' => 'Processing Objection',
            'complaint' => 'Complaint',
            'other' => 'Other Request',
            default => 'Unknown'
        };
    }

    public function getLegalBasisLabelAttribute(): string
    {
        return match($this->legal_basis) {
            'consent' => 'Consent',
            'contract' => 'Contract',
            'legal_obligation' => 'Legal Obligation',
            'vital_interests' => 'Vital Interests',
            'public_task' => 'Public Task',
            'legitimate_interests' => 'Legitimate Interests',
            'other' => 'Other',
            default => 'Unknown'
        };
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

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isOverdue(): bool
    {
        return $this->response_deadline && 
               $this->response_deadline < now() && 
               in_array($this->status, ['submitted', 'under_review']);
    }

    public function isHighUrgency(): bool
    {
        return in_array($this->urgency_level, ['high', 'urgent']);
    }

    public function isHighRisk(): bool
    {
        return $this->risk_level === 'high';
    }

    public function isVerified(): bool
    {
        return $this->identity_verified && 
               $this->authorization_verified && 
               $this->legal_basis_verified;
    }

    public function isCompliant(): bool
    {
        return $this->isVerified() && $this->data_existence_confirmed;
    }

    public function getDaysUntilDeadline(): ?int
    {
        if ($this->response_deadline) {
            return now()->diffInDays($this->response_deadline, false);
        }
        return null;
    }

    public function getProcessingTime(): ?int
    {
        if ($this->submitted_at && $this->completed_at) {
            return $this->submitted_at->diffInDays($this->completed_at);
        }
        return null;
    }

    public function getDaysSinceSubmission(): ?int
    {
        if ($this->submitted_at) {
            return $this->submitted_at->diffInDays(now());
        }
        return null;
    }

    // Static Methods
    public static function generateRequestNumber(): string
    {
        $year = date('Y');
        $lastNumber = static::whereYear('created_at', $year)
            ->where('request_number', 'like', "DAR-{$year}-%")
            ->orderBy('request_number', 'desc')
            ->value('request_number');
        
        if ($lastNumber) {
            $number = (int) substr($lastNumber, -6) + 1;
        } else {
            $number = 1;
        }
        
        return 'DAR-' . $year . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public static function getRequestTypes(): array
    {
        return [
            'access' => 'Data Access',
            'rectification' => 'Data Rectification',
            'erasure' => 'Data Erasure',
            'portability' => 'Data Portability',
            'restriction' => 'Processing Restriction',
            'objection' => 'Processing Objection',
            'complaint' => 'Complaint',
            'other' => 'Other Request',
        ];
    }

    public static function getLegalBases(): array
    {
        return [
            'consent' => 'Consent',
            'contract' => 'Contract',
            'legal_obligation' => 'Legal Obligation',
            'vital_interests' => 'Vital Interests',
            'public_task' => 'Public Task',
            'legitimate_interests' => 'Legitimate Interests',
            'other' => 'Other',
        ];
    }

    public static function getUrgencyLevels(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    // Boot method to auto-generate request number
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = static::generateRequestNumber();
            }
        });
    }
}