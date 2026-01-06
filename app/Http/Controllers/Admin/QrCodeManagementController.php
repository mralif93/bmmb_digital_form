<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\Branch;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class QrCodeManagementController extends Controller
{
    use LogsAuditTrail;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = QrCode::with(['branch', 'creator']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('branch', function ($branchQuery) use ($search) {
                        $branchQuery->where('branch_name', 'like', "%{$search}%")
                            ->orWhere('ti_agent_code', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by expired status
        if ($request->filled('expired')) {
            if ($request->expired === 'yes') {
                $query->where('expires_at', '<', now());
            } elseif ($request->expired === 'no') {
                $query->where(function ($q) {
                    $q->where('expires_at', '>=', now())
                        ->orWhereNull('expires_at');
                });
            }
        }

        $qrCodes = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get branches for filter dropdown
        $branches = Branch::orderBy('branch_name')->get();

        // Get timezone helper
        $timezoneHelper = app(\App\Helpers\TimezoneHelper::class);

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i:s';

        return view('admin.qr-codes.index', compact('qrCodes', 'branches', 'timezoneHelper', 'dateFormat', 'timeFormat'));
    }

    /**
     * Display a listing of trashed resources.
     */
    public function trashed(Request $request)
    {
        $query = QrCode::onlyTrashed()->with(['branch', 'creator']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('branch', function ($branchQuery) use ($search) {
                        $branchQuery->where('branch_name', 'like', "%{$search}%")
                            ->orWhere('ti_agent_code', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $qrCodes = $query->orderBy('deleted_at', 'desc')->paginate(15)->withQueryString();

        // Get branches for filter dropdown
        $branches = Branch::orderBy('branch_name')->get();

        return view('admin.qr-codes.trashed', compact('qrCodes', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::orderBy('branch_name')->get();
        return view('admin.qr-codes.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:branch,url,text,phone,email,sms,wifi,vcard',
            'content' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive',
            'size' => 'nullable|integer|min:100|max:1000',
            'format' => 'nullable|in:png,svg,jpg',
        ]);

        // Ensure lowercase for database storage
        $validated['type'] = strtolower($validated['type']);
        $validated['status'] = strtolower($validated['status']);
        $validated['size'] = $validated['size'] ?? 300;
        $validated['size'] = $validated['size'] ?? 300;
        $validated['format'] = 'svg'; // Force SVG format
        $validated['created_by'] = auth()->id();

        // Generate new validation token
        $validationToken = bin2hex(random_bytes(16));

        // Generate QR code content based on type
        $qrContent = $this->generateQrContent($validated['type'], $validated['content'], $validated['branch_id'] ?? null, $validationToken);

        $validated['qr_code_image'] = null; // No server-side image generation
        $validated['content'] = $qrContent;
        $validated['last_regenerated_at'] = now();
        $validated['expires_at'] = now()->addMinutes($this->getQrCodeExpirationMinutes());
        $validated['validation_token'] = $validationToken;

        $qrCode = QrCode::create($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: "Created QR code: {$qrCode->name} (Type: {$qrCode->type_display})",
            modelType: QrCode::class,
            modelId: $qrCode->id,
            newValues: $qrCode->toArray()
        );

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR code created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(QrCode $qr_code)
    {
        $qr_code->load(['branch', 'creator']);
        $timezoneHelper = app(\App\Helpers\TimezoneHelper::class);

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i:s';

        return view('admin.qr-codes.show', ['qrCode' => $qr_code, 'timezoneHelper' => $timezoneHelper, 'dateFormat' => $dateFormat, 'timeFormat' => $timeFormat]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QrCode $qr_code)
    {
        $branches = Branch::orderBy('branch_name')->get();
        return view('admin.qr-codes.edit', ['qrCode' => $qr_code, 'branches' => $branches]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QrCode $qr_code)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:branch,url,text,phone,email,sms,wifi,vcard',
            'content' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive',
            'size' => 'nullable|integer|min:100|max:1000',
            'format' => 'nullable|in:png,svg,jpg',
        ]);

        // Ensure lowercase for database storage
        $validated['type'] = strtolower($validated['type']);
        $validated['status'] = strtolower($validated['status']);
        $validated['size'] = $validated['size'] ?? 300;
        $validated['size'] = $validated['size'] ?? 300;
        $validated['format'] = 'svg'; // Force SVG format

        // Regenerate QR code if content, type, or format changed
        $needsRegeneration = $qr_code->content !== $validated['content']
            || $qr_code->type !== $validated['type']
            || $qr_code->format !== $validated['format']
            || $qr_code->size !== $validated['size'];

        if ($needsRegeneration) {
            // Generate new validation token when regenerating
            $validationToken = bin2hex(random_bytes(16));

            // Generate QR code content based on type
            $qrContent = $this->generateQrContent($validated['type'], $validated['content'], $validated['branch_id'] ?? null, $validationToken);

            // Delete old QR code image if it exists (cleanup)
            if ($qr_code->qr_code_image) {
                Storage::disk('public')->delete('qr-codes/' . $qr_code->qr_code_image);
            }

            $validated['qr_code_image'] = null;
            $validated['content'] = $qrContent;
            $validated['last_regenerated_at'] = now();
            $validated['expires_at'] = now()->addMinutes($this->getQrCodeExpirationMinutes());
            $validated['validation_token'] = $validationToken;
        }

        // Get old values before update, format dates consistently
        $oldValues = $qr_code->toArray();
        // Convert datetime objects to strings for consistent comparison
        foreach ($oldValues as $key => $value) {
            if ($value instanceof \Carbon\Carbon) {
                $oldValues[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        $qr_code->update($validated);
        $qr_code->refresh();

        // Get new values, format dates consistently
        $newValues = $qr_code->toArray();
        foreach ($newValues as $key => $value) {
            if ($value instanceof \Carbon\Carbon) {
                $newValues[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Updated QR code: {$qr_code->name} (Type: {$qr_code->type_display})",
            modelType: QrCode::class,
            modelId: $qr_code->id,
            oldValues: $oldValues,
            newValues: $newValues
        );

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR code updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QrCode $qr_code)
    {
        $oldValues = $qr_code->toArray();
        $qrCodeName = $qr_code->name;
        $qrCodeId = $qr_code->id;
        $qrCodeType = $qr_code->type_display;

        // Delete QR code image
        if ($qr_code->qr_code_image) {
            Storage::disk('public')->delete('qr-codes/' . $qr_code->qr_code_image);
        }

        $qr_code->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted QR code: {$qrCodeName} (Type: {$qrCodeType})",
            modelType: QrCode::class,
            modelId: $qrCodeId,
            oldValues: $oldValues
        );

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR code deleted successfully!');
    }

    /**
     * Generate QR code content based on type
     */
    private function generateQrContent(string $type, string $content, ?int $branchId = null, ?string $token = null): string
    {
        $type = strtolower($type); // Ensure lowercase
        switch ($type) {
            case 'branch':
                if ($branchId) {
                    $branch = Branch::find($branchId);
                    if ($branch) {
                        $params = ['tiAgentCode' => $branch->ti_agent_code];
                        if ($token) {
                            $params['token'] = $token;
                        }
                        return route('public.branch', $params);
                    }
                }
                return $content;
            case 'url':
                return $content;
            case 'text':
                return $content;
            case 'phone':
                return 'tel:' . $content;
            case 'email':
                return 'mailto:' . $content;
            case 'sms':
                return 'sms:' . $content;
            case 'wifi':
                return 'WIFI:T:' . $content . ';;';
            case 'vcard':
                return $content;
            default:
                return $content;
        }
    }

    /**
     * Regenerate a single QR code
     */
    public function regenerate(QrCode $qr_code)
    {
        try {
            if ($qr_code->status !== 'active') {
                return redirect()->route('admin.qr-codes.index')
                    ->with('error', 'Only active QR codes can be regenerated.');
            }

            // Generate new validation token
            $validationToken = bin2hex(random_bytes(16));

            // Generate QR code content based on type
            $qrContent = $this->generateQrContent($qr_code->type, $qr_code->content, $qr_code->branch_id, $validationToken);

            // Delete old QR code image
            if ($qr_code->qr_code_image) {
                Storage::disk('public')->delete('qr-codes/' . $qr_code->qr_code_image);
            }

            // Update QR code record with new expiration and token
            $qr_code->update([
                'qr_code_image' => null,
                'content' => $qrContent,
                'last_regenerated_at' => now(),
                'expires_at' => now()->addMinutes($this->getQrCodeExpirationMinutes()),
                'validation_token' => $validationToken,
            ]);

            // Log audit trail
            $this->logAuditTrail(
                action: 'update',
                description: "Regenerated QR code: {$qr_code->name}",
                modelType: QrCode::class,
                modelId: $qr_code->id
            );

            return redirect()->route('admin.qr-codes.index')
                ->with('success', 'QR code regenerated successfully! A new QR code image has been generated with a new token. Old QR codes are now invalid.');
        } catch (\Exception $e) {
            return redirect()->route('admin.qr-codes.index')
                ->with('error', 'Failed to regenerate QR code: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate all active QR codes
     */
    public function regenerateAll(Request $request)
    {
        try {
            // Run the artisan command with --all flag to regenerate ALL active QR codes
            \Artisan::call('qr-codes:regenerate', ['--all' => true]);

            $output = \Artisan::output();

            // Log audit trail
            $this->logAuditTrail(
                action: 'update',
                description: 'Regenerated all active QR codes',
                modelType: null,
                modelId: null
            );

            return redirect()->route('admin.qr-codes.index')
                ->with('success', 'QR codes regenerated successfully! All active QR codes have been refreshed with new tokens. Old QR codes are now invalid.');
        } catch (\Exception $e) {
            return redirect()->route('admin.qr-codes.index')
                ->with('error', 'Failed to regenerate QR codes: ' . $e->getMessage());
        }
    }

    /**
     * Get QR code expiration minutes from settings
     */
    private function getQrCodeExpirationMinutes(): int
    {
        $settings = Cache::get('system_settings', []);

        // If auto-generation is enabled, use the frequency to determine expiration
        if ($settings['qr_code_auto_generate'] ?? true) {
            $frequency = $settings['qr_code_auto_gen_frequency'] ?? 'daily';

            switch ($frequency) {
                case 'weekly':
                    return 10080; // 7 days * 24 * 60
                case 'monthly':
                    return 43200; // 30 days * 24 * 60
                case 'quarterly':
                    return 129600; // 90 days * 24 * 60
                case 'yearly':
                    return 525600; // 365 days * 24 * 60
                case 'daily':
                default:
                    return 1440; // 24 hours * 60
            }
        }

        // Fallback to manual setting if auto-generation is disabled
        return (int) ($settings['qr_code_expiration_minutes'] ?? 60);
    }

    /**
     * Restore a soft-deleted QR code.
     */
    public function restore($id)
    {
        try {
            $qrCode = QrCode::onlyTrashed()->findOrFail($id);
            $qrCode->restore();

            // Log audit trail
            $this->logAuditTrail(
                action: 'restore',
                description: "Restored QR code: {$qrCode->name}",
                modelType: QrCode::class,
                modelId: $qrCode->id
            );

            return redirect()->route('admin.qr-codes.trashed')
                ->with('success', 'QR code restored successfully!');
        } catch (\Exception $e) {
            \Log::error("Error restoring QR code ID {$id}: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to restore QR code: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a QR code.
     */
    public function forceDelete($id)
    {
        try {
            $qrCode = QrCode::onlyTrashed()->findOrFail($id);
            $qrCodeName = $qrCode->name;
            $oldValues = $qrCode->toArray();

            // Delete QR code image if exists
            if ($qrCode->qr_code_image) {
                Storage::disk('public')->delete('qr-codes/' . $qrCode->qr_code_image);
            }

            $qrCode->forceDelete();

            // Log audit trail
            $this->logAuditTrail(
                action: 'force_delete',
                description: "Permanently deleted QR code: {$qrCodeName}",
                modelType: QrCode::class,
                modelId: $id,
                oldValues: $oldValues
            );

            // Check if there are any more trashed QR codes
            $remainingTrashedCount = QrCode::onlyTrashed()->count();

            if ($remainingTrashedCount > 0) {
                // Stay on trashed view
                return redirect()->route('admin.qr-codes.trashed')
                    ->with('success', 'QR code permanently deleted successfully!');
            } else {
                // No more trashed QR codes, go back to active list
                return redirect()->route('admin.qr-codes.index')
                    ->with('success', 'QR code permanently deleted successfully! No more trashed QR codes.');
            }
        } catch (\Exception $e) {
            \Log::error("Error force deleting QR code ID {$id}: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete QR code: ' . $e->getMessage());
        }
    }
}
