<?php

use Illuminate\Support\Facades\Route;

// Public Routes - Home route with both names for compatibility
Route::get('/', function () {
    return view('public.home');
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

Route::post('/login', function () {
    $credentials = request()->only('email', 'password');
    
    if (auth()->attempt($credentials, request()->filled('remember'))) {
        request()->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.submit');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // User Management
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::patch('/users/{user}/toggle-status', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Profile
    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('profile');
    
    // System Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
    
    // Form Management
    Route::resource('forms', App\Http\Controllers\Admin\FormController::class);
    Route::post('/forms/{form}/generate-qr', [App\Http\Controllers\Admin\FormController::class, 'generateQrCode'])->name('forms.generate-qr');
    Route::post('/forms/{form}/toggle-status', [App\Http\Controllers\Admin\FormController::class, 'toggleStatus'])->name('forms.toggle-status');
    
    // Content Management
    Route::resource('content', App\Http\Controllers\Admin\ContentController::class);
    Route::post('/content/{page}/toggle-status', [App\Http\Controllers\Admin\ContentController::class, 'toggleStatus'])->name('content.toggle-status');
    Route::post('/content/{page}/toggle-featured', [App\Http\Controllers\Admin\ContentController::class, 'toggleFeatured'])->name('content.toggle-featured');
    
    // QR Code Management
    Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
        Route::post('/generate', [App\Http\Controllers\Admin\QrCodeController::class, 'generate'])->name('generate');
        Route::post('/bulk-generate', [App\Http\Controllers\Admin\QrCodeController::class, 'bulkGenerate'])->name('bulk-generate');
        Route::get('/download/{fileName}', [App\Http\Controllers\Admin\QrCodeController::class, 'download'])->name('download');
    });
    
    // Branch QR Test
    Route::get('/branch-qr-test', function () {
        return view('admin.branch-qr-test');
    })->name('branch-qr-test');
});
