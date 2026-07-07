<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Illuminate\Support\Facades\DB;

/**
 * Handles goods-in: records a purchase invoice, creates a fresh batch for each
 * line (which is what actually raises stock), logs stock movements and updates
 * the supplier ledger — all atomically. Mirrors InventoryService in spirit.
 */
class PurchaseService
{
    public function __construct(
        private readonly AuditLogService $audit,
        private readonly NumberSequenceService $sequences,
    ) {}

    /**
     * @param  array  $data  supplier_id, purchase_date, supplier_invoice_number?,
     *                       payment_terms?, notes?, items[] { product_id, batch_number,
     *                       expiry_date, quantity, purchase_price, mrp, selling_price?,
     *                       gst_percentage? }
     */
    public function createPurchase(array $data): PurchaseInvoice
    {
        $invoice = DB::transaction(function () use ($data) {
            $invoice = PurchaseInvoice::create([
                'supplier_id' => $data['supplier_id'],
                'invoice_number' => $this->generateInvoiceNumber(),
                'supplier_invoice_number' => $data['supplier_invoice_number'] ?? null,
                'purchase_date' => $data['purchase_date'],
                'payment_terms' => $data['payment_terms'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_amount' => 0,
            ]);

            $total = 0;

            foreach ($data['items'] as $line) {
                $quantity = (int) $line['quantity'];
                $purchasePrice = (float) $line['purchase_price'];
                $gstPercentage = (float) ($line['gst_percentage'] ?? 0);

                $base = $quantity * $purchasePrice;
                $lineTotal = round($base + ($base * $gstPercentage / 100), 2);
                $total += $lineTotal;

                // Creating the batch IS the stock-in.
                $batch = ProductBatch::create([
                    'product_id' => $line['product_id'],
                    'batch_number' => $line['batch_number'],
                    'expiry_date' => $line['expiry_date'],
                    'purchase_price' => $purchasePrice,
                    'mrp' => $line['mrp'],
                    'quantity' => $quantity,
                ]);

                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $line['product_id'],
                    'product_batch_id' => $batch->id,
                    'quantity' => $quantity,
                    'purchase_price' => $purchasePrice,
                    'mrp' => $line['mrp'],
                    'selling_price' => $line['selling_price'] ?? $line['mrp'],
                    'gst_percentage' => $gstPercentage,
                    'total' => $lineTotal,
                ]);

                StockMovement::create([
                    'product_id' => $line['product_id'],
                    'product_batch_id' => $batch->id,
                    'user_id' => auth()->id(),
                    'type' => 'purchase',
                    'quantity' => $quantity,
                    'reference_id' => $invoice->invoice_number,
                    'notes' => 'Stock received via purchase',
                ]);
            }

            $invoice->update(['total_amount' => $total]);

            $this->recordSupplierLedger($invoice->supplier_id, $total, $invoice->invoice_number);

            return $invoice;
        });

        $this->audit->log(auth()->user(), 'purchase_created', $invoice, [
            'invoice_number' => $invoice->invoice_number,
            'supplier_id' => $invoice->supplier_id,
            'total_amount' => $invoice->total_amount,
        ]);

        return $invoice;
    }

    private function recordSupplierLedger(int $supplierId, float $amount, string $reference): void
    {
        $previousBalance = (float) (SupplierLedger::where('supplier_id', $supplierId)
            ->latest('id')
            ->value('balance') ?? 0);

        SupplierLedger::create([
            'supplier_id' => $supplierId,
            'type' => 'purchase',
            'amount' => $amount,
            'balance' => $previousBalance + $amount,
            'reference_id' => $reference,
            'notes' => 'Purchase invoice',
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        // Atomic per-tenant counter (see NumberSequenceService) — safe against
        // concurrent goods-in. Runs inside createPurchase()'s transaction.
        $sequence = $this->sequences->next($this->currentPharmacyId(), 'purchase');

        return 'PUR-' . now()->format('Ymd') . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function currentPharmacyId(): ?int
    {
        return auth()->user()?->pharmacy_id;
    }
}
