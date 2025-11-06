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
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function () {
    request()->validate([
        'email' => 'required|email',
    ]);

    $status = Password::sendResetLink(
        request()->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // User Management
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Branch Management
    Route::resource('branches', BranchController::class);
    
    // QR Code Generation Routes (must be before resource route to avoid conflicts)
    Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
        Route::post('/generate', [QrCodeController::class, 'generate'])->name('generate');
        Route::post('/bulk-generate', [QrCodeController::class, 'bulkGenerate'])->name('bulk-generate');
        Route::get('/download/{fileName}', [QrCodeController::class, 'download'])->name('download');
    });
    
    // QR Code Management (CRUD)
    Route::resource('qr-codes', QrCodeManagementController::class);
    Route::post('/qr-codes/regenerate-all', [QrCodeManagementController::class, 'regenerateAll'])->name('qr-codes.regenerate-all');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // System Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
    // Audit Trail
    Route::get('/audit-trails', [AuditTrailController::class, 'index'])->name('audit-trails.index');
    Route::get('/audit-trails/{auditTrail}', [AuditTrailController::class, 'show'])->name('audit-trails.show');
    
    // Dynamic Forms Management (Custom Forms)
    Route::resource('forms', FormController::class);
    
    // Form Builder (Dynamic Form Management)
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
        Route::post('/fields', [FormBuilderController::class, 'storeField'])->name('fields.store');
        Route::put('/fields/{field}', [FormBuilderController::class, 'updateField'])->name('fields.update');
        Route::delete('/fields/{field}', [FormBuilderController::class, 'destroyField'])->name('fields.destroy');
        Route::post('/fields/reorder', [FormBuilderController::class, 'reorderFields'])->name('fields.reorder');
        Route::put('/fields/{field}/column', [FormBuilderController::class, 'updateFieldColumn'])->name('fields.column');
    });
    
    // Form Submissions
    Route::prefix('submissions')->name('submissions.')->group(function () {
        Route::get('/raf', [SubmissionController::class, 'raf'])->name('raf');
        Route::get('/dar', [SubmissionController::class, 'dar'])->name('dar');
        Route::get('/dcr', [SubmissionController::class, 'dcr'])->name('dcr');
        Route::get('/srf', [SubmissionController::class, 'srf'])->name('srf');
        Route::get('/show-raf/{id}', [SubmissionController::class, 'showRaf'])->name('show-raf');
        Route::get('/show-dar/{id}', [SubmissionController::class, 'showDar'])->name('show-dar');
        Route::get('/show-dcr/{id}', [SubmissionController::class, 'showDcr'])->name('show-dcr');
        Route::get('/show-srf/{id}', [SubmissionController::class, 'showSrf'])->name('show-srf');
        Route::put('/{type}/{id}/status', [SubmissionController::class, 'updateStatus'])->name('status.update');
    });
});
