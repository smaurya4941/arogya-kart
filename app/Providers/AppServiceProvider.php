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
use App\Models\SaleReturn;
use App\Models\Customer;
use App\Models\Expense;
use App\Policies\ProductPolicy;
use App\Policies\ProductBatchPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\SalePolicy;
use App\Policies\SaleReturnPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ExpensePolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

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
        /*
        |--------------------------------------------------------------------------
        | Production Hardening
        |--------------------------------------------------------------------------
        | Only takes effect when APP_ENV=production, so local dev is unaffected.
        */
        if ($this->app->isProduction()) {
            // Generate every URL/asset link over HTTPS and mark cookies secure,
            // even when PHP sits behind a proxy that terminates TLS.
            URL::forceScheme('https');

            // Refuse migrate:fresh / db:wipe / migrate:refresh against live
            // pharmacy data — an accidental run would erase real sales records.
            DB::prohibitDestructiveCommands();
        }

        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(ProductBatch::class, ProductBatchPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(PurchaseInvoice::class, PurchasePolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(SaleReturn::class, SaleReturnPolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);

        /*
        |--------------------------------------------------------------------------
        | Blade Helpers
        |--------------------------------------------------------------------------
        */

        /**
         * @currency(1234.5)  → ₹1,234.50
         *
         * Formats a monetary value using the pharmacy currency symbol and
         * two decimal places. Safe to use directly in Blade templates.
         */
        Blade::directive('currency', function (string $expression): string {
            return "<?php echo config('pharmacy.currency_symbol') . number_format({$expression}, 2); ?>";
        });

        /**
         * @pharmacyDate($dateValue)  → 07 Jul 2026
         *
         * Formats a Carbon/date value using the configured pharmacy date format.
         */
        Blade::directive('pharmacyDate', function (string $expression): string {
            return "<?php echo \Carbon\Carbon::parse({$expression})->format(config('pharmacy.date_format', 'd M Y')); ?>";
        });
    }
}
