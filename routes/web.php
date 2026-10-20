<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\CrewPayrollController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTrackingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventOrderController;
use App\Http\Controllers\InitialCountController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceTicketController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SuperuserDatabaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

// Public Customer Self-Service Order Tracking Portal (No login required)
Route::get('/track/{token}', [CustomerTrackingController::class, 'show'])->name('orders.track');

// Public Live Token Route for Crew Lapangan (No login required)
Route::get('/spk/live/{token}', [SpkController::class, 'liveTokenView'])->name('spk.live');
Route::post('/spk/live/{token}/qc', [SpkController::class, 'submitVenueQc'])->name('spk.submit-qc');

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
    Route::middleware('can:view inventory')->group(function () {
        Route::resource('products', ProductController::class);

        // Individual Serialized Assets
        Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
        Route::put('/assets/{asset}/status', [AssetController::class, 'updateStatus'])->name('assets.update-status');

        // Locations & Lemari Storage
        Route::resource('locations', LocationController::class);

        // Stock Movements
        Route::get('/movements', [StockMovementController::class, 'index'])->name('movements.index');
    });

    Route::middleware('can:record movement')->group(function () {
        Route::get('/movements/create', [StockMovementController::class, 'create'])->name('movements.create');
        Route::post('/movements', [StockMovementController::class, 'store'])->name('movements.store');
    });

    // Initial Stock Counts (Sahkan Stok Awal)
    Route::middleware('can:conduct stock count')->group(function () {
        Route::get('/initial-counts', [InitialCountController::class, 'index'])->name('initial-counts.index');
        Route::post('/initial-counts/{count}/reset-simulation', [InitialCountController::class, 'resetSimulation'])->name('initial-counts.reset-simulation');
        Route::post('/initial-counts/{count}/clear-items', [InitialCountController::class, 'clearItems'])->name('initial-counts.clear-items');
        Route::post('/initial-counts/{count}/add-item', [InitialCountController::class, 'addItem'])->name('initial-counts.add-item');
        Route::delete('/initial-counts/{count}/items/{item}', [InitialCountController::class, 'deleteItem'])->name('initial-counts.delete-item');
    });
    Route::post('/initial-counts/{count}/verify', [InitialCountController::class, 'verify'])
        ->name('initial-counts.verify')
        ->middleware('can:verify stock count');

    // Stock Adjustments & Owner Approval
    Route::middleware('can:request stock adjustment')->group(function () {
        Route::get('/adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
        Route::get('/adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
        Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
    });
    Route::middleware('can:approve stock adjustment')->group(function () {
        Route::post('/adjustments/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('adjustments.approve');
        Route::post('/adjustments/{adjustment}/reject', [StockAdjustmentController::class, 'reject'])->name('adjustments.reject');
    });

    // Service & Workshop Maintenance
    Route::middleware('can:create service ticket')->group(function () {
        Route::get('/service-tickets/create', [ServiceTicketController::class, 'create'])->name('service-tickets.create');
        Route::post('/service-tickets', [ServiceTicketController::class, 'store'])->name('service-tickets.store');
    });
    Route::middleware('can:view service tickets')->group(function () {
        Route::get('/service-tickets', [ServiceTicketController::class, 'index'])->name('service-tickets.index');
        Route::get('/service-tickets/{ticket}', [ServiceTicketController::class, 'show'])->name('service-tickets.show');
    });
    Route::middleware('permission:execute service ticket|approve service ticket')->group(function () {
        Route::put('/service-tickets/{ticket}', [ServiceTicketController::class, 'update'])->name('service-tickets.update');
        Route::put('/service-tickets/{ticket}/status', [ServiceTicketController::class, 'updateStatus'])->name('service-tickets.update-status');
    });
    Route::middleware('can:manage service spareparts')->group(function () {
        Route::post('/service-tickets/{ticket}/spareparts', [ServiceTicketController::class, 'addSparepart'])->name('service-tickets.spareparts.store');
        Route::delete('/service-tickets/{ticket}/spareparts/{sparepart}', [ServiceTicketController::class, 'removeSparepart'])->name('service-tickets.spareparts.destroy');
    });

    // Audit Trail
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('can:view audit logs');

    // Master Data Operasional (Customer, Kru, Armada)
    Route::middleware('can:view customers')->group(function () {
        Route::resource('customers', CustomerController::class);
    });
    Route::middleware('can:view crews')->group(function () {
        Route::resource('crews', CrewController::class);
    });
    Route::middleware('can:view vehicles')->group(function () {
        Route::resource('vehicles', VehicleController::class);
    });

    // Event Orders & LED Screen Calculator (Phase 4)
    Route::middleware('can:create orders')->group(function () {
        Route::get('/orders/create', [EventOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [EventOrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/calculate', [EventOrderController::class, 'calculate'])->name('orders.calculate');
        Route::put('/orders/{order}/stage-layout', [EventOrderController::class, 'updateStageLayout'])->name('orders.update-stage-layout');
    });
    Route::middleware('can:view orders')->group(function () {
        Route::get('/orders', [EventOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [EventOrderController::class, 'show'])->name('orders.show');
    });
    Route::middleware('can:edit orders')->group(function () {
        Route::get('/orders/{order}/edit', [EventOrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [EventOrderController::class, 'update'])->name('orders.update');
    });
    Route::middleware('can:delete orders')->group(function () {
        Route::delete('/orders/{order}', [EventOrderController::class, 'destroy'])->name('orders.destroy');
    });

    // SPK & Equipment Gate Checklist (Phase 4)
    Route::middleware('can:view spk')->group(function () {
        Route::get('/spk/{assignment}', [SpkController::class, 'show'])->name('spk.show');
    });
    Route::middleware('can:print spk')->group(function () {
        Route::get('/spk/{assignment}/print', [SpkController::class, 'print'])->name('spk.print');
    });
    Route::middleware('permission:manage spk crews|manage spk vehicles')->group(function () {
        Route::put('/spk/{assignment}/assignment', [SpkController::class, 'updateAssignment'])->name('spk.update-assignment');
    });
    Route::middleware('permission:approve spk qc|upload spk qc')->group(function () {
        Route::post('/spk/{assignment}/qc', [SpkController::class, 'updateQc'])->name('spk.update-qc');
    });
    Route::middleware('can:manage spk departure')->group(function () {
        Route::post('/spk/{assignment}/verify-departure', [SpkController::class, 'verifyDeparture'])->name('spk.verify-departure');
        Route::post('/spk/{assignment}/reset-departure', [SpkController::class, 'resetDeparture'])->name('spk.reset-departure');
    });
    Route::middleware('can:manage spk return')->group(function () {
        Route::post('/spk/{assignment}/verify-return', [SpkController::class, 'verifyReturn'])->name('spk.verify-return');
        Route::post('/spk/{assignment}/reset-return', [SpkController::class, 'resetReturn'])->name('spk.reset-return');
    });
    Route::middleware('permission:manage spk departure|manage spk return|view spk')->group(function () {
        Route::post('/spk/{assignment}/items', [SpkController::class, 'addChecklistItem'])->name('spk.items.store');
        Route::post('/spk/{assignment}/apply-template', [SpkController::class, 'applyTemplate'])->name('spk.apply-template');
        Route::put('/spk/items/{item}', [SpkController::class, 'updateChecklistItem'])->name('spk.items.update');
        Route::delete('/spk/items/{item}', [SpkController::class, 'deleteChecklistItem'])->name('spk.items.destroy');
        Route::post('/spk/items/{item}/toggle', [SpkController::class, 'toggleItem'])->name('spk.toggle-item');
        Route::post('/spk/{assignment}/items/bulk-toggle', [SpkController::class, 'bulkToggleItems'])->name('spk.items.bulk-toggle');
    });

    // SPK Multi-Vehicle, Crew Assignments, & Operational Costs
    Route::middleware('can:manage spk crews')->group(function () {
        Route::post('/spk/{assignment}/crews', [SpkController::class, 'addCrewAssignment'])->name('spk.crews.store');
        Route::put('/spk/crews/{assignment}', [SpkController::class, 'updateCrewAssignment'])->name('spk.crews.update');
        Route::delete('/spk/crews/{assignment}', [SpkController::class, 'deleteCrewAssignment'])->name('spk.crews.destroy');
    });
    Route::middleware('can:manage spk vehicles')->group(function () {
        Route::post('/spk/{assignment}/vehicles', [SpkController::class, 'addVehicleAssignment'])->name('spk.vehicles.store');
        Route::put('/spk/vehicles/{vehicle}', [SpkController::class, 'updateVehicleAssignment'])->name('spk.vehicles.update');
        Route::delete('/spk/vehicles/{vehicle}', [SpkController::class, 'deleteVehicleAssignment'])->name('spk.vehicles.destroy');
    });
    Route::middleware('can:manage operational costs')->group(function () {
        Route::put('/spk/{assignment}/costs', [SpkController::class, 'updateOperationalCosts'])->name('spk.costs.update');
    });

    // Crew Payroll & Freelance Payslip (Admin & Finance)
    Route::middleware('can:view payroll')->group(function () {
        Route::get('/crew-payroll', [CrewPayrollController::class, 'index'])->name('crew-payroll.index');
    });
    Route::middleware('can:mark payroll paid')->group(function () {
        Route::post('/crew-payroll/{assignment}/mark-paid', [CrewPayrollController::class, 'markPaid'])->name('crew-payroll.mark-paid');
        Route::post('/crew-payroll/batch-mark-paid', [CrewPayrollController::class, 'batchMarkPaid'])->name('crew-payroll.batch-mark-paid');
    });
    Route::middleware('can:print payroll slip')->group(function () {
        Route::get('/crew-payroll/{assignment}/slip', [CrewPayrollController::class, 'showSlip'])->name('crew-payroll.slip');
        Route::get('/crew-payroll/crew/{crew}/monthly-slip', [CrewPayrollController::class, 'crewMonthlySlip'])->name('crew-payroll.crew-monthly-slip');
    });

    // Invoices & Client Billing Management
    Route::middleware('can:print invoice')->group(function () {
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    });
    Route::middleware('can:print receipt')->group(function () {
        Route::get('/invoices/payments/{payment}/receipt', [InvoiceController::class, 'printReceipt'])->name('invoices.payments.receipt');
    });
    Route::middleware('can:record invoice payment')->group(function () {
        Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('invoices.payments.store');
    });
    Route::middleware('can:delete invoice payment')->group(function () {
        Route::delete('/invoices/payments/{payment}', [InvoiceController::class, 'deletePayment'])->name('invoices.payments.destroy');
    });
    Route::middleware('can:create invoice')->group(function () {
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    });
    Route::middleware('can:view invoices')->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    });
    Route::middleware('can:edit invoice')->group(function () {
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    });
    Route::middleware('can:delete invoice')->group(function () {
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    });

    // User & Role Management (Superuser & Owner)
    Route::middleware('can:manage users')->group(function () {
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', UserController::class);
    });

    // Superuser Raw Database Manager (Django-like Admin CRUD)
    Route::middleware('role:Superuser')->prefix('superuser/database')->name('superuser.database.')->group(function () {
        Route::get('/', [SuperuserDatabaseController::class, 'index'])->name('index');
        Route::get('/{table}', [SuperuserDatabaseController::class, 'showTable'])->name('show');
        Route::get('/{table}/create', [SuperuserDatabaseController::class, 'createRecord'])->name('create');
        Route::post('/{table}', [SuperuserDatabaseController::class, 'storeRecord'])->name('store');
        Route::get('/{table}/{id}/edit', [SuperuserDatabaseController::class, 'editRecord'])->name('edit');
        Route::put('/{table}/{id}', [SuperuserDatabaseController::class, 'updateRecord'])->name('update');
        Route::delete('/{table}/{id}', [SuperuserDatabaseController::class, 'deleteRecord'])->name('destroy');
    });
});
