<?php

namespace App\Providers;

use App\Repositories\Interfaces\BatchRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Eloquent\EloquentBatchRepository;
use App\Repositories\Eloquent\EloquentProductRepository;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Supplier;
use App\Models\PurchaseInvoice;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Expense;
use App\Policies\ProductPolicy;
use App\Policies\ProductBatchPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\SalePolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ExpensePolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(BatchRepositoryInterface::class, EloquentBatchRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(ProductBatch::class, ProductBatchPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(PurchaseInvoice::class, PurchasePolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
    }
}
