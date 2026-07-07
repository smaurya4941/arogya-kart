<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Centralises the read-model logic for the reporting module so controllers stay
 * thin and the same aggregates can be reused for on-screen tables and PDF exports.
 *
 * Every query flows through the pharmacy-scoped Eloquent models (Sale,
 * PurchaseInvoice, Expense, Product), so tenant isolation from BelongsToPharmacy
 * is preserved automatically.
 */
class ReportService
{
    /**
     * Normalise a request date range into a [start-of-day, end-of-day] tuple.
     * Defaults to the current month when nothing is supplied.
     */
    public function resolveRange(?string $from, ?string $to): array
    {
        $start = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        // Guard against an inverted range so the BETWEEN clauses stay valid.
        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    /*
    |--------------------------------------------------------------------------
    | Sales
    |--------------------------------------------------------------------------
    */

    public function salesQuery(Carbon $start, Carbon $end)
    {
        return Sale::query()
            ->with(['customer', 'cashier'])
            ->whereBetween('sale_date', [$start, $end])
            ->orderByDesc('sale_date')
            ->orderByDesc('id');
    }

    public function salesReport(Carbon $start, Carbon $end): array
    {
        $summary = Sale::query()
            ->whereBetween('sale_date', [$start, $end])
            ->selectRaw('COUNT(*) as invoices')
            ->selectRaw('COALESCE(SUM(subtotal), 0) as subtotal')
            ->selectRaw('COALESCE(SUM(discount_amount), 0) as discount')
            ->selectRaw('COALESCE(SUM(tax_amount), 0) as tax')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total')
            ->selectRaw('COALESCE(SUM(paid_amount), 0) as paid')
            ->selectRaw('COALESCE(SUM(due_amount), 0) as due')
            ->first();

        return [
            'invoices' => (int) $summary->invoices,
            'subtotal' => (float) $summary->subtotal,
            'discount' => (float) $summary->discount,
            'tax' => (float) $summary->tax,
            'total' => (float) $summary->total,
            'paid' => (float) $summary->paid,
            'due' => (float) $summary->due,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */

    public function purchasesQuery(Carbon $start, Carbon $end)
    {
        return PurchaseInvoice::query()
            ->with('supplier')
            ->whereBetween('purchase_date', [$start, $end])
            ->orderByDesc('purchase_date')
            ->orderByDesc('id');
    }

    public function purchasesReport(Carbon $start, Carbon $end): array
    {
        $summary = PurchaseInvoice::query()
            ->whereBetween('purchase_date', [$start, $end])
            ->selectRaw('COUNT(*) as invoices')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total')
            ->first();

        return [
            'invoices' => (int) $summary->invoices,
            'total' => (float) $summary->total,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Profit & Loss
    |--------------------------------------------------------------------------
    */

    /**
     * Cost of goods actually sold in the window, valued at the purchase price of
     * the specific batch each line was dispensed from. This is what makes the P&L
     * accurate rather than approximating COGS with total purchases.
     */
    public function costOfGoodsSold(Carbon $start, Carbon $end): float
    {
        return (float) DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('product_batches', 'product_batches.id', '=', 'sale_items.product_batch_id')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->whereIn('sales.id', Sale::query()->select('id'))
            ->sum(DB::raw('sale_items.quantity * product_batches.purchase_price'));
    }

    /**
     * Expense totals grouped by category, plus the grand total, for the window.
     *
     * @return array{total: float, byCategory: Collection}
     */
    public function expensesBreakdown(Carbon $start, Carbon $end): array
    {
        $byCategory = Expense::query()
            ->whereBetween('date', [$start, $end])
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('amount')
            ->get([
                'expense_categories.name as category',
                DB::raw('COALESCE(SUM(expenses.amount), 0) as amount'),
            ]);

        return [
            'total' => (float) $byCategory->sum('amount'),
            'byCategory' => $byCategory,
        ];
    }

    /**
     * Full profit-and-loss statement for the window.
     */
    public function profitAndLoss(Carbon $start, Carbon $end): array
    {
        $sales = $this->salesReport($start, $end);
        $cogs = $this->costOfGoodsSold($start, $end);
        $expenses = $this->expensesBreakdown($start, $end);

        // Revenue is measured net of tax collected on behalf of the government;
        // the tax is a pass-through liability, not income.
        $revenue = $sales['total'] - $sales['tax'];
        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses['total'];

        return [
            'revenue' => $revenue,
            'gross_sales' => $sales['total'],
            'tax_collected' => $sales['tax'],
            'discounts' => $sales['discount'],
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'gross_margin' => $revenue > 0 ? round(($grossProfit / $revenue) * 100, 2) : 0.0,
            'expenses_total' => $expenses['total'],
            'expenses_by_category' => $expenses['byCategory'],
            'net_profit' => $netProfit,
            'net_margin' => $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0.0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GST
    |--------------------------------------------------------------------------
    */

    /**
     * Output tax (collected on sales) vs input tax (paid on purchases) and the
     * resulting net GST payable. Rates are read from the line items so a mixed
     * cart with several slabs is broken out per slab.
     */
    public function gstReport(Carbon $start, Carbon $end): array
    {
        $output = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->whereIn('sales.id', Sale::query()->select('id'))
            ->where('sale_items.tax_percentage', '>', 0)
            ->groupBy('sale_items.tax_percentage')
            ->orderBy('sale_items.tax_percentage')
            ->get([
                'sale_items.tax_percentage as rate',
                DB::raw('COALESCE(SUM(sale_items.total), 0) as taxable_total'),
                DB::raw('COALESCE(SUM(sale_items.total * sale_items.tax_percentage / (100 + sale_items.tax_percentage)), 0) as tax_amount'),
            ]);

        $input = DB::table('purchase_invoice_items')
            ->join('purchase_invoices', 'purchase_invoices.id', '=', 'purchase_invoice_items.purchase_invoice_id')
            ->whereBetween('purchase_invoices.purchase_date', [$start, $end])
            ->whereIn('purchase_invoices.id', PurchaseInvoice::query()->select('id'))
            ->where('purchase_invoice_items.gst_percentage', '>', 0)
            ->groupBy('purchase_invoice_items.gst_percentage')
            ->orderBy('purchase_invoice_items.gst_percentage')
            ->get([
                'purchase_invoice_items.gst_percentage as rate',
                DB::raw('COALESCE(SUM(purchase_invoice_items.total), 0) as taxable_total'),
                DB::raw('COALESCE(SUM(purchase_invoice_items.total * purchase_invoice_items.gst_percentage / (100 + purchase_invoice_items.gst_percentage)), 0) as tax_amount'),
            ]);

        $outputTax = (float) $output->sum('tax_amount');
        $inputTax = (float) $input->sum('tax_amount');

        return [
            'output_slabs' => $output,
            'input_slabs' => $input,
            'output_tax' => $outputTax,
            'input_tax' => $inputTax,
            'net_payable' => $outputTax - $inputTax,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */

    public function inventoryQuery()
    {
        return Product::query()
            ->with(['category', 'batches'])
            ->orderBy('name');
    }

    /**
     * Total stock-on-hand valued at batch purchase price (cost) and MRP (retail).
     */
    public function inventoryValuation(): array
    {
        $row = DB::table('product_batches')
            ->whereIn('product_id', Product::query()->select('id'))
            ->where('product_batches.quantity', '>', 0)
            ->selectRaw('COALESCE(SUM(quantity), 0) as units')
            ->selectRaw('COALESCE(SUM(quantity * purchase_price), 0) as cost_value')
            ->selectRaw('COALESCE(SUM(quantity * mrp), 0) as retail_value')
            ->first();

        return [
            'units' => (int) $row->units,
            'cost_value' => (float) $row->cost_value,
            'retail_value' => (float) $row->retail_value,
            'potential_margin' => (float) $row->retail_value - (float) $row->cost_value,
        ];
    }

    /**
     * Paginate a query while preserving the current query string for links.
     */
    public function paginate($query, int $perPage = 20): LengthAwarePaginator
    {
        return $query->paginate($perPage)->withQueryString();
    }
}
