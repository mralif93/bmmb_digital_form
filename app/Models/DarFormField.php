<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DarFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'dar_form_id',
        'field_section',
        'field_name',
        'field_label',
        'field_description',
        'field_type',
        'field_placeholder',
        'field_help_text',
        'is_required',
        'is_conditional',
        'conditional_logic',
        'validation_rules',
        'field_options',
        'field_settings',
        'sort_order',
        'is_active',
        'css_class',
        'custom_attributes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_conditional' => 'boolean',
        'is_active' => 'boolean',
        'conditional_logic' => 'array',
        'validation_rules' => 'array',
        'field_options' => 'array',
        'field_settings' => 'array',
        'custom_attributes' => 'array',
    ];

    // Relationships
    public function darForm(): BelongsTo
    {
        return $this->belongsTo(DataAccessRequestForm::class, 'dar_form_id');
    }

    // Scopes
    public function scopeBySection($query, $section)
    {
        return $query->where('field_section', $section);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('field_type', $type);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Helper Methods
    public function isText(): bool
    {
        return in_array($this->field_type, ['text', 'email', 'phone', 'textarea']);
    }

    public function isSelect(): bool
    {
        return in_array($this->field_type, ['select', 'radio', 'checkbox', 'multiselect']);
    }

    public function isFile(): bool
    {
        return $this->field_type === 'file';
    }

    public function isDate(): bool
    {
        return $this->field_type === 'date';
    }

    public function hasOptions(): bool
    {
        return !empty($this->field_options) && is_array($this->field_options);
    }

    public function getOptions(): array
    {
        return $this->field_options ?? [];
    }

    public function getValidationRules(): array
    {
        return $this->validation_rules ?? [];
    }

    public function getConditionalLogic(): array
    {
        return $this->conditional_logic ?? [];
    }

    public function getFieldSettings(): array
    {
        return $this->field_settings ?? [];
    }

    public function getCustomAttributes(): array
    {
        return $this->custom_attributes ?? [];
    }

    // Static Methods
    public static function getFieldTypes(): array
    {
        return [
            'text' => 'Text Input',
            'email' => 'Email Input',
            'phone' => 'Phone Input',
            'number' => 'Number Input',
            'textarea' => 'Text Area',
            'select' => 'Select Dropdown',
            'radio' => 'Radio Buttons',
            'checkbox' => 'Checkboxes',
            'date' => 'Date Picker',
            'file' => 'File Upload',
            'signature' => 'Digital Signature',
            'multiselect' => 'Multi-Select',
        ];
    }

    public static function getFieldSections(): array
    {
        return [
            'requester_info' => 'Requester Information',
            'data_subject_info' => 'Data Subject Information',
            'request_details' => 'Request Details',
            'legal_basis' => 'Legal Basis',
            'data_processing' => 'Data Processing Information',
            'documents' => 'Supporting Documents',
            'compliance' => 'Compliance & Verification',
        ];
    }
}