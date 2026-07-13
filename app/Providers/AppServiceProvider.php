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

        $this->app->singleton(\App\Services\PlatformSettings::class);

        // Resolve RazorpayService from config (its constructor defaults to null,
        // so without this binding container-injected instances are unconfigured).
        // Registered *after* PlatformSettings so the config bridge in boot() can
        // overlay DB-managed keys before the service is first resolved.
        $this->app->singleton(\App\Services\RazorpayService::class, fn () => \App\Services\RazorpayService::make());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Platform settings → runtime config bridge
        |--------------------------------------------------------------------------
        | Overlay DB-managed platform settings onto Laravel config so the payment
        | gateway, GST rate and mail identity can be changed from the Super Admin
        | settings page without redeploying. Degrades silently before the table
        | exists (first migration) so console commands never break.
        */
        try {
            $settings = $this->app->make(\App\Services\PlatformSettings::class);

            foreach ([
                'razorpay_key'            => 'services.razorpay.key',
                'razorpay_secret'         => 'services.razorpay.secret',
                'razorpay_webhook_secret' => 'services.razorpay.webhook_secret',
                'mail_from_address'       => 'mail.from.address',
                'mail_from_name'          => 'mail.from.name',
            ] as $settingKey => $configKey) {
                if (($value = $settings->get($settingKey)) !== null) {
                    config([$configKey => $value]);
                }
            }

            if (($gst = $settings->get('gst_percent')) !== null) {
                config(['saas.gst_percent' => (float) $gst]);
            }
        } catch (\Throwable $e) {
            // Never let a settings-store hiccup take down the whole app boot.
            \Illuminate\Support\Facades\Log::warning('Platform settings config bridge skipped: ' . $e->getMessage());
        }

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
