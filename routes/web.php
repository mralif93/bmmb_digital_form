<?php

use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\FormBuilderController;
use App\Http\Controllers\Admin\FormSectionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\QrCodeManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\MapSsoController;
use App\Http\Controllers\Public\BranchController as PublicBranchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

// Wrap all routes with /eform prefix when ROUTE_PREFIX is set
Route::group(['prefix' => env('ROUTE_PREFIX', '')], function () {

    // MAP SSO Routes (Primary Authentication)
    Route::get('/map/login', [MapSsoController::class, 'login'])->name('map.login');
    Route::get('/map/login-page', function () {
        return view('map-login');
    })->name('map.login.page');
    Route::get('/map/sso', [MapSsoController::class, 'ssoCallback'])->name('map.sso');
    Route::post('/map/logout', [MapSsoController::class, 'logout'])->name('map.logout');

    // Development Auto-Login Route (for local testing with migrated MAP credentials)
    Route::get('/map/dev-login', [MapSsoController::class, 'devLogin'])->name('map.dev.login');

    // Public Routes - Home route
    Route::get('/', function () {
        // Redirect to MAP SSO if not authenticated
        if (!Auth::check()) {
            // Direct redirect to MAP login page
            $mapLoginUrl = config('map.login_url', 'http://127.0.0.1:8000/pengurusan/login/');
            return redirect($mapLoginUrl);
        }
        return view('welcome');
    })->name('home');

    // Public Branch Route (accessed via QR code)
    Route::get('/branch/{tiAgentCode}', [PublicBranchController::class, 'show'])->name('public.branch');

    // Public Form Routes (with optional branch parameter)
    Route::prefix('forms')->name('public.forms.')->group(function () {
        // Legacy routes for backward compatibility (raf, dar, dcr, srf)
        Route::get('/raf/{branch?}', function ($branch = null) {
            return app(\App\Http\Controllers\Public\FormController::class)->show('raf', $branch);
        })->name('raf');

        Route::get('/dar/{branch?}', function ($branch = null) {
            return app(\App\Http\Controllers\Public\FormController::class)->show('dar', $branch);
        })->name('dar');

        Route::get('/dcr/{branch?}', function ($branch = null) {
            return app(\App\Http\Controllers\Public\FormController::class)->show('dcr', $branch);
        })->name('dcr');

        Route::get('/srf/{branch?}', function ($branch = null) {
            return app(\App\Http\Controllers\Public\FormController::class)->show('srf', $branch);
        })->name('srf');

        // Success page after form submission (MUST be before catch-all dynamic route)
        Route::get('/success/{submissionToken}', [\App\Http\Controllers\Public\FormSubmissionController::class, 'success'])
            ->name('success');

        // PDF Preview for printing/downloading
        Route::get('/pdf/{submissionToken}', [\App\Http\Controllers\Public\FormSubmissionController::class, 'pdfPreview'])
            ->name('pdf.preview');

        // Public Form Submission Routes
        Route::post('/{type}/submit', [\App\Http\Controllers\Public\FormSubmissionController::class, 'store'])
            ->where(['type' => '[a-z0-9-]+'])
            ->name('submit');

        // Dynamic form route for new form management system (catch-all - must be last)
        Route::get('/{slug}/{branch?}', [\App\Http\Controllers\Public\FormController::class, 'showBySlug'])
            ->where('slug', '[a-z0-9-]+')
            ->name('slug');
    });

    // Auth routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('auth')->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            $user = auth()->user();

            if (!$user) {
                \Log::error('Dashboard: User not authenticated');
                abort(403, 'User not authenticated');
            }

            // Initialize base stats - Common stats for all roles
            $stats = [
                'total_forms' => \App\Models\Form::count(),
                'total_active_forms' => \App\Models\Form::where('status', 'active')->count(),
                'active_forms' => \App\Models\Form::where('status', 'active')->count(), // Alias for compatibility
                'total_form_submissions' => \App\Models\FormSubmission::count(),
                'total_submissions' => \App\Models\FormSubmission::count(), // Alias for compatibility
                'total_completed_submissions' => \App\Models\FormSubmission::where('status', 'completed')->count(),
            ];

            $topForms = collect();
            $submissionCounts = [];
            $recentSubmissions = collect();
            $mySubmissions = collect();

            if ($user->isAdmin()) {
                // Admin: Full system stats (merge with common stats)
                $stats = array_merge($stats, [
                    'active_forms' => \App\Models\Form::where('status', 'active')->count(),
                    'total_submissions' => \App\Models\FormSubmission::count(),
                    'approved_submissions' => \App\Models\FormSubmission::where('status', 'approved')->count(),
                    'pending_submissions' => \App\Models\FormSubmission::whereIn('status', ['submitted', 'under_review'])->count(),
                    'rejected_submissions' => \App\Models\FormSubmission::where('status', 'rejected')->count(),
                    'active_users' => \App\Models\User::where('status', 'active')->count(),
                    'total_branches' => \App\Models\Branch::count(),
                    'total_qr_codes' => \App\Models\QrCode::count(),
                ]);

                $stats['conversion_rate'] = $stats['total_submissions'] > 0
                    ? round(($stats['approved_submissions'] / $stats['total_submissions']) * 100)
                    : 0;

                // Top performing forms
                $topForms = \App\Models\Form::withCount('submissions')
                    ->where('status', 'active')
                    ->orderBy('submissions_count', 'desc')
                    ->limit(3)
                    ->get();

                // Submission counts by form type
                $submissionCounts = [
                    'raf' => \App\Models\FormSubmission::whereHas('form', function ($q) {
                        $q->where('slug', 'raf');
                    })->count(),
                    'dar' => \App\Models\FormSubmission::whereHas('form', function ($q) {
                        $q->where('slug', 'dar');
                    })->count(),
                    'dcr' => \App\Models\FormSubmission::whereHas('form', function ($q) {
                        $q->where('slug', 'dcr');
                    })->count(),
                    'srf' => \App\Models\FormSubmission::whereHas('form', function ($q) {
                        $q->where('slug', 'srf');
                    })->count(),
                ];

                // Recent submissions
                $recentSubmissions = \App\Models\FormSubmission::with(['form', 'user', 'branch'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

            } elseif ($user->isHQ()) {
                // HQ: All submissions overview
                $stats = [
                    'total_submissions' => \App\Models\FormSubmission::count(),
                    'approved_submissions' => \App\Models\FormSubmission::where('status', 'approved')->count(),
                    'pending_submissions' => \App\Models\FormSubmission::whereIn('status', ['submitted', 'under_review'])->count(),
                    'rejected_submissions' => \App\Models\FormSubmission::where('status', 'rejected')->count(),
                    'in_progress_submissions' => \App\Models\FormSubmission::where('status', 'in_progress')->count(),
                    'completed_submissions' => \App\Models\FormSubmission::where('status', 'completed')->count(),
                ];

                $stats['conversion_rate'] = $stats['total_submissions'] > 0
                    ? round(($stats['approved_submissions'] / $stats['total_submissions']) * 100)
                    : 0;

                // Top performing forms
                $topForms = \App\Models\Form::withCount('submissions')
                    ->where('status', 'active')
                    ->orderBy('submissions_count', 'desc')
                    ->limit(3)
                    ->get();

                // Submission counts by form type
                $submissionCounts = [
                    'raf' => \App\Models\FormSubmission::whereHas('form', function ($q) {
                        $q->where('slug', 'raf');
                    })->count(),
                    'dar' => \App\Models\FormSubmission::whereHas('form', function ($q) {
                        $q->where('slug', 'dar');
                    })->count(),
                    'dcr' => \App\Models\FormSubmission::whereHas('form', function ($q) {
                        $q->where('slug', 'dcr');
                    })->count(),
                    'srf' => \App\Models\FormSubmission::whereHas('form', function ($q) {
                        $q->where('slug', 'srf');
                    })->count(),
                ];

                // Recent submissions
                $recentSubmissions = \App\Models\FormSubmission::with(['form', 'user', 'branch'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

            } elseif ($user->isBM()) {
                // BM: Branch submissions overview WITH take-up/complete workflow
                $branchId = $user->branch_id;
                $branchQuery = $branchId ? \App\Models\FormSubmission::where('branch_id', $branchId) : \App\Models\FormSubmission::whereNull('branch_id');

                $stats = [
                    'available_to_take_up' => (clone $branchQuery)->where('status', 'submitted')->count(),
                    'pending_process' => (clone $branchQuery)->where('status', 'pending_process')->count(),
                    'taken_up_by_me' => (clone $branchQuery)->where('taken_up_by', $user->id)->where('status', 'pending_process')->count(),
                    'completed_by_me' => \App\Models\FormSubmission::where('completed_by', $user->id)
                        ->where('status', 'completed')
                        ->whereMonth('completed_at', now()->month)
                        ->count(),
                    'total_completed' => \App\Models\FormSubmission::where('completed_by', $user->id)->where('status', 'completed')->count(),
                    'branch_submissions' => $branchQuery->count(),
                    'branch_approved' => (clone $branchQuery)->where('status', 'approved')->count(),
                    'branch_pending' => (clone $branchQuery)->whereIn('status', ['submitted', 'under_review'])->count(),
                    'branch_rejected' => (clone $branchQuery)->where('status', 'rejected')->count(),
                    'branch_in_progress' => (clone $branchQuery)->where('status', 'in_progress')->count(),
                    'branch_completed' => (clone $branchQuery)->where('status', 'completed')->count(),
                ];

                $stats['completion_rate'] = $stats['taken_up_by_me'] > 0
                    ? round(($stats['total_completed'] / ($stats['taken_up_by_me'] + $stats['total_completed'])) * 100)
                    : 0;

                $stats['branch_conversion_rate'] = $stats['branch_submissions'] > 0
                    ? round(($stats['branch_approved'] / $stats['branch_submissions']) * 100)
                    : 0;

                // Submission counts by form type (branch)
                $submissionCounts = [
                    'raf' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'raf');
                    })->count(),
                    'dar' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'dar');
                    })->count(),
                    'dcr' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'dcr');
                    })->count(),
                    'srf' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'srf');
                    })->count(),
                ];

                // Available submissions to take up
                $availableSubmissions = (clone $branchQuery)
                    ->where('status', 'submitted')
                    ->with(['form', 'user', 'branch'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                // Pending process submissions (ready to complete)
                $pendingProcessSubmissions = (clone $branchQuery)
                    ->where('status', 'pending_process')
                    ->with(['form', 'user', 'branch', 'takenUpBy'])
                    ->orderBy('taken_up_at', 'desc')
                    ->limit(5)
                    ->get();

                // My recent completions
                $myCompletions = \App\Models\FormSubmission::with(['form', 'branch'])
                    ->where('completed_by', $user->id)
                    ->where('status', 'completed')
                    ->orderBy('completed_at', 'desc')
                    ->limit(5)
                    ->get();

                // Recent branch submissions
                $recentSubmissions = (clone $branchQuery)
                    ->with(['form', 'user', 'branch'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();


            } elseif ($user->isABM()) {
                // ABM: Branch submissions overview WITH take-up/complete workflow (same as BM)
                $branchId = $user->branch_id;
                $branchQuery = $branchId ? \App\Models\FormSubmission::where('branch_id', $branchId) : \App\Models\FormSubmission::whereNull('branch_id');

                $stats = [
                    'available_to_take_up' => (clone $branchQuery)->where('status', 'submitted')->count(),
                    'pending_process' => (clone $branchQuery)->where('status', 'pending_process')->count(),
                    'taken_up_by_me' => (clone $branchQuery)->where('taken_up_by', $user->id)->where('status', 'pending_process')->count(),
                    'completed_by_me' => \App\Models\FormSubmission::where('completed_by', $user->id)
                        ->where('status', 'completed')
                        ->whereMonth('completed_at', now()->month)
                        ->count(),
                    'total_completed' => \App\Models\FormSubmission::where('completed_by', $user->id)->where('status', 'completed')->count(),
                    'branch_submissions' => $branchQuery->count(),
                    'branch_approved' => (clone $branchQuery)->where('status', 'approved')->count(),
                    'branch_pending' => (clone $branchQuery)->whereIn('status', ['submitted', 'under_review'])->count(),
                    'branch_rejected' => (clone $branchQuery)->where('status', 'rejected')->count(),
                    'branch_in_progress' => (clone $branchQuery)->where('status', 'in_progress')->count(),
                    'branch_completed' => (clone $branchQuery)->where('status', 'completed')->count(),
                ];

                $stats['completion_rate'] = $stats['taken_up_by_me'] > 0
                    ? round(($stats['total_completed'] / ($stats['taken_up_by_me'] + $stats['total_completed'])) * 100)
                    : 0;

                $stats['branch_conversion_rate'] = $stats['branch_submissions'] > 0
                    ? round(($stats['branch_approved'] / $stats['branch_submissions']) * 100)
                    : 0;

                // Submission counts by form type (branch)
                $submissionCounts = [
                    'raf' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'raf');
                    })->count(),
                    'dar' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'dar');
                    })->count(),
                    'dcr' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'dcr');
                    })->count(),
                    'srf' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'srf');
                    })->count(),
                ];

                // Available submissions to take up
                $availableSubmissions = (clone $branchQuery)
                    ->where('status', 'submitted')
                    ->with(['form', 'user', 'branch'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                // Pending process submissions (ready to complete)
                $pendingProcessSubmissions = (clone $branchQuery)
                    ->where('status', 'pending_process')
                    ->with(['form', 'user', 'branch', 'takenUpBy'])
                    ->orderBy('taken_up_at', 'desc')
                    ->limit(5)
                    ->get();

                // My recent completions
                $myCompletions = \App\Models\FormSubmission::with(['form', 'branch'])
                    ->where('completed_by', $user->id)
                    ->where('status', 'completed')
                    ->orderBy('completed_at', 'desc')
                    ->limit(5)
                    ->get();

                // Recent branch submissions
                $recentSubmissions = (clone $branchQuery)
                    ->with(['form', 'user', 'branch'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

            } elseif ($user->isCFE()) {
                // CFE: Customer Finance Executive dashboard
                $branchId = $user->branch_id;
                $branchQuery = $branchId ? \App\Models\FormSubmission::where('branch_id', $branchId) : \App\Models\FormSubmission::whereNull('branch_id');

                $stats = [
                    'available_to_take_up' => (clone $branchQuery)->where('status', 'submitted')->count(),
                    'pending_process' => (clone $branchQuery)->where('status', 'pending_process')->count(),
                    'taken_up_by_me' => (clone $branchQuery)->where('taken_up_by', $user->id)->where('status', 'pending_process')->count(),
                    'completed_by_me' => \App\Models\FormSubmission::where('completed_by', $user->id)
                        ->where('status', 'completed')
                        ->whereMonth('completed_at', now()->month)
                        ->count(),
                    'total_completed' => \App\Models\FormSubmission::where('completed_by', $user->id)->where('status', 'completed')->count(),
                    'branch_submissions' => $branchQuery->count(),
                ];

                $stats['completion_rate'] = $stats['taken_up_by_me'] > 0
                    ? round(($stats['total_completed'] / ($stats['taken_up_by_me'] + $stats['total_completed'])) * 100)
                    : 0;

                // Submission counts by form type (branch)
                $submissionCounts = [
                    'raf' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'raf');
                    })->count(),
                    'dar' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'dar');
                    })->count(),
                    'dcr' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'dcr');
                    })->count(),
                    'srf' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'srf');
                    })->count(),
                ];

                // Available submissions to take up
                $availableSubmissions = (clone $branchQuery)
                    ->where('status', 'submitted')
                    ->with(['form', 'user', 'branch'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                // Pending process submissions (ready to complete)
                $pendingProcessSubmissions = (clone $branchQuery)
                    ->where('status', 'pending_process')
                    ->with(['form', 'user', 'branch', 'takenUpBy'])
                    ->orderBy('taken_up_at', 'desc')
                    ->limit(5)
                    ->get();

                // My recent completions
                $myCompletions = \App\Models\FormSubmission::with(['form', 'branch'])
                    ->where('completed_by', $user->id)
                    ->where('status', 'completed')
                    ->orderBy('completed_at', 'desc')
                    ->limit(5)
                    ->get();

            } elseif ($user->isOO()) {
                // OO: Operations Officer dashboard
                $branchId = $user->branch_id;
                $branchQuery = $branchId ? \App\Models\FormSubmission::where('branch_id', $branchId) : \App\Models\FormSubmission::whereNull('branch_id');

                $stats = [
                    'available_to_take_up' => (clone $branchQuery)->where('status', 'submitted')->count(),
                    'pending_process' => (clone $branchQuery)->where('status', 'pending_process')->count(),
                    'taken_up_by_me' => (clone $branchQuery)->where('taken_up_by', $user->id)->where('status', 'pending_process')->count(),
                    'completed_by_me' => \App\Models\FormSubmission::where('completed_by', $user->id)
                        ->where('status', 'completed')
                        ->whereMonth('completed_at', now()->month)
                        ->count(),
                    'total_completed' => \App\Models\FormSubmission::where('completed_by', $user->id)->where('status', 'completed')->count(),
                    'branch_submissions' => $branchQuery->count(),
                ];

                $stats['completion_rate'] = $stats['taken_up_by_me'] > 0
                    ? round(($stats['total_completed'] / ($stats['taken_up_by_me'] + $stats['total_completed'])) * 100)
                    : 0;

                // Submission counts by form type (branch)
                $submissionCounts = [
                    'raf' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'raf');
                    })->count(),
                    'dar' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'dar');
                    })->count(),
                    'dcr' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'dcr');
                    })->count(),
                    'srf' => (clone $branchQuery)->whereHas('form', function ($q) {
                        $q->where('slug', 'srf');
                    })->count(),
                ];

                // Available submissions to take up
                $availableSubmissions = (clone $branchQuery)
                    ->where('status', 'submitted')
                    ->with(['form', 'user', 'branch'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                // Pending process submissions (ready to complete)
                $pendingProcessSubmissions = (clone $branchQuery)
                    ->where('status', 'pending_process')
                    ->with(['form', 'user', 'branch', 'takenUpBy'])
                    ->orderBy('taken_up_at', 'desc')
                    ->limit(5)
                    ->get();

                // My recent completions
                $myCompletions = \App\Models\FormSubmission::with(['form', 'branch'])
                    ->where('completed_by', $user->id)
                    ->where('status', 'completed')
                    ->orderBy('completed_at', 'desc')
                    ->limit(5)
                    ->get();
            } else {
                // Fallback: Their own submissions
                $stats = [
                    'my_submissions' => \App\Models\FormSubmission::where('user_id', $user->id)->count(),
                    'my_approved' => \App\Models\FormSubmission::where('user_id', $user->id)->where('status', 'approved')->count(),
                    'my_pending' => \App\Models\FormSubmission::where('user_id', $user->id)->whereIn('status', ['submitted', 'under_review'])->count(),
                    'my_rejected' => \App\Models\FormSubmission::where('user_id', $user->id)->where('status', 'rejected')->count(),
                    'my_in_progress' => \App\Models\FormSubmission::where('user_id', $user->id)->where('status', 'in_progress')->count(),
                    'my_completed' => \App\Models\FormSubmission::where('user_id', $user->id)->where('status', 'completed')->count(),
                ];

                $stats['my_conversion_rate'] = $stats['my_submissions'] > 0
                    ? round(($stats['my_approved'] / $stats['my_submissions']) * 100)
                    : 0;

                // My submissions by form type
                $submissionCounts = [
                    'raf' => \App\Models\FormSubmission::where('user_id', $user->id)->whereHas('form', function ($q) {
                        $q->where('slug', 'raf');
                    })->count(),
                    'dar' => \App\Models\FormSubmission::where('user_id', $user->id)->whereHas('form', function ($q) {
                        $q->where('slug', 'dar');
                    })->count(),
                    'dcr' => \App\Models\FormSubmission::where('user_id', $user->id)->whereHas('form', function ($q) {
                        $q->where('slug', 'dcr');
                    })->count(),
                    'srf' => \App\Models\FormSubmission::where('user_id', $user->id)->whereHas('form', function ($q) {
                        $q->where('slug', 'srf');
                    })->count(),
                ];

                // My recent submissions
                $mySubmissions = \App\Models\FormSubmission::with(['form', 'branch'])
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }

            // Prepare variables for view based on role
            $viewData = compact('stats', 'topForms', 'submissionCounts', 'recentSubmissions', 'mySubmissions', 'user');

            // Add workflow data for BM, ABM, CFE, and OO (take-up/complete workflow)
            if ($user->isBM() || $user->isABM() || $user->isCFE() || $user->isOO()) {
                $viewData['availableSubmissions'] = $availableSubmissions ?? collect();
                $viewData['pendingProcessSubmissions'] = $pendingProcessSubmissions ?? collect();
                $viewData['myCompletions'] = $myCompletions ?? collect();
            }

            $request = request();

            // For IAM users, show users list instead of form submissions
            if ($user->isIAM()) {
                // Get paginated users (latest to oldest) with search and filters
                $usersQuery = \App\Models\User::with('branch');

                // Search functionality
                if ($request->filled('search')) {
                    $search = $request->search;
                    $usersQuery->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                }

                // Filter by role
                if ($request->filled('role')) {
                    $usersQuery->where('role', $request->role);
                }

                // Filter by status
                if ($request->filled('status')) {
                    $usersQuery->where('status', $request->status);
                }

                // Filter by branch
                if ($request->filled('branch_id')) {
                    $usersQuery->where('branch_id', $request->branch_id);
                }

                // Order by latest first
                $users = $usersQuery->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

                // Get branches for filter dropdown
                $branches = \App\Models\Branch::orderBy('branch_name')->get();

                $viewData['users'] = $users;
                $viewData['branches'] = $branches;
                $viewData['submissions'] = collect(); // Empty for IAM
                $viewData['forms'] = collect(); // Empty for IAM
            } else {
                // Get paginated form submissions (latest to oldest) with search and filters
                $submissionsQuery = \App\Models\FormSubmission::with(['form', 'user', 'branch']);

                // Apply role-based branch filtering
                $branchFilterApplied = false;
                $branchIdForFilter = null;

                if (!$user->isAdmin() && !$user->isHQ()) {
                    // BM, ABM, OO: Only submissions from their branch
                    if ($user->branch_id) {
                        $branchIdForFilter = $user->branch_id;
                        $submissionsQuery->where('branch_id', $user->branch_id);
                        $branchFilterApplied = true;
                    } else {
                        // If user has no branch assigned, show no submissions
                        $submissionsQuery->whereRaw('1 = 0');
                        $branchFilterApplied = true;
                    }
                } elseif (($user->isAdmin() || $user->isHQ()) && $request->filled('branch_id')) {
                    // Admin and HQ can filter by branch via dropdown
                    $branchIdForFilter = $request->branch_id;
                    $submissionsQuery->where('branch_id', $request->branch_id);
                    $branchFilterApplied = true;
                }

                // Ensure common stats are always available (filtered by branch if applicable)
                if (!isset($stats['total_forms'])) {
                    $stats['total_forms'] = \App\Models\Form::count();
                }
                if (!isset($stats['total_active_forms'])) {
                    $stats['total_active_forms'] = \App\Models\Form::where('status', 'active')->count();
                }
                if (!isset($stats['total_form_submissions'])) {
                    $submissionsCountQuery = \App\Models\FormSubmission::query();
                    if ($branchFilterApplied && $branchIdForFilter) {
                        $submissionsCountQuery->where('branch_id', $branchIdForFilter);
                    }
                    $stats['total_form_submissions'] = $submissionsCountQuery->count();
                }
                if (!isset($stats['total_completed_submissions'])) {
                    $completedCountQuery = \App\Models\FormSubmission::where('status', 'completed');
                    if ($branchFilterApplied && $branchIdForFilter) {
                        $completedCountQuery->where('branch_id', $branchIdForFilter);
                    }
                    $stats['total_completed_submissions'] = $completedCountQuery->count();
                }

                // Search functionality
                if ($request->filled('search')) {
                    $search = $request->search;
                    $submissionsQuery->where(function ($q) use ($search) {
                        $q->where('id', 'like', "%{$search}%")
                            ->orWhere('submission_token', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->orWhereHas('branch', function ($branchQuery) use ($search) {
                                $branchQuery->where('branch_name', 'like', "%{$search}%")
                                    ->orWhere('ti_agent_code', 'like', "%{$search}%");
                            })
                            ->orWhereHas('form', function ($formQuery) use ($search) {
                                $formQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('slug', 'like', "%{$search}%");
                            });
                    });
                }

                // Filter by status
                if ($request->filled('status')) {
                    $submissionsQuery->where('status', $request->status);
                }

                // Note: Branch filtering is already applied above based on role

                // Filter by form
                if ($request->filled('form_id')) {
                    $submissionsQuery->where('form_id', $request->form_id);
                }

                // Order by latest first
                $submissions = $submissionsQuery->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

                // Get branches for filter dropdown (Admin and HQ only)
                $branches = collect();
                if ($user->isAdmin() || $user->isHQ()) {
                    $branches = \App\Models\Branch::orderBy('branch_name')->get();
                }

                // Get forms for filter dropdown
                $forms = \App\Models\Form::where('status', 'active')->orderBy('name')->get();

                $viewData['submissions'] = $submissions;
                $viewData['branches'] = $branches;
                $viewData['forms'] = $forms;
                $viewData['users'] = collect(); // Empty for non-IAM
            }

            $viewData['stats'] = $stats;

            return view('admin.dashboard', $viewData);
        })->name('dashboard');

        // User Management (Admin and IAM)
        Route::middleware('admin-or-iam')->group(function () {
            // Custom routes must be defined before resource routes to avoid conflicts
            Route::get('/users/create-modal', [UserController::class, 'createModal'])->name('users.create-modal');
            Route::get('/users/{user}/details', [UserController::class, 'details'])->name('users.details');
            Route::get('/users/{user}/edit-modal', [UserController::class, 'editModal'])->name('users.edit-modal');
            Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::post('/users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email');
            Route::post('/users/{user}/unverify-email', [UserController::class, 'unverifyEmail'])->name('users.unverify-email');
            Route::get('/users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
            Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
            Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
            Route::resource('users', UserController::class);
        });

        // Branch Management (Admin, HQ, and IAM)
        Route::middleware('admin-or-hq-or-iam')->group(function () {
            Route::resource('branches', BranchController::class);
            Route::resource('states', \App\Http\Controllers\Admin\StateController::class);
            Route::resource('regions', \App\Http\Controllers\Admin\RegionController::class);
        });

        // QR Code Management (Admin and HQ)
        Route::middleware('admin-or-hq')->group(function () {
            // QR Code Generation Routes
            Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
                Route::post('/generate', [QrCodeController::class, 'generate'])->name('generate');
                Route::post('/bulk-generate', [QrCodeController::class, 'bulkGenerate'])->name('bulk-generate');
                Route::get('/download/{fileName}', [QrCodeController::class, 'download'])->name('download');
            });

            // QR Code Management (CRUD)
            Route::resource('qr-codes', QrCodeManagementController::class);
            Route::post('/qr-codes/{qr_code}/regenerate', [QrCodeManagementController::class, 'regenerate'])->name('qr-codes.regenerate');
            Route::post('/qr-codes/regenerate-all', [QrCodeManagementController::class, 'regenerateAll'])->name('qr-codes.regenerate-all');
        });

        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        // System Settings (Admin and HQ only)
        Route::middleware('admin-or-hq')->group(function () {
            Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
            Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        });

        // Project Information (Admin Only)
        Route::middleware('admin-only')->group(function () {
            Route::get('/information', [\App\Http\Controllers\Admin\InformationController::class, 'index'])->name('information');
        });

        // Audit Trail (Admin Only)
        Route::middleware('admin-only')->group(function () {
            Route::get('/audit-trails', [AuditTrailController::class, 'index'])->name('audit-trails.index');
            Route::get('/audit-trails/{auditTrail}', [AuditTrailController::class, 'show'])->name('audit-trails.show');
        });

        // Dynamic Forms Management (Admin and HQ)
        Route::middleware('admin-or-hq')->group(function () {
            Route::resource('forms', FormController::class);
            Route::post('/forms/reorder', [FormController::class, 'reorder'])->name('forms.reorder');
            Route::get('/forms/{form}/export', [FormController::class, 'export'])->name('forms.export');
            Route::post('/forms/import', [FormController::class, 'import'])->name('forms.import');

            // Form Sections Management
            Route::prefix('forms/{form}/sections')->name('form-sections.')->group(function () {
                Route::get('/', [FormSectionController::class, 'index'])->name('index');
                Route::get('/create', [FormSectionController::class, 'create'])->name('create');
                Route::post('/', [FormSectionController::class, 'store'])->name('store');
                Route::get('/{section}', [FormSectionController::class, 'show'])->name('show');
                Route::get('/{section}/edit', [FormSectionController::class, 'edit'])->name('edit');
                Route::put('/{section}', [FormSectionController::class, 'update'])->name('update');
                Route::delete('/{section}', [FormSectionController::class, 'destroy'])->name('destroy');
                Route::post('/reorder', [FormSectionController::class, 'reorder'])->name('reorder');
                Route::patch('/{section}/grid-layout', [FormSectionController::class, 'updateGridLayout'])->name('grid-layout.update');
            });

            // Form Builder Routes
            Route::prefix('forms/{form}/builder')->name('form-builder.')->group(function () {
                Route::get('/', [FormBuilderController::class, 'index'])->name('index');
                Route::get('/trashed', [FormBuilderController::class, 'trashed'])->name('trashed');
                Route::get('/fields/{field}', [FormBuilderController::class, 'getField'])->name('fields.show');
                Route::get('/fields/{field}/view', [FormBuilderController::class, 'show'])->name('fields.view');
                Route::post('/fields', [FormBuilderController::class, 'storeField'])->name('fields.store');
                Route::put('/fields/{field}', [FormBuilderController::class, 'updateField'])->name('fields.update');
                Route::delete('/fields/{field}', [FormBuilderController::class, 'destroyField'])->name('fields.destroy');
                Route::post('/fields/reorder', [FormBuilderController::class, 'reorderFields'])->name('fields.reorder');
                Route::put('/fields/{field}/column', [FormBuilderController::class, 'updateFieldColumn'])->name('fields.column');
                Route::post('/fields/{field}/restore', [FormBuilderController::class, 'restore'])->name('fields.restore');
                Route::delete('/fields/{field}/force', [FormBuilderController::class, 'forceDelete'])->name('fields.force-delete');
            });
        });

        // Form Submissions (Dynamic)
        Route::prefix('submissions')->name('submissions.')->group(function () {
            // Legacy routes for backward compatibility (must come before dynamic routes)
            Route::get('/raf', [SubmissionController::class, 'raf'])->name('raf');
            Route::get('/dar', [SubmissionController::class, 'dar'])->name('dar');
            Route::get('/dcr', [SubmissionController::class, 'dcr'])->name('dcr');
            Route::get('/srf', [SubmissionController::class, 'srf'])->name('srf');
            Route::get('/show-raf/{id}', [SubmissionController::class, 'showRaf'])->name('show-raf');
            Route::get('/show-dar/{id}', [SubmissionController::class, 'showDar'])->name('show-dar');
            Route::get('/show-dcr/{id}', [SubmissionController::class, 'showDcr'])->name('show-dcr');
            Route::get('/show-srf/{id}', [SubmissionController::class, 'showSrf'])->name('show-srf');

            // Dynamic routes for new form management system (must come after legacy routes)
            Route::get('/{formSlug}/trashed', [SubmissionController::class, 'trashed'])->name('trashed');
            Route::get('/{formSlug}', [SubmissionController::class, 'index'])->name('index');

            // Create and Store (Admin Only) - must come before show route
            Route::middleware('admin-only')->group(function () {
                Route::get('/{formSlug}/create', [SubmissionController::class, 'create'])->name('create');
                Route::post('/{formSlug}', [SubmissionController::class, 'store'])->name('store');
            });

            Route::get('/{formSlug}/{id}', [SubmissionController::class, 'show'])->name('show');
            Route::get('/{formSlug}/{id}/details', [SubmissionController::class, 'details'])->name('details');
            Route::get('/{formSlug}/{id}/pdf', [SubmissionController::class, 'pdfPreview'])->name('pdf');

            // OO Actions: Take Up and Complete
            Route::post('/{formSlug}/{id}/take-up', [SubmissionController::class, 'takeUp'])->name('take-up');
            Route::post('/{formSlug}/{id}/complete', [SubmissionController::class, 'complete'])->name('complete');

            // Edit, Update, Delete, Restore, and Force Delete (Admin Only)
            Route::middleware('admin-only')->group(function () {
                Route::get('/{formSlug}/{id}/edit', [SubmissionController::class, 'edit'])->name('edit');
                Route::put('/{formSlug}/{id}', [SubmissionController::class, 'update'])->name('update');
                Route::delete('/{formSlug}/{id}', [SubmissionController::class, 'destroy'])->name('destroy');
                Route::post('/{formSlug}/{id}/restore', [SubmissionController::class, 'restore'])->name('restore');
                Route::delete('/{formSlug}/{id}/force', [SubmissionController::class, 'forceDelete'])->name('force-delete');
                Route::put('/{formSlug}/{id}/status', [SubmissionController::class, 'updateStatus'])->name('status.update');
            });
        });
    });

}); // End of ROUTE_PREFIX group
