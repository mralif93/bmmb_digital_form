<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DarResponseData extends Model
{
    use HasFactory;

    protected $fillable = [
        'dar_submission_id',
        'data_category',
        'data_field',
        'data_value',
        'data_source',
        'data_format',
        'file_path',
        'is_sensitive',
        'redaction_notes',
        'metadata',
        'provided_at',
    ];

    protected $casts = [
        'is_sensitive' => 'boolean',
        'metadata' => 'array',
        'provided_at' => 'datetime',
    ];

    // Relationships
    public function darSubmission(): BelongsTo
    {
        return $this->belongsTo(DarFormSubmission::class, 'dar_submission_id');
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('data_category', $category);
    }

    public function scopeByField($query, $field)
    {
        return $query->where('data_field', $field);
    }

    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    public function scopeByFormat($query, $format)
    {
        return $query->where('data_format', $format);
    }

    public function scopeProvidedAfter($query, $date)
    {
        return $query->where('provided_at', '>=', $date);
    }

    public function scopeProvidedBefore($query, $date)
    {
        return $query->where('provided_at', '<=', $date);
    }

    // Accessors & Mutators
    public function getSensitivityBadgeAttribute(): string
    {
        return $this->is_sensitive 
            ? 'bg-red-100 text-red-800' 
            : 'bg-green-100 text-green-800';
    }

    public function getFormatBadgeAttribute(): string
    {
        return match($this->data_format) {
            'json' => 'bg-blue-100 text-blue-800',
            'csv' => 'bg-green-100 text-green-800',
            'pdf' => 'bg-red-100 text-red-800',
            'xlsx' => 'bg-green-100 text-green-800',
            'txt' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getDataValuePreviewAttribute(): string
    {
        if (empty($this->data_value)) {
            return 'No data';
        }

        if ($this->is_sensitive) {
            return '[REDACTED]';
        }

        $value = $this->data_value;
        if (strlen($value) > 100) {
            return substr($value, 0, 100) . '...';
        }

        return $value;
    }

    // Helper Methods
    public function isFile(): bool
    {
        return !empty($this->file_path);
    }

    public function isText(): bool
    {
        return empty($this->file_path) && !empty($this->data_value);
    }

    public function getFileSize(): ?int
    {
        if ($this->isFile() && file_exists($this->file_path)) {
            return filesize($this->file_path);
        }
        return null;
    }

    public function getFileSizeFormatted(): ?string
    {
        $size = $this->getFileSize();
        if ($size === null) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    public function getFileExtension(): ?string
    {
        if ($this->isFile()) {
            return pathinfo($this->file_path, PATHINFO_EXTENSION);
        }
        return null;
    }

    public function getFileName(): ?string
    {
        if ($this->isFile()) {
            return pathinfo($this->file_path, PATHINFO_BASENAME);
        }
        return null;
    }

    public function getMetadataValue($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetadataValue($key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
    }

    // Static Methods
    public static function getDataCategories(): array
    {
        return [
            'personal_info' => 'Personal Information',
            'contact_info' => 'Contact Information',
            'financial_info' => 'Financial Information',
            'demographic_info' => 'Demographic Information',
            'preferences' => 'Preferences',
            'account_info' => 'Account Information',
            'transaction_data' => 'Transaction Data',
            'communication_data' => 'Communication Data',
            'location_data' => 'Location Data',
            'device_data' => 'Device Data',
            'other' => 'Other Data',
        ];
    }

    public static function getDataFormats(): array
    {
        return [
            'json' => 'JSON',
            'csv' => 'CSV',
            'pdf' => 'PDF',
            'xlsx' => 'Excel',
            'txt' => 'Text',
            'xml' => 'XML',
            'other' => 'Other',
        ];
    }

    public static function getSensitiveDataFields(): array
    {
        return [
            'ssn',
            'social_security_number',
            'credit_card_number',
            'bank_account_number',
            'password',
            'pin',
            'biometric_data',
            'medical_record',
            'financial_record',
        ];
    }

    public function isSensitiveField(): bool
    {
        $sensitiveFields = static::getSensitiveDataFields();
        return in_array(strtolower($this->data_field), $sensitiveFields);
    }
}