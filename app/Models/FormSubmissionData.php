<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmissionData extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'field_id',
        'field_value',
        'field_value_json',
        'file_path',
    ];

    protected $casts = [
        'field_value_json' => 'array',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'submission_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'field_id');
    }
}

