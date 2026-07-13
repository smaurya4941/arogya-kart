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
    $role = auth()->user()->role;
    $role = $role instanceof \App\Enums\UserRole ? $role : ($role !== null ? \App\Enums\UserRole::tryFrom($role) : null);
    
    return match($role) {
        \App\Enums\UserRole::SUPER_ADMIN => redirect('/superadmin/dashboard'),
        \App\Enums\UserRole::ADMIN => redirect('/admin/dashboard'),
        \App\Enums\UserRole::STAFF => redirect('/staff/dashboard'),
        \App\Enums\UserRole::CLIENT => redirect('/client/dashboard'),
        default => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Owner-only tenant routes
|--------------------------------------------------------------------------
| Business administration: dashboards, configuration, procurement, finance,
| team and billing. Restricted to the pharmacy owner (enum role: admin).
*/
Route::middleware(['auth', 'role:admin', 'subscription'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        // Pharmacy Setup & Configuration
        Route::get('/profile', [\App\Http\Controllers\PharmacyProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\PharmacyProfileController::class, 'update'])->name('profile.update');

        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        // Customers (Phase B)
        Route::resource('customers', CustomerController::class);

        // Team / staff management (Phase 2) — seat-capped by plan
        Route::patch('team/{user}/toggle-status', [\App\Http\Controllers\Admin\TeamController::class, 'toggleStatus'])->name('team.toggle-status');
        Route::resource('team', \App\Http\Controllers\Admin\TeamController::class)->except(['show'])->parameters(['team' => 'user']);

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
            // Secure download route for queued PDF exports (served from storage/app/exports/)
            Route::get('/download/{filename}', [\App\Http\Controllers\ReportsController::class, 'download'])->name('download');
        });

        // Expenses (Phase C)
        Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);

        // Notifications (Phase 12)
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

        // SaaS Subscriptions & Billing (Phase 14 & 15)
        Route::get('/subscription', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscription/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
        Route::post('/subscription/callback', [\App\Http\Controllers\SubscriptionController::class, 'callback'])->name('subscription.callback');

        // Suppliers (Phase A)
        Route::resource('suppliers', SupplierController::class);

        // Purchases / Goods-in (Phase A)
        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    });

