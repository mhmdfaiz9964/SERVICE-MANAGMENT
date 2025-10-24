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

// Authentication routes
Auth::routes();

// Protected routes (require authentication)
Route::middleware(['auth:web'])->group(function () {
    // Admin routes prefix
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard routes
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
        Route::get('/', fn() => view('admin.dashboard'));

        // User management
        Route::resource('users', UserController::class);

        // Service management
        Route::resource('service-categories', ServiceCategoryController::class)->names('service.categories');
        Route::get('service/categories/toggle/{id}', [ServiceCategoryController::class, 'toggleStatus'])
            ->name('service.categories.toggle');
        
        Route::resource('services', ServiceController::class)->names('services');

        // Technician management
        Route::resource('technician', TechnicianController::class)->names('technician');
        Route::post('/technicians/{id}/toggle-status', [TechnicianController::class, 'toggleStatus'])
            ->name('technician.toggle-status');

        // Customer management
        Route::resource('customer', CustomerController::class)->names('customer');

        // App settings
        Route::get('app-settings', [AppSettingController::class, 'index'])->name('app_settings');
        Route::put('app-settings/update', [AppSettingController::class, 'update'])->name('app_settings.update');
    });
});