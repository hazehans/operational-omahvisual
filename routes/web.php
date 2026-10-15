<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventOrderController;
use App\Http\Controllers\InitialCountController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceTicketController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SuperuserDatabaseController;
use Illuminate\Support\Facades\Route;

// Public Live Token Route for Crew Lapangan (No login required)
Route::get('/spk/live/{token}', [SpkController::class, 'liveTokenView'])->name('spk.live');

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

    // User Profile & Password
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Master Products & Categories
    Route::resource('products', ProductController::class);

    // Individual Serialized Assets
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    Route::put('/assets/{asset}/status', [AssetController::class, 'updateStatus'])->name('assets.update-status');

    // Locations & Lemari Storage
    Route::resource('locations', LocationController::class);

    // Stock Movements
    Route::get('/movements', [StockMovementController::class, 'index'])->name('movements.index');
    Route::get('/movements/create', [StockMovementController::class, 'create'])->name('movements.create');
    Route::post('/movements', [StockMovementController::class, 'store'])->name('movements.store');

    // Initial Stock Counts (Sahkan Stok Awal)
    Route::get('/initial-counts', [InitialCountController::class, 'index'])->name('initial-counts.index');
    Route::post('/initial-counts/{count}/verify', [InitialCountController::class, 'verify'])->name('initial-counts.verify');
    Route::post('/initial-counts/{count}/reset-simulation', [InitialCountController::class, 'resetSimulation'])->name('initial-counts.reset-simulation');

    // Stock Adjustments & Owner Approval
    Route::get('/adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
    Route::get('/adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
    Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
    Route::post('/adjustments/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('adjustments.approve');
    Route::post('/adjustments/{adjustment}/reject', [StockAdjustmentController::class, 'reject'])->name('adjustments.reject');

    // Service & Workshop Maintenance
    Route::get('/service-tickets', [ServiceTicketController::class, 'index'])->name('service-tickets.index');
    Route::get('/service-tickets/create', [ServiceTicketController::class, 'create'])->name('service-tickets.create');
    Route::post('/service-tickets', [ServiceTicketController::class, 'store'])->name('service-tickets.store');
    Route::get('/service-tickets/{ticket}', [ServiceTicketController::class, 'show'])->name('service-tickets.show');
    Route::put('/service-tickets/{ticket}', [ServiceTicketController::class, 'update'])->name('service-tickets.update');
    Route::put('/service-tickets/{ticket}/status', [ServiceTicketController::class, 'updateStatus'])->name('service-tickets.update-status');
    Route::post('/service-tickets/{ticket}/spareparts', [ServiceTicketController::class, 'addSparepart'])->name('service-tickets.spareparts.store');
    Route::delete('/service-tickets/{ticket}/spareparts/{sparepart}', [ServiceTicketController::class, 'removeSparepart'])->name('service-tickets.spareparts.destroy');

    // Audit Trail
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Event Orders & LED Screen Calculator (Phase 4)
    Route::post('/orders/calculate', [EventOrderController::class, 'calculate'])->name('orders.calculate');
    Route::put('/orders/{order}/stage-layout', [EventOrderController::class, 'updateStageLayout'])->name('orders.update-stage-layout');
    Route::resource('orders', EventOrderController::class);

    // SPK & Equipment Gate Checklist (Phase 4)
    Route::get('/spk/{assignment}', [SpkController::class, 'show'])->name('spk.show');
    Route::get('/spk/{assignment}/print', [SpkController::class, 'print'])->name('spk.print');
    Route::post('/spk/{assignment}/verify-departure', [SpkController::class, 'verifyDeparture'])->name('spk.verify-departure');
    Route::post('/spk/{assignment}/verify-return', [SpkController::class, 'verifyReturn'])->name('spk.verify-return');
    Route::post('/spk/items/{item}/toggle', [SpkController::class, 'toggleItem'])->name('spk.toggle-item');

    // Superuser Raw Database Manager (Django-like Admin CRUD)
    Route::prefix('superuser/database')->name('superuser.database.')->group(function () {
        Route::get('/', [SuperuserDatabaseController::class, 'index'])->name('index');
        Route::get('/{table}', [SuperuserDatabaseController::class, 'showTable'])->name('show');
        Route::get('/{table}/create', [SuperuserDatabaseController::class, 'createRecord'])->name('create');
        Route::post('/{table}', [SuperuserDatabaseController::class, 'storeRecord'])->name('store');
        Route::get('/{table}/{id}/edit', [SuperuserDatabaseController::class, 'editRecord'])->name('edit');
        Route::put('/{table}/{id}', [SuperuserDatabaseController::class, 'updateRecord'])->name('update');
        Route::delete('/{table}/{id}', [SuperuserDatabaseController::class, 'deleteRecord'])->name('destroy');
    });
});
