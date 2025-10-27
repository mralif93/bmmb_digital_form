<?php

use Illuminate\Support\Facades\Route;

// Public Routes - Home page
Route::get('/', function () {
    return view('public.home');
})->name('home');

// Redirect public.home to home for compatibility
Route::get('/home', function () {
    return redirect()->route('home');
})->name('public.home');

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

// Auth Routes
Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::get('/register', [App\Http\Controllers\Auth\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\AuthController::class, 'sendResetLink'])->name('password.email');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Admin Routes (protected by auth and admin middleware)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // User Management
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::patch('/users/{user}/toggle-status', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Form Submissions
    Route::prefix('submissions')->name('submissions.')->group(function () {
        Route::get('/dar', [App\Http\Controllers\Admin\SubmissionController::class, 'dar'])->name('dar');
        Route::get('/dcr', [App\Http\Controllers\Admin\SubmissionController::class, 'dcr'])->name('dcr');
        Route::get('/raf', [App\Http\Controllers\Admin\SubmissionController::class, 'raf'])->name('raf');
        Route::get('/srf', [App\Http\Controllers\Admin\SubmissionController::class, 'srf'])->name('srf');
        
        // Show individual submissions
        Route::get('/dar/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'showDar'])->name('show-dar');
        Route::get('/dcr/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'showDcr'])->name('show-dcr');
        Route::get('/raf/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'showRaf'])->name('show-raf');
        Route::get('/srf/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'showSrf'])->name('show-srf');
        
        // Update status
        Route::patch('/{type}/{id}/status', [App\Http\Controllers\Admin\SubmissionController::class, 'updateStatus'])->name('update-status');
    });
    
    // Profile
    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('profile');
    
    // System Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
});
