<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Region::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $regions = $query->withCount('branches')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.regions.index', compact('regions'));
    }

    /**
     * Display a listing of trashed resources.
     */
    public function trashed(Request $request)
    {
        $query = Region::onlyTrashed();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $regions = $query->withCount('branches')->orderBy('deleted_at', 'desc')->paginate(15)->withQueryString();

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'd/m/Y';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.regions.trashed', compact('regions', 'dateFormat', 'timeFormat'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.regions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:regions,name',
        ]);

        $region = Region::create($validated);

        $this->logAuditTrail(
            action: 'create',
            description: "Created region: {$region->name}",
            modelType: Region::class,
            modelId: $region->id,
            newValues: $region->toArray()
        );

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $region = Region::withTrashed()->findOrFail($id);
        $region->load(['branches' => fn($q) => $q->orderBy('branch_name')->limit(10)]);
        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'd/m/Y';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.regions.show', compact('region', 'dateFormat', 'timeFormat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Region $region)
    {
        return view('admin.regions.edit', compact('region'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:regions,name,' . $region->id,
        ]);

        $oldValues = $region->toArray();
        $region->update($validated);

        $this->logAuditTrail(
            action: 'update',
            description: "Updated region: {$region->name}",
            modelType: Region::class,
            modelId: $region->id,
            oldValues: $oldValues,
            newValues: $region->toArray()
        );

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region updated successfully!');
    }

    /**
     * Resync regions from MAP database.
     */
    public function resync()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('map:migrate-regions');

            return redirect()->back()->with('success', 'Region synchronization completed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to sync regions: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $region)
    {
        if ($region->branches()->exists()) {
            return redirect()->route('admin.regions.index')
                ->with('error', 'Cannot delete region with associated branches.');
        }

        $oldValues = $region->toArray();
        $regionName = $region->name;
        $regionId = $region->id;

        $region->delete();

        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted region: {$regionName}",
            modelType: Region::class,
            modelId: $regionId,
            oldValues: $oldValues
        );

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region deleted successfully!');
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore($id)
    {
        $region = Region::onlyTrashed()->findOrFail($id);
        $region->restore();

        $this->logAuditTrail(
            action: 'restore',
            description: "Restored region: {$region->name}",
            modelType: Region::class,
            modelId: $region->id,
            newValues: $region->toArray()
        );

        return redirect()->route('admin.regions.index', ['trashed' => 'true'])
            ->with('success', 'Region restored successfully!');
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete($id)
    {
        $region = Region::onlyTrashed()->findOrFail($id);

        if ($region->branches()->withTrashed()->exists()) {
            return redirect()->route('admin.regions.index', ['trashed' => 'true'])
                ->with('error', 'Cannot permanently delete region with associated branches (even if deleted).');
        }

        $oldValues = $region->toArray();
        $regionName = $region->name;
        $regionId = $region->id;

        $region->forceDelete();

        $this->logAuditTrail(
            action: 'force_delete',
            description: "Permanently deleted region: {$regionName}",
            modelType: Region::class,
            modelId: $regionId,
            oldValues: $oldValues
        );

        if (Region::onlyTrashed()->exists()) {
            return redirect()->route('admin.regions.index', ['trashed' => 'true'])
                ->with('success', 'Region permanently deleted successfully!');
        }

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region permanently deleted successfully!');
    }
}
