<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;

class StateController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = State::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $states = $query->withCount('branches')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.states.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.states.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:states,name',
        ]);

        $state = State::create($validated);

        $this->logAuditTrail(
            action: 'create',
            description: "Created state: {$state->name}",
            modelType: State::class,
            modelId: $state->id,
            newValues: $state->toArray()
        );

        return redirect()->route('admin.states.index')
            ->with('success', 'State created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        $state->load(['branches' => fn($q) => $q->orderBy('branch_name')->limit(10)]);
        return view('admin.states.show', compact('state'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state)
    {
        return view('admin.states.edit', compact('state'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:states,name,' . $state->id,
        ]);

        $oldValues = $state->toArray();
        $state->update($validated);

        $this->logAuditTrail(
            action: 'update',
            description: "Updated state: {$state->name}",
            modelType: State::class,
            modelId: $state->id,
            oldValues: $oldValues,
            newValues: $state->toArray()
        );

        return redirect()->route('admin.states.index')
            ->with('success', 'State updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        if ($state->branches()->exists()) {
            return redirect()->route('admin.states.index')
                ->with('error', 'Cannot delete state with associated branches.');
        }

        $oldValues = $state->toArray();
        $stateName = $state->name;
        $stateId = $state->id;

        $state->delete();

        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted state: {$stateName}",
            modelType: State::class,
            modelId: $stateId,
            oldValues: $oldValues
        );

        return redirect()->route('admin.states.index')
            ->with('success', 'State deleted successfully!');
    }
}
