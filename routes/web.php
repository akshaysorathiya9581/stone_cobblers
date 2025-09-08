<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Artisan;

// Frontend / Auth
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return 'All caches cleared!';
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard (module: dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('module:dashboard');

    // Customers
    Route::post('/customers/check-email', [CustomerController::class, 'checkEmail'])
        ->name('customers.check-email')
        ->middleware('module:customers');

    Route::resource('customers', CustomerController::class)
        ->middleware('module:customers');

    // Projects
    Route::resource('projects', ProjectController::class)
        ->middleware('module:projects');

    // Quotes
    Route::resource('quotes', QuoteController::class)
        ->middleware('module:quotes');

    // Files
    Route::resource('files', FileController::class)
        ->middleware('module:files');
});

