<?php

use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('public.home');
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

// Auth routes (placeholder - you can implement these later)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/logout', function () {
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
});
