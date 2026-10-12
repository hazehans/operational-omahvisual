<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InitialCountController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Products & Categories
    Route::resource('products', ProductController::class);

    // Individual Serialized Assets
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    Route::put('/assets/{asset}/status', [AssetController::class, 'updateStatus'])->name('assets.update-status');

    // Locations & Lemari Storage
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/locations/{location}', [LocationController::class, 'show'])->name('locations.show');

    // Stock Movements
    Route::get('/movements', [StockMovementController::class, 'index'])->name('movements.index');
    Route::get('/movements/create', [StockMovementController::class, 'create'])->name('movements.create');
    Route::post('/movements', [StockMovementController::class, 'store'])->name('movements.store');

    // Initial Stock Counts (Sahkan Stok Awal)
    Route::get('/initial-counts', [InitialCountController::class, 'index'])->name('initial-counts.index');
    Route::post('/initial-counts/{count}/verify', [InitialCountController::class, 'verify'])->name('initial-counts.verify');

    // Stock Adjustments & Owner Approval
    Route::get('/adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
    Route::get('/adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
    Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
    Route::post('/adjustments/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('adjustments.approve');
    Route::post('/adjustments/{adjustment}/reject', [StockAdjustmentController::class, 'reject'])->name('adjustments.reject');

    // Audit Trail
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
