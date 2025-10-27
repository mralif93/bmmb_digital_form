<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RemittanceApplicationForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_number',
        'status',
        'version',
        'applicant_name',
        'applicant_phone',
        'applicant_email',
        'applicant_address',
        'applicant_city',
        'applicant_state',
        'applicant_postal_code',
        'applicant_country',
        'applicant_id_type',
        'applicant_id_number',
        'applicant_id_expiry_date',
        'remittance_amount',
        'remittance_currency',
        'remittance_purpose',
        'remittance_purpose_description',
        'remittance_frequency',
        'beneficiary_name',
        'beneficiary_relationship',
        'beneficiary_address',
        'beneficiary_city',
        'beneficiary_state',
        'beneficiary_postal_code',
        'beneficiary_country',
        'beneficiary_phone',
        'beneficiary_email',
        'beneficiary_bank_name',
        'beneficiary_bank_account',
        'beneficiary_bank_routing',
        'beneficiary_bank_swift',
        'payment_method',
        'payment_source',
        'payment_currency',
        'exchange_rate',
        'service_fee',
        'total_amount',
        'supporting_documents',
        'id_document_path',
        'proof_of_income_path',
        'beneficiary_id_path',
        'bank_statement_path',
        'purpose_document_path',
        'aml_verified',
        'kyc_verified',
        'sanctions_checked',
        'compliance_notes',
        'risk_level',
        'processed_by',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'completed_at',
        'rejection_reason',
        'internal_notes',
        'ip_address',
        'user_agent',
        'form_data',
        'audit_trail',
    ];

    protected $casts = [
        'applicant_id_expiry_date' => 'date',
        'remittance_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'service_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'supporting_documents' => 'array',
        'aml_verified' => 'boolean',
        'kyc_verified' => 'boolean',
        'sanctions_checked' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'form_data' => 'array',
        'audit_trail' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(RafFormField::class, 'raf_form_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(RafFormSubmission::class, 'raf_form_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCurrency($query, $currency)
    {
        return $query->where('remittance_currency', $currency);
    }

    public function scopeByAmountRange($query, $min, $max)
    {
        return $query->whereBetween('remittance_amount', [$min, $max]);
    }

    public function scopeSubmittedAfter($query, $date)
    {
        return $query->where('submitted_at', '>=', $date);
    }

    public function scopeSubmittedBefore($query, $date)
    {
        return $query->where('submitted_at', '<=', $date);
    }

    // Accessors & Mutators
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->remittance_amount, 2) . ' ' . $this->remittance_currency;
    }

    public function getTotalFormattedAmountAttribute(): string
    {
        return number_format($this->total_amount, 2) . ' ' . $this->payment_currency;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'under_review' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'completed' => 'bg-purple-100 text-purple-800',
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

    public function isHighRisk(): bool
    {
        return $this->risk_level === 'high';
    }

    public function isCompliant(): bool
    {
        return $this->aml_verified && $this->kyc_verified && $this->sanctions_checked;
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
    public static function generateApplicationNumber(): string
    {
        $year = date('Y');
        $lastNumber = static::whereYear('created_at', $year)
            ->where('application_number', 'like', "RAF-{$year}-%")
            ->orderBy('application_number', 'desc')
            ->value('application_number');
        
        if ($lastNumber) {
            $number = (int) substr($lastNumber, -6) + 1;
        } else {
            $number = 1;
        }
        
        return 'RAF-' . $year . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Boot method to auto-generate application number
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->application_number)) {
                $model->application_number = static::generateApplicationNumber();
            }
        });
    }
}