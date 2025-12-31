<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class BranchController extends Controller
{
    /**
     * Display the branch public page with forms
     */
    public function show($tiAgentCode, Request $request)
    {
        $branch = Branch::where('ti_agent_code', $tiAgentCode)->firstOrFail();

        // Check if there's an active QR code for this branch
        $qrCode = QrCode::where('branch_id', $branch->id)
            ->where('type', 'branch')
            ->where('status', 'active')
            ->first();

        // If no active QR code exists, show error
        if (!$qrCode) {
            abort(403, 'This QR code is not active or has been deactivated. Please contact the administrator.');
        }

        // Check if QR code is expired
        if ($qrCode->expires_at && $qrCode->expires_at->isPast()) {
            abort(403, 'This QR Code has expired. Please use the QR code for today.');
        }

        // Validate token if QR code has one
        // Token verification is required for security
        if ($qrCode->validation_token) {
            $token = $request->query('token');

            // If no token provided, reject access
            if (empty($token)) {
                abort(403, 'Invalid QR code. Token is required. Please scan the QR code again.');
            }

            // If token doesn't match, reject access
            if ($token !== $qrCode->validation_token) {
                Log::warning('QR code accessed with invalid token', [
                    'qr_code_id' => $qrCode->id,
                    'branch_id' => $branch->id,
                    'expected_token' => substr($qrCode->validation_token, 0, 8) . '...',
                    'provided_token' => substr($token, 0, 8) . '...',
                ]);
                abort(403, 'Invalid QR code token. Please scan the latest QR code.');
            } else {
                // Token matches - valid access
                Log::debug('QR code accessed with valid token', [
                    'qr_code_id' => $qrCode->id,
                    'branch_id' => $branch->id,
                ]);
            }
        }

        // Store branch_id in session for form submissions
        session(['submission_branch_id' => $branch->id]);

        $token = $request->query('token');
        $tokenValid = $qrCode->validation_token && $token === $qrCode->validation_token;
        $wasExpired = false;

        $qrCodeInfo = [
            'is_expired' => $wasExpired,
            'expires_at' => $qrCode->expires_at,
            'last_regenerated_at' => $qrCode->last_regenerated_at,
            'has_token' => !empty($qrCode->validation_token),
            'token_provided' => !empty($token),
            'token_valid' => $tokenValid,
        ];

        return view('public.branch', compact('branch', 'qrCodeInfo'), ['hideHeader' => true]);
    }

    /**
     * Regenerate an expired QR code
     */
    private function regenerateQrCode(QrCode $qrCode)
    {
        try {
            $branch = $qrCode->branch;
            // Generate new validation token
            $newToken = bin2hex(random_bytes(16));

            // Generate QR code URL with token
            $qrContent = $branch ? route('public.branch', ['tiAgentCode' => $branch->ti_agent_code, 'token' => $newToken]) : $qrCode->content;

            // Delete old QR code image
            if ($qrCode->qr_code_image) {
                Storage::disk('public')->delete('qr-codes/' . $qrCode->qr_code_image);
            }

            // Generate new QR code image
            $qrCodeImage = QrCodeGenerator::format($qrCode->format)
                ->size($qrCode->size)
                ->margin(2)
                ->generate($qrContent);

            // Save new QR code image
            $fileName = 'qr_' . time() . '_' . uniqid() . '.' . $qrCode->format;
            $filePath = 'qr-codes/' . $fileName;
            Storage::disk('public')->put($filePath, $qrCodeImage);

            // Update QR code record with new expiration and token
            $qrCode->update([
                'qr_code_image' => $fileName,
                'content' => $qrContent,
                'last_regenerated_at' => now(),
                'expires_at' => now()->addMinutes($this->getQrCodeExpirationMinutes()),
                'validation_token' => $newToken,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to auto-regenerate QR code: ' . $e->getMessage());
        }
    }

    /**
     * Get QR code expiration minutes from settings
     */
    private function getQrCodeExpirationMinutes(): int
    {
        $settings = Cache::get('system_settings', []);
        return (int) ($settings['qr_code_expiration_minutes'] ?? 60);
    }
}
