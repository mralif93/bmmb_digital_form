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
        return view('admin.branches.show', compact('branch'));
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
}