/*
|--------------------------------------------------------------------------
| Shared operational routes (owner + staff)
|--------------------------------------------------------------------------
| POS, sales and inventory. Reachable by the owner and any staff member; the
| controllers' policies (Product/Sale/ProductBatch) enforce the finer per-role
| rules — e.g. a Cashier can ring up a sale but not edit the catalogue. Route
| names stay under `admin.` so existing views resolve unchanged.
*/
Route::middleware(['auth', 'role:admin,staff', 'subscription'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // POS / Sales & Billing
        Route::middleware([\App\Http\Middleware\CheckDrugLicenseExpiry::class])->group(function () {
            Route::get('/pos', [SaleController::class, 'create'])->name('pos.index');
            Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
        });
        
        Route::get('sales/search', [SaleController::class, 'search'])->name('sales.search');
        Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
        Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

        // Returns / Refunds (Phase 4) — gated by the 'return sale' permission
        Route::get('returns', [\App\Http\Controllers\Admin\SaleReturnController::class, 'index'])->name('returns.index');
        Route::get('sales/{sale}/return', [\App\Http\Controllers\Admin\SaleReturnController::class, 'create'])->name('returns.create');
        Route::post('sales/{sale}/return', [\App\Http\Controllers\Admin\SaleReturnController::class, 'store'])->name('returns.store');
        Route::get('returns/{return}', [\App\Http\Controllers\Admin\SaleReturnController::class, 'show'])->name('returns.show');

        // Inventory / catalogue
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

        // Customer Payments (Phase 2)
        Route::post('customers/{customer}/payments', [\App\Http\Controllers\Admin\CustomerPaymentController::class, 'store'])
            ->name('customers.payments.store');

        // CSV Importer (Phase 3)
        Route::get('imports', [\App\Http\Controllers\Admin\DataImportController::class, 'create'])->name('imports.create');
        Route::post('imports', [\App\Http\Controllers\Admin\DataImportController::class, 'store'])->name('imports.store');
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


// Super Admin routes — the platform owner's control panel (all tenants).
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

        // Each section below is gated by a granular Super-Admin capability
        // (admin.can:<key>). A full super admin holds every capability; a
        // restricted "support" super admin only the ones granted to their account.

        // Tenant management — full lifecycle (onboard / edit / suspend / archive / restore)
        Route::middleware('admin.can:pharmacies')->group(function () {
            Route::get('/pharmacies', [\App\Http\Controllers\SuperAdmin\PharmacyController::class, 'index'])->name('pharmacies.index');
            Route::get('/pharmacies/create', [\App\Http\Controllers\SuperAdmin\PharmacyController::class, 'create'])->name('pharmacies.create');
            Route::post('/pharmacies', [\App\Http\Controllers\SuperAdmin\PharmacyController::class, 'store'])->name('pharmacies.store');
            Route::get('/pharmacies/{pharmacy}', [\App\Http\Controllers\SuperAdmin\PharmacyController::class, 'show'])->name('pharmacies.show')->withTrashed();
            Route::get('/pharmacies/{pharmacy}/edit', [\App\Http\Controllers\SuperAdmin\PharmacyController::class, 'edit'])->name('pharmacies.edit');
            Route::put('/pharmacies/{pharmacy}', [\App\Http\Controllers\SuperAdmin\PharmacyController::class, 'update'])->name('pharmacies.update');
            Route::patch('/pharmacies/{pharmacy}/toggle-status', [\App\Http\Controllers\SuperAdmin\PharmacyController::class, 'toggleStatus'])->name('pharmacies.toggle-status');
            Route::delete('/pharmacies/{pharmacy}', [\App\Http\Controllers\SuperAdmin\PharmacyController::class, 'destroy'])->name('pharmacies.destroy');
            Route::patch('/pharmacies/{pharmacy}/restore', [\App\Http\Controllers\SuperAdmin\PharmacyController::class, 'restore'])->name('pharmacies.restore');
        });

        // Impersonation — a distinct capability so support can be granted it alone.
        Route::middleware('admin.can:impersonate')->group(function () {
            Route::post('/pharmacies/{pharmacy}/impersonate', [\App\Http\Controllers\SuperAdmin\ImpersonationController::class, 'start'])->name('pharmacies.impersonate');
        });

        // Global user management — every account across all tenants
        Route::middleware('admin.can:users')->group(function () {
            Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\SuperAdmin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::resource('users', \App\Http\Controllers\SuperAdmin\UserController::class)->except(['show']);
        });

        // Billing — plans, subscriptions, invoices and coupons.
        Route::middleware('admin.can:billing')->group(function () {
            // Plan catalogue
            Route::patch('/plans/{plan}/toggle-status', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'toggleStatus'])->name('plans.toggle-status');
            Route::resource('plans', \App\Http\Controllers\SuperAdmin\PlanController::class)->except(['show']);

            // Subscriptions — full lifecycle management
            Route::post('/subscriptions/{subscription}/extend-trial', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'extendTrial'])->name('subscriptions.extend-trial');
            Route::post('/subscriptions/{subscription}/cancel', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
            Route::resource('subscriptions', \App\Http\Controllers\SuperAdmin\SubscriptionController::class)->except(['show']);

            // Invoices / billing desk
            Route::get('/invoices/export', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'export'])->name('invoices.export');
            Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'pdf'])->name('invoices.pdf');
            Route::patch('/invoices/{invoice}/mark-paid', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
            Route::patch('/invoices/{invoice}/void', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'void'])->name('invoices.void');
            Route::patch('/invoices/{invoice}/refund', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'refund'])->name('invoices.refund');
            Route::get('/invoices', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'index'])->name('invoices.index');
            Route::post('/invoices', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'store'])->name('invoices.store');
        });

        // Cross-tenant operational visibility (read-only browsers + aggregate overview)
        Route::middleware('admin.can:operations')->group(function () {
            Route::get('/operations', [\App\Http\Controllers\SuperAdmin\OperationsController::class, 'index'])->name('operations.index');
            Route::get('/operations/products', [\App\Http\Controllers\SuperAdmin\OperationsController::class, 'products'])->name('operations.products');
            Route::get('/operations/sales', [\App\Http\Controllers\SuperAdmin\OperationsController::class, 'sales'])->name('operations.sales');
            Route::get('/operations/purchases', [\App\Http\Controllers\SuperAdmin\OperationsController::class, 'purchases'])->name('operations.purchases');
            Route::get('/operations/customers', [\App\Http\Controllers\SuperAdmin\OperationsController::class, 'customers'])->name('operations.customers');
            Route::get('/operations/expenses', [\App\Http\Controllers\SuperAdmin\OperationsController::class, 'expenses'])->name('operations.expenses');
        });

        // Activity / audit trail (impersonation & platform events)
        Route::middleware('admin.can:audit')->group(function () {
            Route::get('/audit/export', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'export'])->name('audit.export');
            Route::get('/audit', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'index'])->name('audit.index');
        });

        // Announcements / broadcast messages
        Route::middleware('admin.can:announcements')->group(function () {
            Route::patch('/announcements/{announcement}/toggle', [\App\Http\Controllers\SuperAdmin\AnnouncementController::class, 'toggle'])->name('announcements.toggle');
            Route::resource('announcements', \App\Http\Controllers\SuperAdmin\AnnouncementController::class)->except(['show']);
        });

        // Coupons & discounts — part of billing.
        Route::middleware('admin.can:billing')->group(function () {
            Route::patch('/coupons/{coupon}/toggle', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'toggle'])->name('coupons.toggle');
            Route::resource('coupons', \App\Http\Controllers\SuperAdmin\CouponController::class)->except(['show']);
        });

        // Platform settings
        Route::middleware('admin.can:settings')->group(function () {
            Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'update'])->name('settings.update');
        });

        // System health & operations
        Route::middleware('admin.can:system')->group(function () {
            Route::get('/system', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'index'])->name('system.index');
            Route::post('/system/failed-jobs/retry', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'retryFailed'])->name('system.failed.retry');
            Route::post('/system/failed-jobs/flush', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'flushFailed'])->name('system.failed.flush');
        });
    });



// Razorpay server-to-server webhook (no auth / CSRF-exempt — verified by signature)
Route::post('/webhooks/razorpay', [\App\Http\Controllers\SubscriptionController::class, 'webhook'])
    ->name('webhooks.razorpay');


//Auth routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Leave impersonation — must live outside the super-admin group because while
    // impersonating the acting user is a tenant admin, not a Super Admin.
    Route::post('/impersonate/leave', [\App\Http\Controllers\SuperAdmin\ImpersonationController::class, 'stop'])->name('impersonate.leave');
});

require __DIR__.'/auth.php';
