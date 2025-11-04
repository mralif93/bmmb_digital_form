<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\FormManagementController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

// Public Routes - Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Form Routes
Route::prefix('forms')->name('public.forms.')->group(function () {
    Route::get('/raf', function () {
        return view('public.forms.raf');
    })->name('raf');
    
    Route::get('/dar', function () {
        return view('public.forms.dar');
    })->name('dar');
    
    Route::get('/dcr', function () {
        return view('public.forms.dcr');
    })->name('dcr');
    
    Route::get('/srf', function () {
        return view('public.forms.srf');
    })->name('srf');
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
    
    // Profile
    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('profile');
    
    // System Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
    
    // Forms Management (4 Forms: RAF, DAR, DCR, SRF)
    Route::prefix('forms/{type}')->name('forms.')->where(['type' => 'raf|dar|dcr|srf'])->group(function () {
        Route::get('/', [FormManagementController::class, 'index'])->name('index');
        Route::get('/create', [FormManagementController::class, 'create'])->name('create');
        Route::post('/', [FormManagementController::class, 'store'])->name('store');
        Route::get('/{id}', [FormManagementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [FormManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FormManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [FormManagementController::class, 'destroy'])->name('destroy');
    });
    
    // QR Code Management
    Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
        Route::post('/generate', [QrCodeController::class, 'generate'])->name('generate');
        Route::post('/bulk-generate', [QrCodeController::class, 'bulkGenerate'])->name('bulk-generate');
        Route::get('/download/{fileName}', [QrCodeController::class, 'download'])->name('download');
    });
    
    // Branch QR Test
    Route::get('/branch-qr-test', function () {
        return view('admin.branch-qr-test');
    })->name('branch-qr-test');
});
