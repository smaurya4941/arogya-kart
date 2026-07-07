<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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

        $summary = $this->reports->salesReport($start, $end);
        $sales = $this->reports->salesQuery($start, $end)->get();

        return Pdf::loadView('admin.reports.pdf.sales', [
            'sales' => $sales,
            'summary' => $summary,
            'start' => $start,
            'end' => $end,
            'pharmacy' => optional(auth()->user())->pharmacy,
        ])->download('sales-report-'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf');
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

        $summary = $this->reports->purchasesReport($start, $end);
        $purchases = $this->reports->purchasesQuery($start, $end)->get();

        return Pdf::loadView('admin.reports.pdf.purchases', [
            'purchases' => $purchases,
            'summary' => $summary,
            'start' => $start,
            'end' => $end,
            'pharmacy' => optional(auth()->user())->pharmacy,
        ])->download('purchases-report-'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf');
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

        $gst = $this->reports->gstReport($start, $end);

        return Pdf::loadView('admin.reports.pdf.gst', [
            'gst' => $gst,
            'start' => $start,
            'end' => $end,
            'pharmacy' => optional(auth()->user())->pharmacy,
        ])->download('gst-report-'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf');
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
