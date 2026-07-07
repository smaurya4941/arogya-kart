<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductBatchController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\CustomerController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Admin routes
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        // Pharmacy Setup & Configuration
        Route::get('/profile', [\App\Http\Controllers\PharmacyProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\PharmacyProfileController::class, 'update'])->name('profile.update');
        
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        // POS / Sales & Billing (Phase B)
        Route::get('/pos', [SaleController::class, 'create'])->name('pos.index');
        Route::get('sales/search', [SaleController::class, 'search'])->name('sales.search');
        Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
        Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

        // Customers (Phase B)
        Route::resource('customers', CustomerController::class);

        // Reports (Phase C)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/sales', [\App\Http\Controllers\ReportsController::class, 'sales'])->name('sales');
            Route::get('/sales/pdf', [\App\Http\Controllers\ReportsController::class, 'salesPdf'])->name('sales.pdf');
            Route::get('/purchases', [\App\Http\Controllers\ReportsController::class, 'purchases'])->name('purchases');
            Route::get('/purchases/pdf', [\App\Http\Controllers\ReportsController::class, 'purchasesPdf'])->name('purchases.pdf');
            Route::get('/profit', [\App\Http\Controllers\ReportsController::class, 'profit'])->name('profit');
            Route::get('/gst', [\App\Http\Controllers\ReportsController::class, 'gst'])->name('gst');
            Route::get('/gst/pdf', [\App\Http\Controllers\ReportsController::class, 'gstPdf'])->name('gst.pdf');
            Route::get('/inventory', [\App\Http\Controllers\ReportsController::class, 'inventory'])->name('inventory');
        });

        // Expenses (Phase C)
        Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);

        // Notifications (Phase 12)
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

        // SaaS Subscriptions & Billing (Phase 14 & 15)
        Route::get('/subscription', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscription/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');


        // Suppliers (Phase A)
        Route::resource('suppliers', SupplierController::class);

        // Purchases / Goods-in (Phase A)
        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

        // Existing
        Route::resource('products', ProductController::class);

        Route::get('products/{product}/batches/create', [ProductBatchController::class, 'create'])
            ->name('products.batches.create');
        Route::post('products/{product}/batches', [ProductBatchController::class, 'store'])
            ->name('products.batches.store');

        Route::get('batches/{batch}/edit', [ProductBatchController::class, 'edit'])
            ->name('batches.edit');
        Route::put('batches/{batch}', [ProductBatchController::class, 'update'])
            ->name('batches.update');
        Route::delete('batches/{batch}', [ProductBatchController::class, 'destroy'])
            ->name('batches.destroy');

        Route::post('products/{product}/issue-stock', [ProductController::class, 'issueStock'])
            ->name('products.issue-stock');
    });



// Staff routes
Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [StaffController::class, 'index'])->name('dashboard');
});

//Client routes
Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [ClientController::class, 'index'])->name('dashboard');
    });



//Auth routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
