<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\PurchaseInvoiceItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\CustomerLedger;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Handles goods-out: rings up a sale, picks stock FEFO (First-Expiry-First-Out)
 * across batches, calculates GST, generates the invoice number, records the
 * payment split and decrements stock — all inside a single locked transaction so
 * two tills can never oversell the same batch. Mirrors PurchaseService in spirit.
 */
class SaleService
{
    public function __construct(
        private readonly AuditLogService $audit,
        private readonly NumberSequenceService $sequences,
    ) {}

    /**
     * @param  array  $data  customer_id?, payment_method, discount_amount?,
     *                       paid_amount?, notes?, items[] { product_id, quantity,
     *                       discount_percentage? }
     */
    public function createSale(array $data): Sale
    {
        $sale = DB::transaction(function () use ($data) {
            $headerDiscount = round((float) ($data['discount_amount'] ?? 0), 2);

            $sale = Sale::create([
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => auth()->id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'sale_date' => now(),
                'payment_method' => $data['payment_method'],
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => $headerDiscount,
                'total_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'payment_status' => 'unpaid',
                'doctor_name' => $data['doctor_name'] ?? null,
                'doctor_registration_number' => $data['doctor_registration_number'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            $taxTotal = 0;

            foreach ($data['items'] as $line) {
                [$lineBase, $lineTax] = $this->fulfilLine($sale, $line);
                $subtotal += $lineBase;
                $taxTotal += $lineTax;
            }

            $grandTotal = max(0, round($subtotal + $taxTotal - $headerDiscount, 2));

            // Default to a fully-paid bill; only credit sales pass a lower amount.
            $paid = array_key_exists('paid_amount', $data) && $data['paid_amount'] !== null
                ? round((float) $data['paid_amount'], 2)
                : $grandTotal;
            $paid = min($paid, $grandTotal);
            $due = round($grandTotal - $paid, 2);

            $sale->update([
                'subtotal' => round($subtotal, 2),
                'tax_amount' => round($taxTotal, 2),
                'total_amount' => $grandTotal,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_status' => $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
            ]);

            if ($due > 0 && $sale->customer_id) {
                CustomerLedger::create([
                    'pharmacy_id' => $sale->pharmacy_id,
                    'customer_id' => $sale->customer_id,
                    'type' => 'sale',
                    'amount' => $due,
                    'reference' => $sale->invoice_number,
                    'date' => $sale->sale_date->toDateString(),
                    'description' => "Credit purchase on invoice #{$sale->invoice_number}",
                ]);

                $customer = Customer::find($sale->customer_id);
                $customer->increment('outstanding_balance', $due);
            }
            
            // Handle DPDP Consent
            if ($sale->customer_id && isset($data['consent_given']) && $data['consent_given']) {
                $customer = Customer::find($sale->customer_id);
                if ($customer && !$customer->consent_given) {
                    $customer->update([
                        'consent_given' => true,
                        'consent_date' => now(),
                    ]);
                }
            }
            
            // Handle Split Payments
            if ($data['payment_method'] === 'split' && !empty($data['payments'])) {
                foreach ($data['payments'] as $payment) {
                    \App\Models\SalePayment::create([
                        'sale_id' => $sale->id,
                        'payment_method' => $payment['method'],
                        'amount' => round((float) $payment['amount'], 2),
                    ]);
                }
            } elseif ($paid > 0 && $data['payment_method'] !== 'credit') {
                \App\Models\SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method' => $data['payment_method'],
                    'amount' => $paid,
                ]);
            }

            return $sale;
        });

        $this->audit->log(auth()->user(), 'sale_created', $sale, [
            'invoice_number' => $sale->invoice_number,
            'customer_id' => $sale->customer_id,
            'total_amount' => $sale->total_amount,
        ]);

        return $sale;
    }

    /**
     * Consume `quantity` of a product FEFO across its batches, writing one
     * sale_item per batch slice and decrementing stock. Returns [base, tax] for
     * the whole line so the caller can accumulate the invoice totals.
     *
     * @return array{0: float, 1: float}
     */
    private function fulfilLine(Sale $sale, array $line): array
    {
        $productId = (int) $line['product_id'];
        $quantity = (int) $line['quantity'];
        $discountPct = round((float) ($line['discount_percentage'] ?? 0), 2);
        $gstPct = $this->gstRateFor($productId);

        // FEFO: nearest expiry first, active + in-date + in-stock only. Locked so a
        // concurrent sale of the same batch waits for us to commit before reading.
        $batches = ProductBatch::query()
            ->where('product_id', $productId)
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '>=', now()->toDateString())
            ->orderBy('expiry_date')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $available = (int) $batches->sum('quantity');
        if ($available < $quantity) {
            $name = optional($batches->first()?->product)->name
                ?? \App\Models\Product::find($productId)?->name
                ?? "product #{$productId}";

            throw ValidationException::withMessages([
                'items' => "Insufficient in-date stock for {$name}. Requested {$quantity}, only {$available} available.",
            ]);
        }

        $lineBase = 0;
        $lineTax = 0;
        $remaining = $quantity;

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $take = min((int) $batch->quantity, $remaining);
            $unitPrice = (float) $batch->mrp;

            $base = round($unitPrice * $take * (1 - $discountPct / 100), 2);
            $tax = round($base * $gstPct / 100, 2);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $productId,
                'product_batch_id' => $batch->id,
                'quantity' => $take,
                'unit_price' => $unitPrice,
                'mrp' => $unitPrice,
                'discount_percentage' => $discountPct,
                'tax_percentage' => $gstPct,
                'total' => round($base + $tax, 2),
            ]);

            $batch->decrement('quantity', $take);

            StockMovement::create([
                'product_id' => $productId,
                'product_batch_id' => $batch->id,
                'user_id' => auth()->id(),
                'type' => 'sale',
                'quantity' => -$take,
                'reference_id' => $sale->invoice_number,
                'notes' => 'Stock sold via POS',
            ]);

            $lineBase += $base;
            $lineTax += $tax;
            $remaining -= $take;
        }

        return [$lineBase, $lineTax];
    }

    /**
     * A product's GST rate isn't stored on the product, so we reuse the rate it
     * was most recently purchased at (per tenant, since product_id is scoped).
     * Public so the POS search endpoint can price lines the same way checkout will.
     */
    public function gstRateFor(int $productId): float
    {
        return (float) (PurchaseInvoiceItem::where('product_id', $productId)
            ->latest('id')
            ->value('gst_percentage') ?? 0);
    }

    private function generateInvoiceNumber(): string
    {
        // Atomic per-tenant counter (see NumberSequenceService) — safe when two
        // tills check out simultaneously. Runs inside createSale()'s transaction.
        $sequence = $this->sequences->next($this->currentPharmacyId(), 'sale');

        return 'INV-' . now()->format('Ymd') . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function currentPharmacyId(): ?int
    {
        return auth()->user()?->pharmacy_id;
    }
}
