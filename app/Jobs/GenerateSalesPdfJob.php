<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ReportReadyNotification;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Queued job: generate a Sales PDF report and notify the requesting user.
 *
 * Dispatched from ReportsController::salesPdf() so the HTTP response
 * returns immediately with a flash message. The worker picks this up,
 * generates the PDF using DomPDF, saves it to storage/app/exports/ and
 * sends a ReportReadyNotification to the user who requested it.
 */
class GenerateSalesPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Maximum attempts before the job is marked as failed. */
    public int $tries = 3;

    /** Timeout in seconds for DomPDF rendering on large datasets. */
    public int $timeout = 120;

    public function __construct(
        public readonly int    $userId,
        public readonly string $start,
        public readonly string $end,
    ) {}

    public function handle(ReportService $reports): void
    {
        $start = Carbon::parse($this->start)->startOfDay();
        $end   = Carbon::parse($this->end)->endOfDay();

        $user     = User::findOrFail($this->userId);
        $summary  = $reports->salesReport($start, $end);
        $sales    = $reports->salesQuery($start, $end)->get();
        $pharmacy = $user->pharmacy;

        $pdf = Pdf::loadView('admin.reports.pdf.sales', compact('sales', 'summary', 'start', 'end', 'pharmacy'))
            ->setPaper(config('pharmacy.pdf_paper_size', 'A4'), config('pharmacy.pdf_paper_orientation', 'portrait'));

        $filename  = 'sales-report-' . $start->format('Ymd') . '-' . $end->format('Ymd') . '-' . uniqid() . '.pdf';
        $directory = 'exports';
        $path      = "{$directory}/{$filename}";

        Storage::disk('local')->makeDirectory($directory);
        Storage::disk('local')->put($path, $pdf->output());

        Log::channel('daily')->info("Sales PDF generated: {$path}", ['user_id' => $this->userId]);

        $user->notify(new ReportReadyNotification(
            title: 'Sales Report Ready',
            filename: $filename,
            period: $start->format('d M Y') . ' – ' . $end->format('d M Y'),
        ));
    }

    public function failed(\Throwable $e): void
    {
        Log::channel('daily')->error('GenerateSalesPdfJob failed', [
            'user_id' => $this->userId,
            'error'   => $e->getMessage(),
        ]);
    }
}
