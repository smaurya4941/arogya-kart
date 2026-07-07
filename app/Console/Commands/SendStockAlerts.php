<?php

namespace App\Console\Commands;

use App\Models\Pharmacy;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\User;
use App\Notifications\ExpiryAlertNotification;
use App\Notifications\LowStockNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Scheduled Artisan command: pharmacy:stock-alerts
 *
 * Runs daily (configured in bootstrap/app.php withSchedule at 08:00).
 * For every pharmacy, finds:
 *   1. Products whose total stock is at or below min_stock_alert.
 *   2. Batches that expire within the configured alert window.
 *
 * Notifications are dispatched to the database channel (queued via ShouldQueue)
 * so the command finishes quickly regardless of how many pharmacies exist.
 */
class SendStockAlerts extends Command
{
    protected $signature = 'pharmacy:stock-alerts
                            {--pharmacy= : Only run for a specific pharmacy ID (useful for testing)}
                            {--dry-run   : Report what would be sent without persisting any notifications}';

    protected $description = 'Send low-stock and expiry alerts to pharmacy admin users.';

    public function handle(): int
    {
        $expiryWindow = config('pharmacy.expiry_alert_days', 30);
        $dryRun       = $this->option('dry-run');
        $pharmacyId   = $this->option('pharmacy');

        $pharmacies = Pharmacy::query()
            ->when($pharmacyId, fn ($q) => $q->where('id', $pharmacyId))
            ->get();

        if ($pharmacies->isEmpty()) {
            $this->warn('No pharmacies found.');
            return self::SUCCESS;
        }

        $totalLowStock = 0;
        $totalExpiring = 0;

        foreach ($pharmacies as $pharmacy) {
            // Resolve the admin user for this pharmacy (first admin found)
            $admin = User::where('pharmacy_id', $pharmacy->id)
                ->whereHas('roles', fn ($q) => $q->where('name', 'admin'))
                ->orWhere(function ($q) use ($pharmacy) {
                    $q->where('pharmacy_id', $pharmacy->id)
                      ->where('role', 'admin');
                })
                ->first();

            if (!$admin) {
                $this->warn("[Pharmacy #{$pharmacy->id}] No admin user found — skipping.");
                continue;
            }

            // ── 1. Low-stock products ─────────────────────────────────────
            $products = Product::where('pharmacy_id', $pharmacy->id)
                ->with('batches')
                ->get()
                ->filter(function (Product $product): bool {
                    return $product->total_stock <= ($product->min_stock_alert ?? config('pharmacy.low_stock_default', 10));
                });

            foreach ($products as $product) {
                $stock = $product->total_stock;
                $this->line("  [LOW STOCK] {$product->name}: {$stock} units");
                $totalLowStock++;

                if (!$dryRun) {
                    $admin->notify(new LowStockNotification($product, $stock));
                }
            }

            // ── 2. Expiring batches ───────────────────────────────────────
            $expiring = ProductBatch::where('pharmacy_id', $pharmacy->id)
                ->with('product')
                ->where('quantity', '>', 0)
                ->whereBetween('expiry_date', [now(), now()->addDays($expiryWindow)])
                ->orderBy('expiry_date')
                ->get();

            foreach ($expiring as $batch) {
                $days = (int) now()->diffInDays($batch->expiry_date);
                $this->line("  [EXPIRY] {$batch->product?->name} (Batch {$batch->batch_number}): expires in {$days} day(s)");
                $totalExpiring++;

                if (!$dryRun) {
                    $admin->notify(new ExpiryAlertNotification($batch, $days));
                }
            }

            $this->info("[Pharmacy #{$pharmacy->id}: {$pharmacy->name}] Low-stock: {$products->count()}, Expiring: {$expiring->count()}");
        }

        $suffix = $dryRun ? ' (dry-run — nothing was saved)' : '';
        $summary = "Done. Total low-stock alerts: {$totalLowStock}, expiry alerts: {$totalExpiring}{$suffix}.";

        $this->info($summary);
        Log::channel('daily')->info("pharmacy:stock-alerts — {$summary}");

        return self::SUCCESS;
    }
}
