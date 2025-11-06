<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_type',
        'section_key',
        'section_label',
        'section_description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeForFormType($query, string $formType)
    {
        return $query->where('form_type', $formType);
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
    public static function getSectionsForFormType(string $formType): array
    {
        return static::forFormType($formType)
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
     * Initialize default sections for a form type if none exist
     */
    public static function initializeDefaults(string $formType): void
    {
        $existing = static::forFormType($formType)->count();
        if ($existing > 0) {
            return; // Already initialized
        }

        $defaults = static::getDefaultSections($formType);
        $sortOrder = 1;

        foreach ($defaults as $key => $label) {
            static::create([
                'form_type' => $formType,
                'section_key' => $key,
                'section_label' => $label,
                'sort_order' => $sortOrder++,
                'is_active' => true,
            ]);
        }
    }
}
