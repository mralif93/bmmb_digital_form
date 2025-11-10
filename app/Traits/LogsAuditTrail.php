<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsAuditTrail
{
    /**
     * Log an audit trail entry.
     *
     * @param string $action The action performed (create, update, delete, etc.)
     * @param string|null $description Human-readable description
     * @param string|null $modelType The model class name
     * @param int|null $modelId The model ID
     * @param array|null $oldValues Previous values (for updates)
     * @param array|null $newValues New values (for updates/creates)
     * @param array|null $requestData Request data (sanitized)
     * @return AuditTrail
     */
    public function logAuditTrail(
        string $action,
        ?string $description = null,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $requestData = null
    ): AuditTrail {
        // Sanitize request data (remove sensitive fields)
        if ($requestData === null && Request::has('_token', '_method')) {
            $requestData = Request::except(['_token', '_method', 'password', 'password_confirmation']);
        }

        return AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'request_data' => $requestData,
        ]);
    }
}
