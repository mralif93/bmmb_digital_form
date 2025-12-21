<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'form_id',
        'section_key',
        'section_label',
        'section_description',
        'sort_order',
        'is_active',
        'grid_layout',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'grid_layout' => '2-column',
    ];

    /**
     * Relationship: FormSection belongs to Form
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Relationship: FormSection has many fields
     */
    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class, 'section_id')->orderBy('sort_order');
    }

    /**
     * Relationship: FormSection has many active fields
     */
    public function activeFields(): HasMany
    {
        return $this->hasMany(FormField::class, 'section_id')->where('is_active', true)->orderBy('sort_order');
    }

    // Scopes
    public function scopeForForm($query, $formId)
    {
        return $query->where('form_id', $formId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Static Methods
    public static function getSectionsForForm($formId): array
    {
        return static::forForm($formId)
            ->active()
            ->ordered()
            ->get()
            ->pluck('section_label', 'section_key')
            ->toArray();
    }

    /**
     * Get default sections for a form type (fallback if no custom sections exist)
     */
    public static function getDefaultSections(string $formType): array
    {
        $defaults = [
            'raf' => [
                'applicant_info' => 'Applicant Information',
                'remittance_details' => 'Remittance Details',
                'beneficiary_info' => 'Beneficiary Information',
                'payment_info' => 'Payment Information',
                'documents' => 'Supporting Documents',
                'compliance' => 'Compliance & Verification',
            ],
            'dar' => [
                'requester_info' => 'Requester Information',
                'data_subject_info' => 'Data Subject Information',
                'request_details' => 'Request Details',
                'legal_basis' => 'Legal Basis',
                'data_processing' => 'Data Processing Information',
                'documents' => 'Supporting Documents',
                'compliance' => 'Compliance & Verification',
            ],
            'dcr' => [
                'requester_info' => 'Requester Information',
                'data_subject_info' => 'Data Subject Information',
                'correction_details' => 'Correction Details',
                'legal_basis' => 'Legal Basis',
                'verification' => 'Verification Process',
                'implementation' => 'Implementation Plan',
                'documents' => 'Supporting Documents',
                'compliance' => 'Compliance & Verification',
            ],
            'srf' => [
                'customer_info' => 'Customer Information',
                'account_info' => 'Account Information',
                'service_details' => 'Service Details',
                'financial_info' => 'Financial Information',
                'compliance' => 'Compliance & Risk',
                'documents' => 'Supporting Documents',
                'delivery' => 'Service Delivery',
            ],
        ];

        return $defaults[$formType] ?? [];
    }

    /**
     * Initialize default sections for a form if none exist
     */
    public static function initializeDefaults($formId, string $formType): void
    {
        $existing = static::forForm($formId)->count();
        if ($existing > 0) {
            return; // Already initialized
        }

        $defaults = static::getDefaultSections($formType);
        $sortOrder = 1;

        foreach ($defaults as $key => $label) {
            static::create([
                'form_id' => $formId,
                'section_key' => $key,
                'section_label' => $label,
                'sort_order' => $sortOrder++,
                'is_active' => true,
            ]);
        }
    }
}
