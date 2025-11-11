<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Form extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'is_public',
        'allow_multiple_submissions',
        'submission_limit',
        'settings',
        'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'allow_multiple_submissions' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($form) {
            if (empty($form->slug)) {
                $form->slug = Str::slug($form->name);
            }
        });
    }

    /**
     * Relationship: Form has many sections
     */
    public function sections(): HasMany
    {
        return $this->hasMany(FormSection::class)->orderBy('sort_order');
    }

    /**
     * Relationship: Form has many active sections
     */
    public function activeSections(): HasMany
    {
        return $this->hasMany(FormSection::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Relationship: Form has many fields
     */
    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    /**
     * Relationship: Form has many active fields
     */
    public function activeFields(): HasMany
    {
        return $this->hasMany(FormField::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Relationship: Form has many submissions
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    /**
     * Get form status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }
}
