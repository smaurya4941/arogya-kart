<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Services\SaleReturnService;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    public function __construct(
        private readonly SaleReturnService $returns
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', SaleReturn::class);

        $returns = SaleReturn::query()
            ->with(['sale', 'processor'])
            ->when($request->string('q')->toString(), function ($query, $q) {
                $query->where('return_number', 'like', "%{$q}%")
                    ->orWhereHas('sale', fn ($s) => $s->where('invoice_number', 'like', "%{$q}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.returns.index', compact('returns'));
    }

    /** Return form for a specific sale. */
    public function create(Sale $sale)
    {
        $this->authorize('create', SaleReturn::class);

        $sale->load(['items.product', 'items.returnItems', 'customer']);

        abort_if(! $sale->hasReturnableItems(), 422, 'This sale has no items left to return.');

        return view('admin.returns.create', compact('sale'));
    }

    public function store(Request $request, Sale $sale)
    {
        $this->authorize('create', SaleReturn::class);

        $validated = $request->validate([
            'reason'          => ['nullable', 'string', 'max:255'],
            'refund_method'   => ['required', 'in:cash,upi,card,adjustment'],
            'lines'           => ['required', 'array', 'min:1'],
            'lines.*.sale_item_id' => ['required', 'integer'],
            'lines.*.quantity'     => ['required', 'integer', 'min:0'],
        ]);

        try {
            $return = $this->returns->processReturn($sale, $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Could not process the return: ' . $e->getMessage());
        }

        // Returns move stock and refunds — refresh the owner dashboard figures.
        \App\Http\Controllers\DashboardController::flushStats($sale->pharmacy_id);

        return redirect()
            ->route('admin.returns.show', $return)
            ->with('success', "Return {$return->return_number} processed. ₹" . number_format($return->total_amount, 2) . ' refunded.');
    }

    public function show(SaleReturn $return)
    {
        $this->authorize('view', $return);

        $return->load(['sale.customer', 'processor', 'items.product', 'items.batch']);

        return view('admin.returns.show', compact('return'));
    }
}
