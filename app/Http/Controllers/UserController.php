<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
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
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->with('branch')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.users.index', compact('users', 'dateFormat', 'timeFormat'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::orderBy('branch_name')->get();
        return view('admin.users.create', compact('branches'));
    }

    /**
     * Get user create form for modal (AJAX)
     */
    public function createModal()
    {
        try {
            $branches = Branch::orderBy('branch_name')->get();

            // Render the user create form partial
            $html = view('admin.users.modal-create', compact('branches'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading create modal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading create form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'role' => 'required|in:admin,branch_manager,assistant_branch_manager,operation_officer,headquarters,iam',
                'status' => 'required|in:active,inactive,suspended',
                'bio' => 'nullable|string|max:1000',
                'branch_id' => 'nullable|exists:branches,id',
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

            // Check if request came from dashboard (IAM users)
            $isFromDashboard = $request->header('Referer') && str_contains($request->header('Referer'), '/dashboard');
            $redirectUrl = $isFromDashboard ? route('admin.dashboard') : route('admin.users.index');

            // If AJAX request, return JSON response with redirect URL
            if ($request->ajax() || $request->wantsJson()) {
                // Store success message in session for the redirect
                session()->flash('success', 'User created successfully.');

                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully.',
                    'redirect' => $redirectUrl
                ]);
            }

            return redirect($redirectUrl)
                ->with('success', 'User created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // If AJAX request, return JSON response with validation errors
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());

            // If AJAX request, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating user: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating user. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::withTrashed()->with('branch')->findOrFail($id);

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.users.show', compact('user', 'dateFormat', 'timeFormat'));
    }

    /**
     * Get user details for modal (AJAX)
     */
    public function details(User $user)
    {
        $user->load('branch');

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        // Render the user details partial
        $html = view('admin.users.modal-content', compact('user', 'dateFormat', 'timeFormat'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $branches = Branch::orderBy('branch_name')->get();
        return view('admin.users.edit', compact('user', 'branches'));
    }

    /**
     * Get user edit form for modal (AJAX)
     */
    public function editModal(User $user)
    {
        $branches = Branch::orderBy('branch_name')->get();

        // Render the user edit form partial
        $html = view('admin.users.modal-edit', compact('user', 'branches'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'phone' => 'nullable|string|max:20',
                'role' => 'required|in:admin,branch_manager,assistant_branch_manager,operation_officer,headquarters,iam',
                'status' => 'required|in:active,inactive,suspended',
                'bio' => 'nullable|string|max:1000',
                'branch_id' => 'nullable|exists:branches,id',
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

            // Check if request came from dashboard (IAM users)
            $isFromDashboard = $request->header('Referer') && str_contains($request->header('Referer'), '/dashboard');
            $redirectUrl = $isFromDashboard ? route('admin.dashboard') : route('admin.users.index');

            // If AJAX request, return JSON response with redirect URL
            if ($request->ajax() || $request->wantsJson()) {
                // Store success message in session for the redirect
                session()->flash('success', 'User updated successfully.');

                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.',
                    'redirect' => $redirectUrl
                ]);
            }

            return redirect($redirectUrl)
                ->with('success', 'User updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // If AJAX request, return JSON response with validation errors
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());

            // If AJAX request, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating user: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating user. Please try again.');
        }
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
     * Toggle user status between active and inactive.
     */
    public function toggleStatus(User $user)
    {
        $oldStatus = $user->status;

        // Only toggle between active and inactive
        // If suspended, change to inactive
        if ($oldStatus === 'active') {
            $newStatus = 'inactive';
        } else {
            // For inactive or suspended, set to active
            $newStatus = 'active';
        }

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

        // If AJAX request, return JSON response
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully.',
                'status' => $newStatus
            ]);
        }

        return redirect()->back()
            ->with('success', 'User status updated successfully.');
    }

    /**
     * Verify user email address.
     */
    public function verifyEmail(User $user)
    {
        if ($user->email_verified_at) {
            // If AJAX request, return JSON response
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already verified.'
                ], 400);
            }

            return redirect()->back()
                ->with('error', 'Email is already verified.');
        }

        $oldEmailVerifiedAt = $user->email_verified_at;

        $user->update([
            'email_verified_at' => now()
        ]);

        // Refresh the model to get the updated value
        $user->refresh();

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Manually verified email for user: {$user->full_name} ({$user->email})",
            modelType: User::class,
            modelId: $user->id,
            oldValues: ['email_verified_at' => $oldEmailVerifiedAt],
            newValues: ['email_verified_at' => $user->email_verified_at]
        );

        // If AJAX request, return JSON response
        if (request()->ajax() || request()->wantsJson()) {
            $verifiedAt = \App\Helpers\TimezoneHelper::toSystemTimezone($user->email_verified_at);
            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully.',
                'verified_at' => $verifiedAt ? $verifiedAt->format('M d, Y h:i A') : null
            ]);
        }

        return redirect()->back()
            ->with('success', 'Email verified successfully.');
    }

    /**
     * Unverify user email address.
     */
    public function unverifyEmail(User $user)
    {
        if (!$user->email_verified_at) {
            // If AJAX request, return JSON response
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is not verified.'
                ], 400);
            }

            return redirect()->back()
                ->with('error', 'Email is not verified.');
        }

        $oldEmailVerifiedAt = $user->email_verified_at;

        $user->update([
            'email_verified_at' => null
        ]);

        // Refresh the model to get the updated value
        $user->refresh();

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Manually unverified email for user: {$user->full_name} ({$user->email})",
            modelType: User::class,
            modelId: $user->id,
            oldValues: ['email_verified_at' => $oldEmailVerifiedAt],
            newValues: ['email_verified_at' => null]
        );

        // If AJAX request, return JSON response
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email unverified successfully.'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Email unverified successfully.');
    }

    /**
     * Display a listing of trashed (soft deleted) users.
     */
    public function trashed(Request $request)
    {
        $query = User::onlyTrashed();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('deleted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('deleted_at', '<=', $request->date_to);
        }

        $users = $query->with('branch')->orderBy('deleted_at', 'desc')->paginate(15)->withQueryString();

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.users.trashed', compact('users', 'dateFormat', 'timeFormat'));
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (!$user->trashed()) {
            return redirect()
                ->route('admin.users.show', $user->id)
                ->with('error', 'User is not deleted.');
        }

        $user->restore();

        // Log audit trail
        $this->logAuditTrail(
            action: 'restore',
            description: "Restored user: {$user->full_name} ({$user->email})",
            modelType: User::class,
            modelId: $user->id,
            newValues: ['restored_at' => now()]
        );

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'User restored successfully.');
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (!$user->trashed()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'User is not deleted. Use delete instead.');
        }

        $oldValues = $user->toArray();
        unset($oldValues['password']);
        $userName = $user->full_name;
        $userEmail = $user->email;
        $userId = $user->id;

        $user->forceDelete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'force_delete',
            description: "Permanently deleted user: {$userName} ({$userEmail})",
            modelType: User::class,
            modelId: $userId,
            oldValues: $oldValues
        );

        return redirect()
            ->route('admin.users.trashed')
            ->with('success', 'User permanently deleted successfully.');
    }
}
