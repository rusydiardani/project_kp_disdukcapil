<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Public Tracking Routes
Route::get('/cek-layanan', [App\Http\Controllers\PublicTrackingController::class, 'index'])->name('tracking.index');
Route::post('/cek-layanan', [App\Http\Controllers\PublicTrackingController::class, 'search'])
    // ->middleware('throttle:10,1') // Rate limit 10 hits per minute (optional, basic setup first)
    ->name('tracking.search');

Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Services CRUD
    Route::resource('services', ServiceRequestController::class);

    // Admin Only Routes
    Route::middleware(['can:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('service-types', ServiceTypeController::class);
    });
});

// Fallback redirect for /home
Route::get('/home', function () {
    return redirect()->route('dashboard');
});


