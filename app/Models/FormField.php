<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'field_type',
        'field_name',
        'field_label',
        'field_description',
        'field_options',
        'validation_rules',
        'is_required',
        'sort_order',
        'step',
    ];

    protected $casts = [
        'field_options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function getValidationRulesAttribute($value)
    {
        $rules = json_decode($value, true) ?? [];
        
        if ($this->is_required) {
            $rules[] = 'required';
        }

        return $rules;
    }
}