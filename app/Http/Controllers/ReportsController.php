<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateGstPdfJob;
use App\Jobs\GeneratePurchasesPdfJob;
use App\Jobs\GenerateSalesPdfJob;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportsController extends Controller
{
    public function __construct(private readonly ReportService $reports)
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Sales
    |--------------------------------------------------------------------------
    */

    public function sales(Request $request)
    {
        $this->authorizeReports();
        [$start, $end] = $this->range($request);

        $summary = $this->reports->salesReport($start, $end);
        $sales = $this->reports->paginate($this->reports->salesQuery($start, $end));

        return view('admin.reports.sales', compact('sales', 'summary', 'start', 'end'));
    }

    public function salesPdf(Request $request)
    {
        $this->authorizeReports();
        [$start, $end] = $this->range($request);

        GenerateSalesPdfJob::dispatch(auth()->id(), $start->toDateString(), $end->toDateString());

        return redirect()->route('admin.reports.sales')
            ->with('success', '📄 Your Sales PDF is being generated. We\'ll notify you when it\'s ready to download.');
    }

    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */

    public function purchases(Request $request)
    {
        $this->authorizeReports();
        [$start, $end] = $this->range($request);

        $summary = $this->reports->purchasesReport($start, $end);
        $purchases = $this->reports->paginate($this->reports->purchasesQuery($start, $end));

        return view('admin.reports.purchases', compact('purchases', 'summary', 'start', 'end'));
    }

    public function purchasesPdf(Request $request)
    {
        $this->authorizeReports();
        [$start, $end] = $this->range($request);

        GeneratePurchasesPdfJob::dispatch(auth()->id(), $start->toDateString(), $end->toDateString());

        return redirect()->route('admin.reports.purchases')
            ->with('success', '📄 Your Purchases PDF is being generated. We\'ll notify you when it\'s ready to download.');
    }

    /*
    |--------------------------------------------------------------------------
    | Profit & Loss
    |--------------------------------------------------------------------------
    */

    public function profit(Request $request)
    {
        $this->authorizeReports();
        [$start, $end] = $this->range($request);

        $pnl = $this->reports->profitAndLoss($start, $end);

        return view('admin.reports.profit', compact('pnl', 'start', 'end'));
    }

    /*
    |--------------------------------------------------------------------------
    | GST
    |--------------------------------------------------------------------------
    */

    public function gst(Request $request)
    {
        $this->authorizeReports();
        [$start, $end] = $this->range($request);

        $gst = $this->reports->gstReport($start, $end);

        return view('admin.reports.gst', compact('gst', 'start', 'end'));
    }

    public function gstPdf(Request $request)
    {
        $this->authorizeReports();
        [$start, $end] = $this->range($request);

        GenerateGstPdfJob::dispatch(auth()->id(), $start->toDateString(), $end->toDateString());

        return redirect()->route('admin.reports.gst')
            ->with('success', '📄 Your GST Report PDF is being generated. We\'ll notify you when it\'s ready to download.');
    }

    /**
     * Serve a generated PDF export from storage.
     *
     * The filename is validated to prevent path traversal; only .pdf files
     * inside the exports/ directory are served. Access is restricted to
     * authenticated admin users.
     */
    public function download(string $filename)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        // Prevent path traversal — only allow alphanumeric, dash, underscore, dot
        abort_if(!preg_match('/^[\w\-]+\.pdf$/i', $filename), 404);

        $path = "exports/{$filename}";

        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */

    public function inventory(Request $request)
    {
        $this->authorizeReports();

        $valuation = $this->reports->inventoryValuation();
        $products = $this->reports->paginate($this->reports->inventoryQuery());

        return view('admin.reports.inventory', compact('products', 'valuation'));
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function range(Request $request): array
    {
        return $this->reports->resolveRange(
            $request->query('from'),
            $request->query('to'),
        );
    }

    private function authorizeReports(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
    }
}
