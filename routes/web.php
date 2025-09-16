<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\KitchenQuoteController;
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
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

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
    Route::resource('quotes', QuoteController::class)->middleware('module:quotes');
    Route::get('/quote/{type?}', [QuoteController::class, 'quote_form_show'])->name('quote.form.show');
    Route::get('/quotes/{quote}/download', [QuoteController::class, 'download'])->name('quotes.download');

    // Files
    Route::resource('files', FileController::class)
        ->middleware('module:files');

    // kitchen quotes
    // Route::resource('kitchen-quotes', KitchenQuoteController::class)
    //     ->middleware('module:kitchen-quotes');
    Route::get('kitchen-quotes', [KitchenQuoteController::class, 'index'])->name('kitchen-quotes.index')->middleware('module:kitchen_quotes');
    Route::post('kitchen-quotes/store', [KitchenQuoteController::class, 'store'])->name('kitchen-quotes.store')->middleware('module:kitchen_quotes');
});

