<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SrfFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'srf_form_id',
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
    public function srfForm(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestForm::class, 'srf_form_id');
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

    public function isCurrency(): bool
    {
        return $this->field_type === 'currency';
    }

    public function isTime(): bool
    {
        return $this->field_type === 'time';
    }

    public function isDateTime(): bool
    {
        return $this->field_type === 'datetime';
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
            'currency' => 'Currency Input',
            'multiselect' => 'Multi-Select',
            'time' => 'Time Picker',
            'datetime' => 'Date & Time Picker',
        ];
    }

    public static function getFieldSections(): array
    {
        return [
            'customer_info' => 'Customer Information',
            'account_info' => 'Account Information',
            'service_details' => 'Service Details',
            'financial_info' => 'Financial Information',
            'compliance' => 'Compliance & Risk',
            'documents' => 'Supporting Documents',
            'delivery' => 'Service Delivery',
        ];
    }
}