<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePurchaseRequest;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly PurchaseService $purchases
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseInvoice::class);

        $invoices = PurchaseInvoice::query()
            ->with('supplier')
            ->when($request->string('q')->toString(), function ($query, $q) {
                $query->where('invoice_number', 'like', "%{$q}%")
                    ->orWhere('supplier_invoice_number', 'like', "%{$q}%");
            })
            ->when($request->filled('supplier_id'), function ($query) use ($request) {
                $query->where('supplier_id', $request->integer('supplier_id'));
            })
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.purchases.index', [
            'invoices' => $invoices,
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', PurchaseInvoice::class);

        return view('admin.purchases.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku']),
            'selectedSupplierId' => $request->integer('supplier_id') ?: null,
        ]);
    }

    public function store(StorePurchaseRequest $request)
    {
        $this->authorize('create', PurchaseInvoice::class);

        try {
            $invoice = $this->purchases->createPurchase($request->validated());
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Could not record the purchase: ' . $e->getMessage());
        }

        // New stock changes inventory valuation and low-stock counts on the dashboard.
        \App\Http\Controllers\DashboardController::flushStats(auth()->user()->pharmacy_id);

        return redirect()
            ->route('admin.purchases.show', $invoice)
            ->with('success', 'Purchase recorded and stock received.');
    }

    public function show(PurchaseInvoice $purchase)
    {
        $this->authorize('view', $purchase);

        $purchase->load(['supplier', 'items.product', 'items.batch']);

        return view('admin.purchases.show', compact('purchase'));
    }
}
