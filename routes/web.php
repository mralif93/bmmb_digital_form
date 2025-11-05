<?php

use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ProfileController;
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
    Route::get('/raf/{branch?}', function ($branch = null) {
        if ($branch) {
            $branchModel = \App\Models\Branch::where('ti_agent_code', $branch)->first();
            if ($branchModel) {
                session(['submission_branch_id' => $branchModel->id]);
            }
        }
        return view('public.forms.raf');
    })->name('raf');
    
    Route::get('/dar/{branch?}', function ($branch = null) {
        if ($branch) {
            $branchModel = \App\Models\Branch::where('ti_agent_code', $branch)->first();
            if ($branchModel) {
                session(['submission_branch_id' => $branchModel->id]);
            }
        }
        return view('public.forms.dar');
    })->name('dar');
    
    Route::get('/dcr/{branch?}', function ($branch = null) {
        if ($branch) {
            $branchModel = \App\Models\Branch::where('ti_agent_code', $branch)->first();
            if ($branchModel) {
                session(['submission_branch_id' => $branchModel->id]);
            }
        }
        return view('public.forms.dcr');
    })->name('dcr');
    
    Route::get('/srf/{branch?}', function ($branch = null) {
        if ($branch) {
            $branchModel = \App\Models\Branch::where('ti_agent_code', $branch)->first();
            if ($branchModel) {
                session(['submission_branch_id' => $branchModel->id]);
            }
        }
        return view('public.forms.srf');
    })->name('srf');
    
    // Public Form Submission Routes
    Route::post('/{type}/submit', [\App\Http\Controllers\Public\FormSubmissionController::class, 'store'])
        ->where(['type' => 'raf|dar|dcr|srf'])
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
});
