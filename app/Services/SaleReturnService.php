<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Handles goods back-in: processes a customer return against an existing sale.
 * The mirror image of SaleService — restores stock to the exact batch each unit
 * was sold from, records a stock movement, and issues a credit-note refund. All
 * inside one locked transaction so a return can't race a concurrent sale of the
 * same batch or over-return a line.
 */
class SaleReturnService
{
    public function __construct(
        private readonly AuditLogService $audit,
        private readonly NumberSequenceService $sequences,
    ) {}

    /**
     * @param  array  $data  reason?, refund_method, lines[] { sale_item_id, quantity }
     */
    public function processReturn(Sale $sale, array $data): SaleReturn
    {
        // Drop empty lines and normalise before we open the transaction.
        $lines = collect($data['lines'] ?? [])
            ->map(fn ($l) => ['sale_item_id' => (int) ($l['sale_item_id'] ?? 0), 'quantity' => (int) ($l['quantity'] ?? 0)])
            ->filter(fn ($l) => $l['sale_item_id'] > 0 && $l['quantity'] > 0)
            ->values();

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages(['lines' => 'Select at least one item and quantity to return.']);
        }

        $return = DB::transaction(function () use ($sale, $data, $lines) {
            // Load the sale's items with their already-returned quantities, locked
            // against concurrent returns of the same sale.
            $sale->load(['items.returnItems']);
            $itemsById = $sale->items->keyBy('id');

            $return = SaleReturn::create([
                'pharmacy_id'   => $sale->pharmacy_id,
                'sale_id'       => $sale->id,
                'user_id'       => auth()->id(),
                'return_number' => $this->generateReturnNumber($sale->pharmacy_id),
                'reason'        => $data['reason'] ?? null,
                'refund_method' => $data['refund_method'] ?? 'cash',
                'subtotal'      => 0,
                'tax_amount'    => 0,
                'total_amount'  => 0,
            ]);

            $refundTotal = 0.0;
            $taxTotal    = 0.0;

            foreach ($lines as $line) {
                $saleItem = $itemsById->get($line['sale_item_id']);

                if (! $saleItem) {
                    throw ValidationException::withMessages(['lines' => 'One of the selected items does not belong to this sale.']);
                }

                $qty = $line['quantity'];
                $returnable = $saleItem->returnableQuantity();

                if ($qty > $returnable) {
                    throw ValidationException::withMessages([
                        'lines' => "Cannot return {$qty} of {$saleItem->product?->name}; only {$returnable} remain returnable.",
                    ]);
                }

                // Prorate the original line's value (base + tax already baked into
                // SaleItem::total) across the returned quantity.
                $lineRefund = round($saleItem->unitRefundValue() * $qty, 2);
                $taxPct     = (float) $saleItem->tax_percentage;
                // Split the gross refund back into base + tax for reporting.
                $lineTax    = $taxPct > 0 ? round($lineRefund * $taxPct / (100 + $taxPct), 2) : 0.0;

                $return->items()->create([
                    'sale_item_id'     => $saleItem->id,
                    'product_id'       => $saleItem->product_id,
                    'product_batch_id' => $saleItem->product_batch_id,
                    'quantity'         => $qty,
                    'unit_price'       => $saleItem->unit_price,
                    'tax_percentage'   => $taxPct,
                    'total'            => $lineRefund,
                ]);

                // Restore stock to the exact batch it left from.
                if ($saleItem->product_batch_id) {
                    ProductBatch::where('id', $saleItem->product_batch_id)
                        ->lockForUpdate()
                        ->first()
                        ?->increment('quantity', $qty);
                }

                StockMovement::create([
                    'pharmacy_id'      => $sale->pharmacy_id,
                    'product_id'       => $saleItem->product_id,
                    'product_batch_id' => $saleItem->product_batch_id,
                    'user_id'          => auth()->id(),
                    'type'             => 'sale_return',
                    'quantity'         => $qty, // positive: stock coming back in
                    'reference_id'     => $return->return_number,
                    'notes'            => 'Customer return against ' . $sale->invoice_number,
                ]);

                $refundTotal += $lineRefund;
                $taxTotal    += $lineTax;
            }

            $return->update([
                'subtotal'     => round($refundTotal - $taxTotal, 2),
                'tax_amount'   => round($taxTotal, 2),
                'total_amount' => round($refundTotal, 2),
            ]);

            return $return;
        });

        $this->audit->log(auth()->user(), 'sale_returned', $return, [
            'return_number'  => $return->return_number,
            'sale_id'        => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'total_refunded' => $return->total_amount,
        ]);

        return $return;
    }

    private function generateReturnNumber(?int $pharmacyId): string
    {
        $sequence = $this->sequences->next($pharmacyId, 'sale_return');

        return 'RET-' . now()->format('Ymd') . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
