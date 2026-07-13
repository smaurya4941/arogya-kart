<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Pharmacy;
use App\Models\Subscription;
use App\Services\AuditLogService;
use App\Services\BillingService;
use App\Services\RazorpayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Platform-wide billing desk. Lists every subscription invoice across all
 * tenants and lets the platform owner raise invoices manually, settle or void
 * them, record refunds, download a PDF, and export revenue as CSV.
 *
 * Invoice uses BelongsToPharmacy; the Super Admin bypasses that scope, so these
 * queries and bindings span all pharmacies.
 */
class InvoiceController extends Controller
{
    public function __construct(
        private readonly BillingService $billing,
        private readonly AuditLogService $audit,
        private readonly RazorpayService $razorpay,
    ) {}

    public function index(Request $request)
    {
        $query = $this->filtered($request);

        $invoices = (clone $query)
            ->with(['pharmacy', 'subscription.plan'])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        // Totals over the *filtered* set, not just the current page.
        $totals = (clone $query)
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('COALESCE(SUM(total), 0) as billed')
            ->selectRaw("COALESCE(SUM(CASE WHEN status = ? THEN total ELSE 0 END), 0) as collected", [Invoice::STATUS_PAID])
            ->first();

        return view('superadmin.invoices.index', [
            'invoices'   => $invoices,
            'totals'     => $totals,
            'statuses'   => [Invoice::STATUS_PENDING, Invoice::STATUS_PAID, Invoice::STATUS_FAILED, Invoice::STATUS_REFUNDED, Invoice::STATUS_VOID],
            'pharmacies' => Pharmacy::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** Raise an invoice manually against a subscription. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'amount'          => ['nullable', 'numeric', 'min:0'],
            'status'          => ['required', Rule::in([Invoice::STATUS_PENDING, Invoice::STATUS_PAID])],
        ]);

        $subscription = Subscription::findOrFail($validated['subscription_id']);

        $invoice = $this->billing->generateInvoice($subscription, array_filter([
            'amount' => $validated['amount'] ?? null,
            'status' => $validated['status'],
        ], fn ($v) => $v !== null));

        $this->audit->log(auth()->user(), 'invoice_generated', $invoice, [
            'invoice_number' => $invoice->invoice_number,
            'total'          => $invoice->total,
            'status'         => $invoice->status,
        ]);

        return back()->with('success', "Invoice {$invoice->invoice_number} generated.");
    }

    public function markPaid(Invoice $invoice)
    {
        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->with('error', 'Invoice is already paid.');
        }

        $invoice->update([
            'status'         => Invoice::STATUS_PAID,
            'paid_at'        => now(),
            'payment_method' => $invoice->payment_method ?: 'manual',
        ]);

        $this->audit->log(auth()->user(), 'invoice_marked_paid', $invoice, [
            'invoice_number' => $invoice->invoice_number,
            'total'          => $invoice->total,
        ]);

        return back()->with('success', "Invoice {$invoice->invoice_number} marked paid.");
    }

    public function void(Invoice $invoice)
    {
        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->with('error', 'A paid invoice cannot be voided — issue a refund instead.');
        }

        $invoice->update(['status' => Invoice::STATUS_VOID]);

        $this->audit->log(auth()->user(), 'invoice_voided', $invoice, [
            'invoice_number' => $invoice->invoice_number,
        ]);

        return back()->with('success', "Invoice {$invoice->invoice_number} voided.");
    }

    /**
     * Refund a paid invoice. When the invoice was settled through Razorpay (a real
     * `pay_…` transaction), the refund is pushed to the gateway first — the local
     * status is only flipped if that succeeds, so records never claim a refund the
     * gateway didn't make. Manual/dev invoices are simply marked refunded.
     */
    public function refund(Invoice $invoice)
    {
        if ($invoice->status !== Invoice::STATUS_PAID) {
            return back()->with('error', 'Only a paid invoice can be refunded.');
        }

        $paymentId  = $invoice->transaction_id;
        $viaGateway = $invoice->payment_method === 'razorpay'
            && is_string($paymentId)
            && str_starts_with($paymentId, 'pay_');

        $refundId = null;

        if ($viaGateway) {
            if (! $this->razorpay->isConfigured()) {
                return back()->with('error', 'Razorpay is not configured — cannot process a live refund.');
            }

            try {
                $refund = $this->razorpay->refundPayment($paymentId, (float) $invoice->total, [
                    'invoice_number' => $invoice->invoice_number,
                    'pharmacy_id'    => (string) $invoice->pharmacy_id,
                ]);
                $refundId = $refund['id'] ?? null;
            } catch (\Throwable $e) {
                Log::error('Razorpay refund failed', [
                    'invoice_id' => $invoice->id,
                    'payment_id' => $paymentId,
                    'error'      => $e->getMessage(),
                ]);

                return back()->with('error', 'Refund failed at the payment gateway. The invoice was left unchanged.');
            }
        }

        $invoice->update(['status' => Invoice::STATUS_REFUNDED]);

        $this->audit->log(auth()->user(), 'invoice_refunded', $invoice, [
            'invoice_number' => $invoice->invoice_number,
            'total'          => $invoice->total,
            'gateway'        => $viaGateway ? 'razorpay' : 'manual',
            'refund_id'      => $refundId,
        ]);

        return back()->with('success', $viaGateway
            ? "Invoice {$invoice->invoice_number} refunded via Razorpay ({$refundId})."
            : "Invoice {$invoice->invoice_number} marked refunded (no gateway payment on record).");
    }

    /** Stream the invoice as a PDF (inline). */
    public function pdf(Invoice $invoice)
    {
        $invoice->load(['pharmacy', 'subscription.plan']);

        $pdf = Pdf::loadView('superadmin.invoices.pdf', ['invoice' => $invoice])
            ->setPaper('A4', 'portrait');

        return $pdf->stream("{$invoice->invoice_number}.pdf");
    }

    /** Download the filtered invoice set as CSV (revenue export). */
    public function export(Request $request): StreamedResponse
    {
        $invoices = $this->filtered($request)
            ->with(['pharmacy', 'subscription.plan'])
            ->latest()
            ->get();

        $filename = 'invoices-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($invoices) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice #', 'Pharmacy', 'Plan', 'Amount', 'Tax', 'Total', 'Status', 'Method', 'Transaction', 'Issued', 'Paid At']);

            foreach ($invoices as $invoice) {
                fputcsv($out, [
                    $invoice->invoice_number,
                    $invoice->pharmacy?->name,
                    $invoice->subscription?->plan?->name,
                    $invoice->amount,
                    $invoice->tax,
                    $invoice->total,
                    $invoice->status,
                    $invoice->payment_method,
                    $invoice->transaction_id,
                    $invoice->created_at?->format('Y-m-d H:i'),
                    $invoice->paid_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Shared, filter-aware base query for the list, totals and export so all three
     * stay consistent.
     */
    private function filtered(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        return Invoice::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('pharmacy_id'), fn ($q) => $q->where('pharmacy_id', $request->integer('pharmacy_id')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')));
    }
}
