<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use LogsAuditTrail;
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        
        // Check if user exists and is active
        $user = User::where('email', $request->email)->first();
        
        if ($user && $user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is not active. Please contact administrator.',
            ])->withInput();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Update last login info
            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Log audit trail for login
            $this->logAuditTrail(
                action: 'login',
                description: "User logged in: {$user->full_name} ({$user->email})",
                modelType: User::class,
                modelId: $user->id
            );

            // Redirect based on user role
            // Admin, staff roles (HQ, BM, ABM, OO), and IAM go to admin dashboard
            if ($user->isAdmin() || $user->isHQ() || $user->isBM() || $user->isABM() || $user->isOO() || $user->isIAM()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Log audit trail for registration
        $userData = $user->toArray();
        unset($userData['password']);
        $this->logAuditTrail(
            action: 'create',
            description: "User registered: {$user->full_name} ({$user->email})",
            modelType: User::class,
            modelId: $user->id,
            newValues: $userData
        );

        // Auto login after registration
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log audit trail for logout (before logout)
        if ($user) {
            $this->logAuditTrail(
                action: 'logout',
                description: "User logged out: {$user->full_name} ({$user->email})",
                modelType: User::class,
                modelId: $user->id
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}
