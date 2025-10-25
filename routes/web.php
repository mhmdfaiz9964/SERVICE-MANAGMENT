<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TechnicianController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AppSettingController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Auth::routes(); // /login, /register, /logout, /password/reset, etc.

/*
|--------------------------------------------------------------------------
| Protected Routes (require authentication)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('/admin');
    });
    /*
    |--------------------------------------------------------------------------
    | Admin Routes (prefix /admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard
            Route::get('/', [HomeController::class, 'index'])->name('dashboard');
            Route::get('/dashboard', [HomeController::class, 'index']);

            // User management
            Route::resource('users', UserController::class);

            // Service Categories
            Route::resource('service-categories', ServiceCategoryController::class)->names('service.categories');
            Route::get('service/categories/toggle/{id}', [ServiceCategoryController::class, 'toggleStatus'])->name('service.categories.toggle');

            // Services
            Route::resource('services', ServiceController::class)->names('services');

            // Technicians
            Route::resource('technicians', TechnicianController::class)->names('technician');
            Route::post('/technicians/{id}/toggle-status', [TechnicianController::class, 'toggleStatus'])->name('technician.toggle-status');

            // Customers
            Route::resource('customers', CustomerController::class)->names('customer');

            // App Settings
            Route::get('app-settings', [AppSettingController::class, 'index'])->name('app_settings');
            Route::put('app-settings/update', [AppSettingController::class, 'update'])->name('app_settings.update');
        });
});
