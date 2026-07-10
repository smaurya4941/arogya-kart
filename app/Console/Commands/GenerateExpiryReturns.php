<?php

namespace App\Console\Commands;

use App\Models\ProductBatch;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateExpiryReturns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pharmacy:generate-expiry-returns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scans for batches expiring within 45 days and drafts supplier returns.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for near-expiry product batches...');

        $thresholdDate = Carbon::now()->addDays(45)->startOfDay();

        // Find batches expiring within 45 days that still have stock
        // and haven't already been added to a return draft.
        $nearExpiryBatches = ProductBatch::with(['product'])
            ->where('quantity_remaining', '>', 0)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $thresholdDate)
            ->whereNotNull('supplier_id') // Only process batches with known suppliers
            ->get();

        if ($nearExpiryBatches->isEmpty()) {
            $this->info('No near-expiry batches found.');
            return;
        }

        // Group by pharmacy and supplier so we create one return per supplier per tenant
        $groupedBatches = $nearExpiryBatches->groupBy(function ($batch) {
            return $batch->pharmacy_id . '_' . $batch->supplier_id;
        });

        DB::beginTransaction();
        try {
            foreach ($groupedBatches as $key => $batches) {
                // Determine pharmacy and supplier from the first batch
                $firstBatch = $batches->first();
                $pharmacyId = $firstBatch->pharmacy_id;
                $supplierId = $firstBatch->supplier_id;

                // Create a draft return
                $purchaseReturn = PurchaseReturn::create([
                    'pharmacy_id' => $pharmacyId,
                    'supplier_id' => $supplierId,
                    'return_number' => 'RET-' . strtoupper(Str::random(8)),
                    'return_date' => Carbon::now(),
                    'reason' => 'Near Expiry automated return',
                    'total_amount' => 0,
                    'status' => 'draft',
                ]);

                $totalAmount = 0;

                foreach ($batches as $batch) {
                    $itemTotal = $batch->quantity_remaining * $batch->purchase_price;
                    $totalAmount += $itemTotal;

                    PurchaseReturnItem::create([
                        'pharmacy_id' => $pharmacyId,
                        'purchase_return_id' => $purchaseReturn->id,
                        'product_id' => $batch->product_id,
                        'product_batch_id' => $batch->id,
                        'quantity' => $batch->quantity_remaining,
                        'unit_price' => $batch->purchase_price,
                        'total' => $itemTotal,
                    ]);
                    
                    // Note: We do NOT decrement the stock here. 
                    // Stock should be decremented when the status changes from 'draft' to 'sent'.
                }

                $purchaseReturn->update(['total_amount' => $totalAmount]);
                $this->info("Generated draft return {$purchaseReturn->return_number} for Supplier ID {$supplierId} (Pharmacy {$pharmacyId})");
            }
            
            DB::commit();
            $this->info('Automated expiry returns generated successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to generate returns: ' . $e->getMessage());
        }
    }
}
