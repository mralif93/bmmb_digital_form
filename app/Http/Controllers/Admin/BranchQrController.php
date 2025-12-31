<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Facades\Cache;

class BranchQrController extends Controller
{
    /**
     * Display the daily QR Code for the branch counter.
     */
    public function display()
    {
        $user = auth()->user();

        // Ensure user has a branch
        if (!$user->branch_id) {
            abort(403, 'You must optionally be assigned to a branch to view the counter display.');
        }

        $branch = $user->branch;

        // Find or create today's QR code for this branch
        $qrCode = QrCode::where('branch_id', $branch->id)
            ->where('type', 'branch')
            ->whereDate('created_at', today())
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        // If no valid QR code exists, we might need to rely on the scheduled task or generate one on the fly.
        // For robustness, let's try to find the latest valid one even if created earlier but still not expired.
        if (!$qrCode) {
            $qrCode = QrCode::where('branch_id', $branch->id)
                ->where('type', 'branch')
                ->where('expires_at', '>', now())
                ->latest()
                ->first();
        }

        // If still no QR code, we can't display anything (or show an error/placeholder).
        // In a real scenario, we might want to trigger generation here if allowed.
        // But for now, let's assume the daily scheduler handles it.

        // If still no QR code, generate one on the fly for today
        if (!$qrCode) {
            // Create a new token
            $token = \Illuminate\Support\Str::random(32);

            // Generate content URL
            $url = route('public.branch', ['tiAgentCode' => $branch->ti_agent_code]);
            $url .= (str_contains($url, '?') ? '&' : '?') . 'token=' . $token;

            // Create the QR code record
            $qrCode = QrCode::create([
                'name' => 'Branch QR - ' . now()->toDateString(),
                'branch_id' => $branch->id,
                'validation_token' => $token,
                'content' => $url,
                'expires_at' => today()->endOfDay(),
                'type' => 'branch', // Assuming 'branch' is the type for daily branch codes
                'created_by' => $user->id, // Track who triggered it (the BM)
                'last_regenerated_at' => now(),
            ]);
        }

        // Prepare the QR content
        // This reproduces logic from QrCodeManagementController::generateQrContent
        // We need the token.
        $qrContent = null;
        if ($qrCode) {
            $url = route('public.branch', ['tiAgentCode' => $branch->ti_agent_code]);
            // Append token
            $url .= (str_contains($url, '?') ? '&' : '?') . 'token=' . $qrCode->validation_token;
            $qrContent = $url;
        }

        // Get primary color from settings
        $settings = Cache::get('system_settings', []);
        $primaryColor = $settings['primary_color'] ?? '#FE8000';

        return view('admin.qr-codes.display', [
            'branch' => $branch,
            'qrCode' => $qrCode,
            'qrContent' => $qrContent,
            'primaryColor' => $primaryColor,
        ]);
    }
    /**
     * Download the counter display as a PDF.
     */
    public function downloadPdf()
    {
        $user = auth()->user();

        // Ensure user has a branch
        if (!$user->branch_id) {
            abort(403, 'You must optionally be assigned to a branch to download the counter display.');
        }

        $branch = $user->branch;

        // Reuse logic to find or create QR code (same as display)
        // Find or create today's QR code for this branch
        $qrCode = QrCode::where('branch_id', $branch->id)
            ->where('type', 'branch')
            ->whereDate('created_at', today())
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$qrCode) {
            $qrCode = QrCode::where('branch_id', $branch->id)
                ->where('type', 'branch')
                ->where('expires_at', '>', now())
                ->latest()
                ->first();
        }

        if (!$qrCode) {
            // Generate on the fly if missing (same as display)
            $token = \Illuminate\Support\Str::random(32);
            $url = route('public.branch', ['tiAgentCode' => $branch->ti_agent_code]);
            $url .= (str_contains($url, '?') ? '&' : '?') . 'token=' . $token;

            $qrCode = QrCode::create([
                'name' => 'Branch QR - ' . now()->toDateString(),
                'branch_id' => $branch->id,
                'validation_token' => $token,
                'content' => $url,
                'expires_at' => today()->endOfDay(),
                'type' => 'branch',
                'created_by' => $user->id,
                'last_regenerated_at' => now(),
            ]);
        }

        $qrContent = null;
        if ($qrCode) {
            $url = route('public.branch', ['tiAgentCode' => $branch->ti_agent_code]);
            $url .= (str_contains($url, '?') ? '&' : '?') . 'token=' . $qrCode->validation_token;
            $qrContent = $url;
        }

        // Get primary color from settings
        $settings = Cache::get('system_settings', []);
        $primaryColor = $settings['primary_color'] ?? '#FE8000';

        // Load PDF View
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.qr-codes.display', [
            'branch' => $branch,
            'qrCode' => $qrCode,
            'qrContent' => $qrContent,
            'primaryColor' => $primaryColor,
            'isPdf' => true
        ]);

        return $pdf->setPaper('a4', 'portrait')
            ->stream('Counter_Display_' . $branch->ti_agent_code . '.pdf');
    }
}
