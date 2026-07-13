<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $sales
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Sale::class);

        $base = Sale::query()
            ->when($request->string('q')->toString(), function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('invoice_number', 'like', "%{$q}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('payment_status', $request->string('status')->toString()))
            ->when($request->filled('method'), fn ($query) => $query->where('payment_method', $request->string('method')->toString()))
            ->when($request->date('date_from'), fn ($query, $from) => $query->whereDate('sale_date', '>=', $from))
            ->when($request->date('date_to'), fn ($query, $to) => $query->whereDate('sale_date', '<=', $to));

        // Aggregate the whole filtered set (not just the current page) for the cards.
        $totals = (clone $base)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(SUM(paid_amount), 0) as paid, COALESCE(SUM(due_amount), 0) as due')
            ->first();

        $sales = $base
            ->with(['customer', 'cashier'])
            ->latest('sale_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.sales.index', compact('sales', 'totals'));
    }

    /**
     * The POS / new-bill screen.
     */
    public function create()
    {
        $this->authorize('create', Sale::class);

        return view('admin.sales.create', [
            'customers' => Customer::orderBy('name')->get(['id', 'name', 'phone']),
        ]);
    }

    public function store(StoreSaleRequest $request)
    {
        $this->authorize('create', Sale::class);

        try {
            $sale = $this->sales->createSale($request->validated());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Could not complete the sale: ' . $e->getMessage());
        }

        // Sales move today's revenue, stock and P&L — refresh the dashboard cache.
        \App\Http\Controllers\DashboardController::flushStats(auth()->user()->pharmacy_id);

        if ($request->input('action') === 'save_print') {
            return redirect()
                ->route('admin.sales.invoice', ['sale' => $sale, 'autoprint' => 1])
                ->with('success', 'Bill saved. Printing…');
        }

        return redirect()
            ->route('admin.sales.show', $sale)
            ->with('success', 'Bill saved successfully.');
    }

    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);

        $sale->load(['customer', 'cashier', 'items.product', 'items.batch', 'items.returnItems', 'returns']);

        return view('admin.sales.show', compact('sale'));
    }

    public function invoice(Sale $sale)
    {
        $this->authorize('view', $sale);

        $sale->load(['customer', 'cashier', 'items.product', 'items.batch']);

        return view('admin.sales.invoice', [
            'sale' => $sale,
            'pharmacy' => auth()->user()->pharmacy,
        ]);
    }

    /**
     * AJAX autocomplete for the POS: returns in-date, in-stock products with
     * their nearest-expiry (FEFO) selling price so the till can price a line.
     */
    public function search(Request $request): JsonResponse
    {
        $this->authorize('create', Sale::class);

        $term = trim((string) $request->query('q', ''));
        if ($term === '') {
            return response()->json([]);
        }

        $products = Product::query()
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%");
            })
            ->with(['batches' => function ($q) {
                $q->where('status', 'active')
                    ->where('quantity', '>', 0)
                    ->whereDate('expiry_date', '>=', now()->toDateString())
                    ->orderBy('expiry_date')
                    ->orderBy('id');
            }])
            ->orderBy('name')
            ->limit(15)
            ->get();

        $results = $products->map(function (Product $product) {
            $available = (int) $product->batches->sum('quantity');
            $nearest = $product->batches->first();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'stock' => $available,
                'price' => $nearest ? (float) $nearest->mrp : 0,
                'gst' => $this->sales->gstRateFor($product->id),
                'nearest_expiry' => $nearest?->expiry_date?->toDateString(),
                'generic_name' => $product->generic_name,
                'storage_conditions' => $product->storage_conditions,
            ];
        })->filter(fn ($row) => $row['stock'] > 0)->values();

        return response()->json($results);
    }
}
