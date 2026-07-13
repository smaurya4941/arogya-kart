<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Pharmacy;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Cross-tenant operational visibility. Every list here is read-only: the platform
 * owner can browse (and filter by tenant) the products, sales, purchases,
 * customers and expenses of every pharmacy on the platform, plus an aggregate
 * overview. All these models use BelongsToPharmacy, which the Super Admin
 * bypasses, so the queries naturally span all tenants; we eager-load `pharmacy`
 * so each row is attributable.
 */
class OperationsController extends Controller
{
    /** Aggregate control tower across every tenant. */
    public function index()
    {
        $salesAllTime   = (float) Sale::sum('total_amount');
        $salesThisMonth = (float) Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total_amount');

        $purchasesAllTime = (float) PurchaseInvoice::sum('total_amount');
        $expensesThisMonth = (float) Expense::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        // Top tenants by sales value this month.
        $topPharmacies = Sale::query()
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->select('pharmacy_id', DB::raw('COUNT(*) as sale_count'), DB::raw('SUM(total_amount) as sales_value'))
            ->groupBy('pharmacy_id')
            ->orderByDesc('sales_value')
            ->with('pharmacy:id,name')
            ->take(8)
            ->get();

        return view('superadmin.operations.index', [
            'salesAllTime'      => $salesAllTime,
            'salesThisMonth'    => $salesThisMonth,
            'purchasesAllTime'  => $purchasesAllTime,
            'expensesThisMonth' => $expensesThisMonth,
            'productCount'      => Product::count(),
            'customerCount'     => Customer::count(),
            'topPharmacies'     => $topPharmacies,
        ]);
    }

    public function products(Request $request)
    {
        $products = Product::query()
            ->with(['pharmacy:id,name', 'category:id,name'])
            // Distinct alias (not "total_stock") so it doesn't collide with the
            // Product::getTotalStockAttribute accessor, which would re-query per row.
            ->withSum('batches as stock_qty', 'quantity')
            ->when($request->filled('pharmacy_id'), fn ($q) => $q->where('pharmacy_id', $request->integer('pharmacy_id')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $term)->orWhere('sku', 'like', $term)->orWhere('generic_name', 'like', $term));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('superadmin.operations.products', $this->withTenants([
            'products' => $products,
        ], $request));
    }

    public function sales(Request $request)
    {
        $query = Sale::query()
            ->with(['pharmacy:id,name', 'customer:id,name', 'cashier:id,name'])
            ->when($request->filled('pharmacy_id'), fn ($q) => $q->where('pharmacy_id', $request->integer('pharmacy_id')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')))
            ->when($request->filled('q'), fn ($q) => $q->where('invoice_number', 'like', '%' . $request->string('q') . '%'))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('sale_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('sale_date', '<=', $request->date('to')));

        $total = (float) (clone $query)->sum('total_amount');

        return view('superadmin.operations.sales', $this->withTenants([
            'sales' => $query->latest('sale_date')->paginate(30)->withQueryString(),
            'total' => $total,
        ], $request));
    }

    public function purchases(Request $request)
    {
        $query = PurchaseInvoice::query()
            ->with(['pharmacy:id,name', 'supplier:id,name'])
            ->when($request->filled('pharmacy_id'), fn ($q) => $q->where('pharmacy_id', $request->integer('pharmacy_id')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $q->where(fn ($sub) => $sub->where('invoice_number', 'like', $term)->orWhere('supplier_invoice_number', 'like', $term));
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('purchase_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('purchase_date', '<=', $request->date('to')));

        $total = (float) (clone $query)->sum('total_amount');

        return view('superadmin.operations.purchases', $this->withTenants([
            'purchases' => $query->latest('purchase_date')->paginate(30)->withQueryString(),
            'total'     => $total,
        ], $request));
    }

    public function customers(Request $request)
    {
        $customers = Customer::query()
            ->with('pharmacy:id,name')
            ->withCount('sales')
            ->when($request->filled('pharmacy_id'), fn ($q) => $q->where('pharmacy_id', $request->integer('pharmacy_id')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $term)->orWhere('phone', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('superadmin.operations.customers', $this->withTenants([
            'customers' => $customers,
        ], $request));
    }

    public function expenses(Request $request)
    {
        $query = Expense::query()
            ->with(['pharmacy:id,name', 'category:id,name'])
            ->when($request->filled('pharmacy_id'), fn ($q) => $q->where('pharmacy_id', $request->integer('pharmacy_id')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $q->where(fn ($sub) => $sub->where('vendor', 'like', $term)->orWhere('description', 'like', $term));
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('date', '<=', $request->date('to')));

        $total = (float) (clone $query)->sum('amount');

        return view('superadmin.operations.expenses', $this->withTenants([
            'expenses' => $query->latest('date')->paginate(30)->withQueryString(),
            'total'    => $total,
        ], $request));
    }

    /**
     * Attach the tenant filter dropdown data (and the currently-selected tenant)
     * that every browser view shares.
     *
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    private function withTenants(array $data, Request $request): array
    {
        return array_merge($data, [
            'pharmacies'      => Pharmacy::orderBy('name')->get(['id', 'name']),
            'selectedTenant'  => $request->integer('pharmacy_id') ?: null,
        ]);
    }
}
