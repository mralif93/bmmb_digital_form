<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use LogsAuditTrail;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Branch::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('branch_name', 'like', "%{$search}%")
                    ->orWhere('ti_agent_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('stateRelation', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('regionRelation', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state_id', $request->state);
        }

        // Filter by region
        if ($request->filled('region')) {
            $query->where('region_id', $request->region);
        }

        $branches = $query->orderBy('branch_name')->paginate(15)->withQueryString();

        return view('admin.branches.index', compact('branches'));
    }

    /**
     * Display a listing of trashed resources.
     */
    public function trashed(Request $request)
    {
        $query = Branch::onlyTrashed();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('branch_name', 'like', "%{$search}%")
                    ->orWhere('ti_agent_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('stateRelation', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('regionRelation', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by state
        if ($request->filled('state')) {
            $query->where('state_id', $request->state);
        }

        // Filter by region
        if ($request->filled('region')) {
            $query->where('region_id', $request->region);
        }

        $branches = $query->orderBy('deleted_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.branches.trashed', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_name' => 'required|string|max:255',
            'weekend_start_day' => 'required|in:MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY',
            'ti_agent_code' => 'required|string|max:255|unique:branches,ti_agent_code',
            'address' => 'required|string',
            'email' => 'required|email|max:255|unique:branches,email',
            'state_id' => 'required|exists:states,id',
            'region_id' => 'required|exists:regions,id',
        ]);

        $branch = Branch::create($validated);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: "Created branch: {$branch->branch_name} (TI Code: {$branch->ti_agent_code})",
            modelType: Branch::class,
            modelId: $branch->id,
            newValues: $branch->toArray()
        );

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        $timezoneHelper = app(\App\Helpers\TimezoneHelper::class);

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i:s';

        return view('admin.branches.show', compact('branch', 'timezoneHelper', 'dateFormat', 'timeFormat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'branch_name' => 'required|string|max:255',
            'weekend_start_day' => 'required|in:MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY',
            'ti_agent_code' => 'required|string|max:255|unique:branches,ti_agent_code,' . $branch->id,
            'address' => 'required|string',
            'email' => 'required|email|max:255|unique:branches,email,' . $branch->id,
            'state_id' => 'required|exists:states,id',
            'region_id' => 'required|exists:regions,id',
        ]);

        // Get old values before update, format dates consistently
        $oldValues = $branch->toArray();
        foreach ($oldValues as $key => $value) {
            if ($value instanceof \Carbon\Carbon) {
                $oldValues[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        $branch->update($validated);
        $branch->refresh();

        // Get new values, format dates consistently
        $newValues = $branch->toArray();
        foreach ($newValues as $key => $value) {
            if ($value instanceof \Carbon\Carbon) {
                $newValues[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Updated branch: {$branch->branch_name} (TI Code: {$branch->ti_agent_code})",
            modelType: Branch::class,
            modelId: $branch->id,
            oldValues: $oldValues,
            newValues: $newValues
        );

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch updated successfully!');
    }

    /**
     * Resync branches from MAP database.
     */
    public function resync()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('map:sync-branches', [
                '--include-regions' => false,
                '--include-states' => false,
            ]);

            return redirect()->back()->with('success', 'Branch synchronization started successfully. Check logs for details.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to start synchronization: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate QR Code for a specific branch.
     */
    public function regenerateQr(Branch $branch)
    {
        $user = auth()->user();

        // 1. Find existing active Branch QR for this branch
        $existingQr = \App\Models\QrCode::where('branch_id', $branch->id)
            ->where('type', 'branch')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        // 2. Invalidate existing QR (delete record)
        if ($existingQr) {
            $existingQr->delete();
        }

        // 3. Generate new details
        $token = \Illuminate\Support\Str::random(32);
        $url = route('public.branch', ['tiAgentCode' => $branch->ti_agent_code]);
        $url .= (str_contains($url, '?') ? '&' : '?') . 'token=' . $token;

        // Get expiration from settings
        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $expirationMinutes = $settings['qr_code_expiration_minutes'] ?? 1440; // Default 24 hours

        // 4. Create new QR Code record
        $newQr = \App\Models\QrCode::create([
            'name' => 'Branch QR - ' . now()->toDateString(),
            'branch_id' => $branch->id,
            'validation_token' => $token,
            'content' => $url,
            'expires_at' => now()->addMinutes($expirationMinutes),
            'type' => 'branch',
            'created_by' => $user->id,
            'last_regenerated_at' => now(),
        ]);

        // 5. Log audit trail
        $this->logAuditTrail(
            action: 'regenerate_qr',
            description: "Regenerated QR Code for branch: {$branch->branch_name}",
            modelType: Branch::class,
            modelId: $branch->id,
            newValues: ['qr_code_id' => $newQr->id]
        );

        return redirect()->back()->with('success', 'QR Code regenerated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $oldValues = $branch->toArray();
        $branchName = $branch->branch_name;
        $branchId = $branch->id;

        $branch->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted branch: {$branchName} (TI Code: {$oldValues['ti_agent_code']})",
            modelType: Branch::class,
            modelId: $branchId,
            oldValues: $oldValues
        );

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch deleted successfully!');
    }

    /**
     * Restore a soft-deleted branch.
     */
    public function restore($id)
    {
        try {
            $branch = Branch::onlyTrashed()->findOrFail($id);
            $branch->restore();

            // Log audit trail
            $this->logAuditTrail(
                action: 'restore',
                description: "Restored branch: {$branch->branch_name} (TI Code: {$branch->ti_agent_code})",
                modelType: Branch::class,
                modelId: $branch->id
            );

            return redirect()->route('admin.branches.index', ['trashed' => 'true'])
                ->with('success', 'Branch restored successfully!');
        } catch (\Exception $e) {
            \Log::error("Error restoring branch ID {$id}: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to restore branch: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a branch.
     */
    public function forceDelete($id)
    {
        try {
            $branch = Branch::onlyTrashed()->findOrFail($id);
            $branchName = $branch->branch_name;
            $oldValues = $branch->toArray();

            $branch->forceDelete();

            // Log audit trail
            $this->logAuditTrail(
                action: 'force_delete',
                description: "Permanently deleted branch: {$branchName}",
                modelType: Branch::class,
                modelId: $id,
                oldValues: $oldValues
            );

            // Check if there are any more trashed branches
            $remainingTrashedCount = Branch::onlyTrashed()->count();

            if ($remainingTrashedCount > 0) {
                // Stay on trashed view
                return redirect()->route('admin.branches.index', ['trashed' => 'true'])
                    ->with('success', 'Branch permanently deleted successfully!');
            } else {
                // No more trashed branches, go back to active list
                return redirect()->route('admin.branches.index')
                    ->with('success', 'Branch permanently deleted successfully! No more trashed branches.');
            }
        } catch (\Exception $e) {
            \Log::error("Error force deleting branch ID {$id}: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete branch: ' . $e->getMessage());
        }
    }
}
