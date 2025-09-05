<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;

// Frontend / Auth
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/customers/check-email', [CustomerController::class, 'checkEmail'])->name('customers.check-email');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('customers', CustomerController::class);
    Route::resource('projects', ProjectController::class);
});
