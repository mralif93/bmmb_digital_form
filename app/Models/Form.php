<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'settings',
        'form_type',
        'step_count',
        'is_active',
        'is_public',
        'qr_code',
        'qr_code_url',
        'submissions_count',
        'created_by',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code ? asset('storage/qr-codes/' . $this->qr_code) : null;
    }

    public function getFormUrlAttribute()
    {
        return route('public.forms.show', $this->slug);
    }
}