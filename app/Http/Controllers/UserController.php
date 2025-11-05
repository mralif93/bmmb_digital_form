<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use LogsAuditTrail;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,user,moderator',
            'status' => 'required|in:active,inactive,suspended',
            'bio' => 'nullable|string|max:1000',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Log audit trail (exclude password from logging)
        $userData = $user->toArray();
        unset($userData['password']);
        $this->logAuditTrail(
            action: 'create',
            description: "Created user: {$user->full_name} ({$user->email})",
            modelType: User::class,
            modelId: $user->id,
            newValues: $userData
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,user,moderator',
            'status' => 'required|in:active,inactive,suspended',
            'bio' => 'nullable|string|max:1000',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        // Get old values before update, format dates consistently
        $oldValues = $user->toArray();
        unset($oldValues['password']);
        foreach ($oldValues as $key => $value) {
            if ($value instanceof \Carbon\Carbon) {
                $oldValues[$key] = $value->format('Y-m-d H:i:s');
            }
        }
        
        $user->update($validated);
        $user->refresh();

        // Get new values, format dates consistently
        $userData = $user->toArray();
        unset($userData['password']);
        foreach ($userData as $key => $value) {
            if ($value instanceof \Carbon\Carbon) {
                $userData[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        // Log audit trail (exclude password from logging)
        $this->logAuditTrail(
            action: 'update',
            description: "Updated user: {$user->full_name} ({$user->email})",
            modelType: User::class,
            modelId: $user->id,
            oldValues: $oldValues,
            newValues: $userData
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $oldValues = $user->toArray();
        unset($oldValues['password']);
        $userName = $user->full_name;
        $userEmail = $user->email;
        $userId = $user->id;
        
        $user->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted user: {$userName} ({$userEmail})",
            modelType: User::class,
            modelId: $userId,
            oldValues: $oldValues
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        $oldStatus = $user->status;
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        
        $user->update([
            'status' => $newStatus
        ]);

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Changed user status: {$user->full_name} from {$oldStatus} to {$newStatus}",
            modelType: User::class,
            modelId: $user->id,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $newStatus]
        );

        return redirect()->back()
            ->with('success', 'User status updated successfully.');
    }
}
