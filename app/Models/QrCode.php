<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QrCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'content',
        'branch_id',
        'qr_code_image',
        'status',
        'size',
        'format',
        'created_by',
        'last_regenerated_at',
        'expires_at',
        'validation_token',
    ];

    protected $casts = [
        'type' => 'string',
        'status' => 'string',
        'size' => 'integer',
        'last_regenerated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if QR code is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the type in title case for display.
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'branch' => 'Branch',
            'url' => 'URL',
            'text' => 'Text',
            'phone' => 'Phone',
            'email' => 'Email',
            'sms' => 'SMS',
            'wifi' => 'WiFi',
            'vcard' => 'vCard',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get the status in title case for display.
     */
    public function getStatusDisplayAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get the branch that owns the QR code.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the QR code.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
