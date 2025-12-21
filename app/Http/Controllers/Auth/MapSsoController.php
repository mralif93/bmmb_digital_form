<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\MapAuthService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MapSsoController extends Controller
{
    use LogsAuditTrail;

    protected $mapAuthService;

    public function __construct(MapAuthService $mapAuthService)
    {
        $this->mapAuthService = $mapAuthService;
    }

    /**
     * Handle MAP SSO login
     *
     * This is the entry point when users are redirected from MAP
     */
    public function login(Request $request)
    {
        // Check if user already authenticated in eform
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        // Get MAP session cookie
        $sessionCookie = $this->mapAuthService->getSessionCookie($request);

        if (!$sessionCookie) {
            // No MAP session - show login page with button to redirect to MAP
            Log::info('No MAP session cookie found, showing login page');
            return redirect()->route('map.login.page');
        }

        // Verify MAP session and sync user
        $user = $this->mapAuthService->verifyAndSyncUser($sessionCookie);

        if (!$user) {
            // Verification failed - redirect to MAP login
            Log::warning('MAP session verification failed, redirecting to MAP');
            return redirect($this->getMapLoginUrl())
                ->with('error', 'Session verification failed. Please login again.');
        }

        // Check if user has required permissions (HQ/BM/CFE only)
        if (!$this->userHasAccess($user)) {
            Log::warning('User does not have eform access', [
                'user_id' => $user->id,
                'role' => $user->role,
                'map_position' => $user->map_position
            ]);

            return redirect($this->getMapLoginUrl())
                ->with('error', 'You do not have permission to access E-form. Please contact administrator.');
        }

        // Log user in to eform
        Auth::login($user);

        // Log audit trail
        $this->logAuditTrail(
            action: 'map_sso_login',
            description: "User logged in via MAP SSO: {$user->full_name} ({$user->email})",
            modelType: get_class($user),
            modelId: $user->id
        );

        Log::info('User logged in via MAP SSO', [
            'user_id' => $user->id,
            'email' => $user->email,
            'username' => $user->username
        ]);

        // Redirect to dashboard
        return $this->redirectToDashboard($user);
    }

    /**
     * Handle SSO callback with signed token from MAP
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ssoCallback(Request $request)
    {
        // Check if already authenticated
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        $token = $request->query('token');

        if (!$token) {
            Log::warning('SSO callback missing token');
            return redirect()->route('map.login.page')
                ->with('error', 'Invalid SSO request. Please try again.');
        }

        // Parse and verify token
        $userData = $this->verifyAndParseToken($token);

        if (!$userData) {
            Log::warning('SSO token verification failed');
            return redirect()->route('map.login.page')
                ->with('error', 'SSO session expired or invalid. Please try again.');
        }

        // Create or update user from token data
        $user = $this->syncUserFromToken($userData);

        if (!$user) {
            Log::error('Failed to sync user from SSO token', ['data' => $userData]);
            return redirect()->route('map.login.page')
                ->with('error', 'Failed to create user account. Please contact administrator.');
        }

        // Log user in
        Auth::login($user);

        // Log audit trail
        $this->logAuditTrail(
            action: 'map_sso_token_login',
            description: "User logged in via MAP SSO token: {$user->full_name} ({$user->email})",
            modelType: get_class($user),
            modelId: $user->id
        );

        Log::info('User logged in via MAP SSO token', [
            'user_id' => $user->id,
            'email' => $user->email,
            'username' => $user->username
        ]);

        // Redirect to dashboard
        return $this->redirectToDashboard($user);
    }

    /**
     * Verify and parse SSO token
     *
     * @param string $token
     * @return array|null User data if valid, null otherwise
     */
    private function verifyAndParseToken(string $token): ?array
    {
        // Token format: base64_data.signature
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            Log::warning('SSO token invalid format');
            return null;
        }

        [$tokenB64, $signature] = $parts;

        // Get shared secret (should match Django's SECRET_KEY or EFORM_SSO_SECRET)
        $secretKey = config('map.sso_secret', config('app.key'));

        // Verify HMAC signature
        $expectedSignature = hash_hmac('sha256', $tokenB64, $secretKey);
        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('SSO token signature mismatch');
            return null;
        }

        // Decode token data
        $tokenJson = base64_decode(strtr($tokenB64, '-_', '+/'));
        if (!$tokenJson) {
            Log::warning('SSO token base64 decode failed');
            return null;
        }

        $tokenData = json_decode($tokenJson, true);
        if (!$tokenData) {
            Log::warning('SSO token JSON decode failed');
            return null;
        }

        // Check token expiry (60 seconds)
        $timestamp = $tokenData['timestamp'] ?? 0;
        if (time() - $timestamp > 60) {
            Log::warning('SSO token expired', ['timestamp' => $timestamp, 'age' => time() - $timestamp]);
            return null;
        }

        return $tokenData;
    }

    /**
     * Sync user from SSO token data
     *
     * @param array $tokenData
     * @return \App\Models\User|null
     */
    private function syncUserFromToken(array $tokenData): ?\App\Models\User
    {
        try {
            // Map MAP position to eform role
            $role = $this->mapPositionToRole($tokenData['position'] ?? '1');

            $user = \App\Models\User::updateOrCreate(
                ['map_user_id' => $tokenData['user_id']],
                [
                    'username' => $tokenData['username'] ?? null,
                    'email' => $tokenData['email'],
                    'first_name' => $tokenData['first_name'],
                    'last_name' => $tokenData['last_name'],
                    'map_position' => $tokenData['position'],
                    'role' => $role,
                    'branch_id' => $tokenData['branch_id'] ?? null,
                    'status' => 'active',
                    'map_last_sync' => now(),
                    'is_map_synced' => true,
                    'last_login_at' => now(),
                    'password' => bcrypt(\Illuminate\Support\Str::random(32)),
                ]
            );

            return $user;
        } catch (\Exception $e) {
            Log::error('Error syncing user from SSO token', [
                'error' => $e->getMessage(),
                'data' => $tokenData
            ]);
            return null;
        }
    }

    /**
     * Map MAP position code to eform role
     *
     * @param string $position
     * @return string
     */
    private function mapPositionToRole(string $position): string
    {
        return match ($position) {
            '1' => 'headquarters',
            '2' => 'branch_manager',
            '3' => 'cfe', // CFE → Customer Finance Executive
            '4' => 'headquarters',
            '9' => 'operation_officer',
            '10' => 'operation_officer',
            default => 'headquarters',
        };
    }

    /**
     * Check if user has access to eform (HQ/BM/CFE only)
     *
     * @param \App\Models\User $user
     * @return bool
     */
    private function userHasAccess($user): bool
    {
        // Allow these MAP positions: 1=HQ, 2=BM, 3=CFE
        $allowedPositions = ['1', '2', '3'];

        return in_array($user->map_position, $allowedPositions);
    }

    /**
     * Redirect user to appropriate dashboard based on role
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectToDashboard($user)
    {
        // Admin, staff roles go to admin dashboard
        if ($user->hasAdminAccess()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Get MAP login redirect URL
     *
     * @return string
     */
    private function getMapLoginUrl(): string
    {
        // For local development, use localhost; for production use the configured URL
        return config('services.map.redirect_url', 'http://localhost:8000/redirect/eform/');
    }

    /**
     * Handle logout - redirect to MAP
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log audit trail for logout
        if ($user) {
            $this->logAuditTrail(
                action: 'map_sso_logout',
                description: "User logged out (MAP SSO): {$user->full_name} ({$user->email})",
                modelType: get_class($user),
                modelId: $user->id
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to MAP logout (federated logout - also logs out from MAP)
        return redirect(config('map.logout_url', 'http://127.0.0.1:8000/pengurusan/logout/'));
    }

    /**
     * Development auto-login (for local testing with migrated MAP credentials)
     *
     * This route allows testing SSO flow locally using migrated credentials
     * It should only be used in development/local environment
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function devLogin(Request $request)
    {
        // Check if already authenticated
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        // Get credentials from query parameters
        $username = $request->query('username');
        $password = $request->query('password');

        if (!$username || !$password) {
            Log::warning('Dev login: Missing username or password');
            return redirect()->route('map.login.page')
                ->with('error', 'Username and password are required for dev login.');
        }

        // Attempt to authenticate using Laravel's Auth
        $credentials = [
            'username' => $username,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Log audit trail
            $this->logAuditTrail(
                action: 'dev_login',
                description: "User logged in via dev login: {$user->full_name} ({$user->email})",
                modelType: get_class($user),
                modelId: $user->id
            );

            Log::info('User logged in via dev login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'username' => $user->username
            ]);

            return $this->redirectToDashboard($user);
        }

        // Auth failed - log and show error
        Log::warning('Dev login: Authentication failed', ['username' => $username]);
        return redirect()->route('map.login.page')
            ->with('error', 'Invalid username or password.');
    }
}