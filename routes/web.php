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
use App\Http\Controllers\Public\BranchController as PublicBranchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

// Public Routes - Home route
Route::get('/', function () {
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
    
    // Dynamic form route for new form management system
    Route::get('/{slug}/{branch?}', [\App\Http\Controllers\Public\FormController::class, 'showBySlug'])
        ->where('slug', '[a-z0-9-]+')
        ->name('slug');
    
    // Public Form Submission Routes
    Route::post('/{type}/submit', [\App\Http\Controllers\Public\FormSubmissionController::class, 'store'])
        ->where(['type' => '[a-z0-9-]+'])
        ->name('submit');
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
        
        // Initialize base stats
        $stats = [];
        $topForms = collect();
        $submissionCounts = [];
        $recentSubmissions = collect();
        $mySubmissions = collect();
        
        if ($user->isAdmin()) {
            // Admin: Full system stats
            $stats = [
                'total_forms' => \App\Models\Form::count(),
                'active_forms' => \App\Models\Form::where('status', 'active')->count(),
                'total_submissions' => \App\Models\FormSubmission::count(),
                'approved_submissions' => \App\Models\FormSubmission::where('status', 'approved')->count(),
                'pending_submissions' => \App\Models\FormSubmission::whereIn('status', ['submitted', 'under_review'])->count(),
                'rejected_submissions' => \App\Models\FormSubmission::where('status', 'rejected')->count(),
                'active_users' => \App\Models\User::where('status', 'active')->count(),
                'total_branches' => \App\Models\Branch::count(),
                'total_qr_codes' => \App\Models\QrCode::count(),
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
                'raf' => \App\Models\FormSubmission::whereHas('form', function($q) { $q->where('slug', 'raf'); })->count(),
                'dar' => \App\Models\FormSubmission::whereHas('form', function($q) { $q->where('slug', 'dar'); })->count(),
                'dcr' => \App\Models\FormSubmission::whereHas('form', function($q) { $q->where('slug', 'dcr'); })->count(),
                'srf' => \App\Models\FormSubmission::whereHas('form', function($q) { $q->where('slug', 'srf'); })->count(),
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
                'raf' => \App\Models\FormSubmission::whereHas('form', function($q) { $q->where('slug', 'raf'); })->count(),
                'dar' => \App\Models\FormSubmission::whereHas('form', function($q) { $q->where('slug', 'dar'); })->count(),
                'dcr' => \App\Models\FormSubmission::whereHas('form', function($q) { $q->where('slug', 'dcr'); })->count(),
                'srf' => \App\Models\FormSubmission::whereHas('form', function($q) { $q->where('slug', 'srf'); })->count(),
            ];
            
            // Recent submissions
            $recentSubmissions = \App\Models\FormSubmission::with(['form', 'user', 'branch'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
        } else {
            // BM, ABM, OO: Their own submissions
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
                'raf' => \App\Models\FormSubmission::where('user_id', $user->id)->whereHas('form', function($q) { $q->where('slug', 'raf'); })->count(),
                'dar' => \App\Models\FormSubmission::where('user_id', $user->id)->whereHas('form', function($q) { $q->where('slug', 'dar'); })->count(),
                'dcr' => \App\Models\FormSubmission::where('user_id', $user->id)->whereHas('form', function($q) { $q->where('slug', 'dcr'); })->count(),
                'srf' => \App\Models\FormSubmission::where('user_id', $user->id)->whereHas('form', function($q) { $q->where('slug', 'srf'); })->count(),
            ];
            
            // My recent submissions
            $mySubmissions = \App\Models\FormSubmission::with(['form', 'branch'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('admin.dashboard', compact('stats', 'topForms', 'submissionCounts', 'recentSubmissions', 'mySubmissions', 'user'));
    })->name('dashboard');
    
        // User Management (Admin Only)
        Route::middleware('admin-only')->group(function () {
            Route::resource('users', UserController::class);
            Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        });
        
        // Branch Management (Admin Only)
        Route::middleware('admin-only')->group(function () {
            Route::resource('branches', BranchController::class);
        });
        
        // QR Code Management (Admin Only)
        Route::middleware('admin-only')->group(function () {
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
    
    // System Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
    // Audit Trail (Admin Only)
    Route::middleware('admin-only')->group(function () {
        Route::get('/audit-trails', [AuditTrailController::class, 'index'])->name('audit-trails.index');
        Route::get('/audit-trails/{auditTrail}', [AuditTrailController::class, 'show'])->name('audit-trails.show');
    });
    
    // Dynamic Forms Management (Admin Only)
    Route::middleware('admin-only')->group(function () {
        Route::resource('forms', FormController::class);
        Route::post('/forms/reorder', [FormController::class, 'reorder'])->name('forms.reorder');
        
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
        });

        // Form Builder Routes
        Route::prefix('forms/{form}/builder')->name('form-builder.')->group(function () {
            Route::get('/', [FormBuilderController::class, 'index'])->name('index');
            Route::get('/fields/{field}', [FormBuilderController::class, 'getField'])->name('fields.show');
            Route::get('/fields/{field}/view', [FormBuilderController::class, 'show'])->name('fields.view');
            Route::post('/fields', [FormBuilderController::class, 'storeField'])->name('fields.store');
            Route::put('/fields/{field}', [FormBuilderController::class, 'updateField'])->name('fields.update');
            Route::delete('/fields/{field}', [FormBuilderController::class, 'destroyField'])->name('fields.destroy');
            Route::post('/fields/reorder', [FormBuilderController::class, 'reorderFields'])->name('fields.reorder');
            Route::put('/fields/{field}/column', [FormBuilderController::class, 'updateFieldColumn'])->name('fields.column');
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
