<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(
        private readonly AuditLogService $audit
    ) {}

    public function issueStock(Product $product, int $quantity): void
    {
        DB::transaction(function () use ($product, $quantity) {
            $remaining = $quantity;

            $batches = $product->availableBatches()
                ->lockForUpdate()
                ->get();

            $totalAvailable = $batches->sum('quantity');
            if ($totalAvailable < $remaining) {
                throw new \RuntimeException('Insufficient stock to issue.');
            }

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $deduct = min($batch->quantity, $remaining);
                $batch->decrement('quantity', $deduct);
                $remaining -= $deduct;
            }
        });

        $this->audit->log(auth()->user(), 'stock_issued', $product, [
            'quantity' => $quantity,
        ]);
    }
}
