<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataCorrectionRequestForm extends Model
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
        'correction_type',
        'correction_description',
        'incorrect_data_items',
        'corrected_data_items',
        'data_sources',
        'data_period_from',
        'data_period_to',
        'reason_for_correction',
        'urgency_level',
        'impact_description',
        'legal_basis',
        'legal_basis_description',
        'consent_obtained',
        'consent_date',
        'consent_method',
        'gdpr_applicable',
        'ccpa_applicable',
        'other_privacy_law_applicable',
        'applicable_privacy_laws',
        'verification_documents',
        'verification_method',
        'third_party_verification_required',
        'third_party_verification_details',
        'data_source_verification_required',
        'data_source_verification_details',
        'verification_status',
        'verification_notes',
        'affected_systems',
        'correction_actions',
        'implementation_plan',
        'target_correction_date',
        'notify_third_parties',
        'third_parties_to_notify',
        'notification_method',
        'supporting_documents',
        'identity_document_path',
        'proof_of_correct_data_path',
        'authorization_document_path',
        'verification_document_path',
        'legal_basis_document_path',
        'other_documents_path',
        'assigned_to',
        'reviewed_by',
        'submitted_at',
        'acknowledged_at',
        'reviewed_at',
        'correction_started_at',
        'correction_completed_at',
        'responded_at',
        'completed_at',
        'response_deadline',
        'response_summary',
        'correction_summary',
        'rejection_reason',
        'internal_notes',
        'identity_verified',
        'authorization_verified',
        'legal_basis_verified',
        'data_accuracy_verified',
        'correction_feasible',
        'feasibility_notes',
        'risk_level',
        'compliance_notes',
        'ip_address',
        'user_agent',
        'form_data',
        'audit_trail',
        'communication_log',
        'correction_log',
    ];

    protected $casts = [
        'requester_id_expiry_date' => 'date',
        'data_subject_id_expiry_date' => 'date',
        'incorrect_data_items' => 'array',
        'corrected_data_items' => 'array',
        'data_sources' => 'array',
        'data_period_from' => 'date',
        'data_period_to' => 'date',
        'consent_obtained' => 'boolean',
        'consent_date' => 'date',
        'gdpr_applicable' => 'boolean',
        'ccpa_applicable' => 'boolean',
        'other_privacy_law_applicable' => 'boolean',
        'verification_documents' => 'array',
        'third_party_verification_required' => 'boolean',
        'data_source_verification_required' => 'boolean',
        'affected_systems' => 'array',
        'correction_actions' => 'array',
        'notify_third_parties' => 'boolean',
        'third_parties_to_notify' => 'array',
        'supporting_documents' => 'array',
        'submitted_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'correction_started_at' => 'datetime',
        'correction_completed_at' => 'datetime',
        'responded_at' => 'datetime',
        'completed_at' => 'datetime',
        'response_deadline' => 'date',
        'target_correction_date' => 'date',
        'identity_verified' => 'boolean',
        'authorization_verified' => 'boolean',
        'legal_basis_verified' => 'boolean',
        'data_accuracy_verified' => 'boolean',
        'correction_feasible' => 'boolean',
        'form_data' => 'array',
        'audit_trail' => 'array',
        'communication_log' => 'array',
        'correction_log' => 'array',
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
        return $this->hasMany(DcrFormField::class, 'dcr_form_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(DcrFormSubmission::class, 'dcr_form_id');
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

    public function scopeByCorrectionType($query, $type)
    {
        return $query->where('correction_type', $type);
    }

    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }

    public function scopeByVerificationStatus($query, $status)
    {
        return $query->where('verification_status', $status);
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

    public function scopeFeasible($query)
    {
        return $query->where('correction_feasible', true);
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

    public function getVerificationStatusBadgeAttribute(): string
    {
        return match($this->verification_status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getCorrectionTypeLabelAttribute(): string
    {
        return match($this->correction_type) {
            'personal_info' => 'Personal Information',
            'contact_info' => 'Contact Information',
            'financial_info' => 'Financial Information',
            'demographic_info' => 'Demographic Information',
            'preferences' => 'Preferences',
            'account_info' => 'Account Information',
            'transaction_data' => 'Transaction Data',
            'other' => 'Other Data',
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
            'data_accuracy_obligation' => 'Data Accuracy Obligation',
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

    public function isPartiallyApproved(): bool
    {
        return $this->status === 'partially_approved';
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
        return $this->isVerified() && $this->data_accuracy_verified;
    }

    public function isFeasible(): bool
    {
        return $this->correction_feasible;
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

    public function getCorrectionProgress(): int
    {
        if (!$this->correction_actions) {
            return 0;
        }

        $totalActions = count($this->correction_actions);
        $completedActions = collect($this->correction_actions)
            ->where('status', 'completed')
            ->count();

        return $totalActions > 0 ? round(($completedActions / $totalActions) * 100) : 0;
    }

    // Static Methods
    public static function generateRequestNumber(): string
    {
        $year = date('Y');
        $lastNumber = static::whereYear('created_at', $year)
            ->where('request_number', 'like', "DCR-{$year}-%")
            ->orderBy('request_number', 'desc')
            ->value('request_number');
        
        if ($lastNumber) {
            $number = (int) substr($lastNumber, -6) + 1;
        } else {
            $number = 1;
        }
        
        return 'DCR-' . $year . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public static function getCorrectionTypes(): array
    {
        return [
            'personal_info' => 'Personal Information',
            'contact_info' => 'Contact Information',
            'financial_info' => 'Financial Information',
            'demographic_info' => 'Demographic Information',
            'preferences' => 'Preferences',
            'account_info' => 'Account Information',
            'transaction_data' => 'Transaction Data',
            'other' => 'Other Data',
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
            'data_accuracy_obligation' => 'Data Accuracy Obligation',
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

    public static function getVerificationStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'failed' => 'Failed',
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