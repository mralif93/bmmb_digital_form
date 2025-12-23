<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        $timezoneHelper = app(\App\Helpers\TimezoneHelper::class);
        return view('admin.profile', compact('user', 'timezoneHelper'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Get old values before update, format dates consistently
        $oldValues = $user->toArray();
        unset($oldValues['password']);
        foreach ($oldValues as $key => $value) {
            if ($value instanceof \Carbon\Carbon) {
                $oldValues[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        // Only update password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->refresh();

        // Get new values, format dates consistently
        $newValues = $user->toArray();
        unset($newValues['password']);
        foreach ($newValues as $key => $value) {
            if ($value instanceof \Carbon\Carbon) {
                $newValues[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        // Log audit trail (exclude password from logging)
        $this->logAuditTrail(
            action: 'update',
            description: "Updated profile: {$user->full_name} ({$user->email})",
            modelType: \App\Models\User::class,
            modelId: $user->id,
            oldValues: $oldValues,
            newValues: $newValues
        );

        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Changed password: {$user->full_name} ({$user->email})",
            modelType: \App\Models\User::class,
            modelId: $user->id
        );

        return redirect()->route('admin.profile')
            ->with('success', 'Password updated successfully!');
    }
}