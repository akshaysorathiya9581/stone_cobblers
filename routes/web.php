<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\KitchenQuoteController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

// Frontend / Auth
// Root: show login to guests, redirect logged-in users to admin dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard'); // redirects to /admin/dashboard
    }
    return app(AuthController::class)->index();
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return 'All caches cleared!';
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Dashboard (module: dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('module:dashboard');

    // Customers
    Route::post('/customers/check-email', [CustomerController::class, 'checkEmail'])
        ->name('customers.check-email')
        ->middleware('module:customers');

    Route::resource('customers', CustomerController::class)->middleware('module:customers');
    Route::post('/customers/{id}/contact', [CustomerController::class, 'updateLastContact'])->name('customers.updateLastContact');


    // Projects
    Route::resource('projects', ProjectController::class)->middleware('module:projects');
    // Return projects for a given customer (used by AJAX)
    Route::get('/customers/{customer}/projects', [ProjectController::class, 'byCustomer'])->name('customers.projects');

    // Quotes
    Route::resource('quotes', QuoteController::class)->middleware('module:quotes');
    Route::get('/quote/{type?}', [QuoteController::class, 'quote_form_show'])->name('quote.form.show');
    Route::get('/quotes/{quote}/download', [QuoteController::class, 'download'])->name('quotes.download');
    Route::post('quotes/{quote}/send',      [QuoteController::class, 'send'])->name('quotes.send');
    Route::post('quotes/{quote}/approve',   [QuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('quotes/{quote}/reject',    [QuoteController::class, 'reject'])->name('quotes.reject');

    // Files
    Route::resource('files', FileController::class)->middleware('module:files');
    Route::get('files/{file}/image', [FileController::class, 'image'])->name('files.image')->middleware('module:files');
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download')->middleware('module:files');
    // Route::delete('files/{file}', [FileController::class, 'destroy'])->name('files.destroy');

    // kitchen quotes
    // Route::resource('kitchen-quotes', KitchenQuoteController::class)
    //     ->middleware('module:kitchen-quotes');
    Route::get('kitchen-quotes', [KitchenQuoteController::class, 'index'])->name('kitchen-quotes.index')->middleware('module:kitchen_quotes');
    Route::post('kitchen-quotes/store', [KitchenQuoteController::class, 'store'])->name('kitchen-quotes.store')->middleware('module:kitchen_quotes');
    
    // show single (for edit prefill), update and delete
    Route::get('kitchen-quotes/{quote}', [KitchenQuoteController::class, 'show'])->name('admin.kitchen-quotes.show');
    Route::match(['put','patch'],'kitchen-quotes/{quote}', [KitchenQuoteController::class, 'update'])->name('admin.kitchen-quotes.update');
    Route::delete('kitchen-quotes/{quote}', [KitchenQuoteController::class, 'destroy'])->name('admin.kitchen-quotes.destroy');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('module:settings');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('module:settings');
    Route::get('settings/{key}', [SettingController::class, 'show'])->name('settings.show')->middleware('module:settings');
    Route::post('settings/create', [SettingController::class, 'store'])->name('settings.store')->middleware('module:settings');
    Route::delete('settings/{key}', [SettingController::class, 'destroy'])->name('settings.destroy')->middleware('module:settings');
});

