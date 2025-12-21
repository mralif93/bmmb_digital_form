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
    public function show(Region $region)
    {
        $region->load(['branches' => fn($q) => $q->orderBy('branch_name')->limit(10)]);
        return view('admin.regions.show', compact('region'));
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
}
