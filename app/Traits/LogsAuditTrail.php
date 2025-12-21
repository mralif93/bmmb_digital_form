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
        if ($requestData === null && (Request::has('_token') || Request::has('_method'))) {
            $requestData = Request::except(['_token', '_method', 'password', 'password_confirmation']);
        }

        // Parse user agent to get browser and platform info
        $userAgent = Request::userAgent();
        $browser = $this->parseBrowserFromUserAgent($userAgent);
        $platform = $this->parsePlatformFromUserAgent($userAgent);

        // Build enhanced metadata
        $metadata = [
            'session_id' => session()->getId(),
            'referrer' => Request::header('referer'),
            'browser' => $browser,
            'platform' => $platform,
            'request_method' => Request::method(),
            'request_url' => Request::fullUrl(),
            'execution_time' => defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) . 'ms' : null,
        ];

        return AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => $userAgent,
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'request_data' => array_merge($requestData ?? [], ['metadata' => $metadata]),
        ]);
    }

    /**
     * Parse browser name from user agent string
     */
    private function parseBrowserFromUserAgent(?string $userAgent): string
    {
        if (!$userAgent)
            return 'Unknown';

        if (preg_match('/MSIE/i', $userAgent))
            return 'Internet Explorer';
        if (preg_match('/Edge/i', $userAgent))
            return 'Microsoft Edge';
        if (preg_match('/Edg/i', $userAgent))
            return 'Microsoft Edge (Chromium)';
        if (preg_match('/Chrome/i', $userAgent))
            return 'Google Chrome';
        if (preg_match('/Safari/i', $userAgent))
            return 'Safari';
        if (preg_match('/Firefox/i', $userAgent))
            return 'Mozilla Firefox';
        if (preg_match('/Opera|OPR/i', $userAgent))
            return 'Opera';

        return 'Unknown';
    }

    /**
     * Parse platform/OS from user agent string
     */
    private function parsePlatformFromUserAgent(?string $userAgent): string
    {
        if (!$userAgent)
            return 'Unknown';

        if (preg_match('/windows|win32|win64/i', $userAgent))
            return 'Windows';
        if (preg_match('/macintosh|mac os x/i', $userAgent))
            return 'macOS';
        if (preg_match('/linux/i', $userAgent))
            return 'Linux';
        if (preg_match('/android/i', $userAgent))
            return 'Android';
        if (preg_match('/iphone|ipad|ipod/i', $userAgent))
            return 'iOS';

        return 'Unknown';
    }
}
