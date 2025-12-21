<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormField extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'form_id',
        'section_id',
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
        'grid_column',
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

    /**
     * Relationship: FormField belongs to Form
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Relationship: FormField belongs to FormSection
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(FormSection::class, 'section_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * Helper Methods
     */
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

    /**
     * Get field types
     */
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
            'repeater' => 'Repeater Table',
            'notes' => 'Important Notes (HTML Content)',
        ];
    }
}
