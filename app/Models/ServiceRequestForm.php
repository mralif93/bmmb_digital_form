<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequestForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_number',
        'status',
        'version',
        'service_type',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_city',
        'customer_state',
        'customer_postal_code',
        'customer_country',
        'customer_id_type',
        'customer_id_number',
        'customer_id_expiry_date',
        'customer_dob',
        'customer_gender',
        'customer_nationality',
        'customer_occupation',
        'customer_employer',
        'customer_employer_address',
        'customer_annual_income',
        'customer_marital_status',
        'account_number',
        'account_type',
        'account_currency',
        'account_balance',
        'account_opening_date',
        'account_status',
        'account_notes',
        'service_description',
        'service_category',
        'service_subcategories',
        'service_amount',
        'service_currency',
        'urgency_level',
        'preferred_completion_date',
        'special_instructions',
        'reason_for_request',
        'deposit_type',
        'deposit_method',
        'deposit_source',
        'deposit_source_details',
        'check_number',
        'check_bank',
        'check_account',
        'check_date',
        'wire_reference',
        'wire_originator',
        'wire_beneficiary',
        'deposit_notes',
        'transaction_amount',
        'transaction_currency',
        'exchange_rate',
        'fees',
        'total_amount',
        'payment_method',
        'payment_details',
        'aml_verified',
        'kyc_verified',
        'sanctions_checked',
        'risk_level',
        'risk_assessment_notes',
        'compliance_notes',
        'requires_approval',
        'approval_reason',
        'supporting_documents',
        'identity_document_path',
        'proof_of_address_path',
        'proof_of_income_path',
        'bank_statement_path',
        'deposit_slip_path',
        'check_image_path',
        'wire_confirmation_path',
        'other_documents_path',
        'assigned_to',
        'reviewed_by',
        'approved_by',
        'submitted_at',
        'acknowledged_at',
        'reviewed_at',
        'approved_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'completion_notes',
        'rejection_reason',
        'cancellation_reason',
        'internal_notes',
        'delivery_method',
        'delivery_instructions',
        'delivery_address',
        'delivery_contact',
        'delivery_phone',
        'delivery_date',
        'delivery_time',
        'delivery_confirmed',
        'delivery_confirmed_at',
        'ip_address',
        'user_agent',
        'form_data',
        'audit_trail',
        'communication_log',
        'service_log',
    ];

    protected $casts = [
        'customer_id_expiry_date' => 'date',
        'customer_dob' => 'date',
        'account_balance' => 'decimal:2',
        'account_opening_date' => 'date',
        'service_subcategories' => 'array',
        'service_amount' => 'decimal:2',
        'check_date' => 'date',
        'transaction_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'fees' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'preferred_completion_date' => 'date',
        'aml_verified' => 'boolean',
        'kyc_verified' => 'boolean',
        'sanctions_checked' => 'boolean',
        'requires_approval' => 'boolean',
        'supporting_documents' => 'array',
        'submitted_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'delivery_date' => 'date',
        'delivery_confirmed' => 'boolean',
        'delivery_confirmed_at' => 'datetime',
        'form_data' => 'array',
        'audit_trail' => 'array',
        'communication_log' => 'array',
        'service_log' => 'array',
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

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(SrfFormField::class, 'srf_form_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(SrfFormSubmission::class, 'srf_form_id');
    }

    public function serviceActions(): HasMany
    {
        return $this->hasMany(SrfServiceAction::class, 'srf_submission_id');
    }

    public function serviceHistory(): HasMany
    {
        return $this->hasMany(SrfServiceHistory::class, 'srf_submission_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByServiceType($query, $type)
    {
        return $query->where('service_type', $type);
    }

    public function scopeByServiceCategory($query, $category)
    {
        return $query->where('service_category', $category);
    }

    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }

    public function scopeByAccount($query, $accountNumber)
    {
        return $query->where('account_number', $accountNumber);
    }

    public function scopeSubmittedAfter($query, $date)
    {
        return $query->where('submitted_at', '>=', $date);
    }

    public function scopeSubmittedBefore($query, $date)
    {
        return $query->where('submitted_at', '<=', $date);
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    public function scopeCompliant($query)
    {
        return $query->where('aml_verified', true)
                    ->where('kyc_verified', true)
                    ->where('sanctions_checked', true);
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
            'in_progress' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
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

    public function getServiceTypeLabelAttribute(): string
    {
        return match($this->service_type) {
            'deposit' => 'Deposit Service',
            'withdrawal' => 'Withdrawal Service',
            'transfer' => 'Transfer Service',
            'account_opening' => 'Account Opening',
            'account_closure' => 'Account Closure',
            'other' => 'Other Service',
            default => 'Unknown'
        };
    }

    public function getServiceCategoryLabelAttribute(): string
    {
        return match($this->service_category) {
            'banking' => 'Banking Services',
            'investment' => 'Investment Services',
            'insurance' => 'Insurance Services',
            'loan' => 'Loan Services',
            'credit_card' => 'Credit Card Services',
            'foreign_exchange' => 'Foreign Exchange',
            'international_transfer' => 'International Transfer',
            'other' => 'Other Services',
            default => 'Unknown'
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->service_amount ?? 0, 2) . ' ' . $this->service_currency;
    }

    public function getTotalFormattedAmountAttribute(): string
    {
        return number_format($this->total_amount ?? 0, 2) . ' ' . $this->transaction_currency;
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

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isHighUrgency(): bool
    {
        return in_array($this->urgency_level, ['high', 'urgent']);
    }

    public function isHighRisk(): bool
    {
        return $this->risk_level === 'high';
    }

    public function isCompliant(): bool
    {
        return $this->aml_verified && $this->kyc_verified && $this->sanctions_checked;
    }

    public function requiresApproval(): bool
    {
        return $this->requires_approval;
    }

    public function isDepositService(): bool
    {
        return $this->service_type === 'deposit';
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

    public function getDaysUntilCompletion(): ?int
    {
        if ($this->preferred_completion_date) {
            return now()->diffInDays($this->preferred_completion_date, false);
        }
        return null;
    }

    // Static Methods
    public static function generateRequestNumber(): string
    {
        $year = date('Y');
        $lastNumber = static::whereYear('created_at', $year)
            ->where('request_number', 'like', "SRF-{$year}-%")
            ->orderBy('request_number', 'desc')
            ->value('request_number');
        
        if ($lastNumber) {
            $number = (int) substr($lastNumber, -6) + 1;
        } else {
            $number = 1;
        }
        
        return 'SRF-' . $year . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public static function getServiceTypes(): array
    {
        return [
            'deposit' => 'Deposit Service',
            'withdrawal' => 'Withdrawal Service',
            'transfer' => 'Transfer Service',
            'account_opening' => 'Account Opening',
            'account_closure' => 'Account Closure',
            'other' => 'Other Service',
        ];
    }

    public static function getServiceCategories(): array
    {
        return [
            'banking' => 'Banking Services',
            'investment' => 'Investment Services',
            'insurance' => 'Insurance Services',
            'loan' => 'Loan Services',
            'credit_card' => 'Credit Card Services',
            'foreign_exchange' => 'Foreign Exchange',
            'international_transfer' => 'International Transfer',
            'other' => 'Other Services',
        ];
    }

    public static function getDepositTypes(): array
    {
        return [
            'cash' => 'Cash Deposit',
            'check' => 'Check Deposit',
            'wire_transfer' => 'Wire Transfer',
            'ach_transfer' => 'ACH Transfer',
            'mobile_deposit' => 'Mobile Deposit',
            'atm_deposit' => 'ATM Deposit',
            'in_person' => 'In-Person Deposit',
            'online' => 'Online Deposit',
            'other' => 'Other Method',
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

    public static function getDeliveryMethods(): array
    {
        return [
            'in_person' => 'In-Person',
            'online' => 'Online',
            'phone' => 'Phone',
            'email' => 'Email',
            'mail' => 'Mail',
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