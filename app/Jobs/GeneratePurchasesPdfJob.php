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
 * Queued job: generate a Purchases PDF report and notify the requesting user.
 */
class GeneratePurchasesPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
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

        $user      = User::findOrFail($this->userId);
        $summary   = $reports->purchasesReport($start, $end);
        $purchases = $reports->purchasesQuery($start, $end)->get();
        $pharmacy  = $user->pharmacy;

        $pdf = Pdf::loadView('admin.reports.pdf.purchases', compact('purchases', 'summary', 'start', 'end', 'pharmacy'))
            ->setPaper(config('pharmacy.pdf_paper_size', 'A4'), config('pharmacy.pdf_paper_orientation', 'portrait'));

        $filename = 'purchases-report-' . $start->format('Ymd') . '-' . $end->format('Ymd') . '-' . uniqid() . '.pdf';
        $path     = "exports/{$filename}";

        Storage::disk('local')->makeDirectory('exports');
        Storage::disk('local')->put($path, $pdf->output());

        Log::channel('daily')->info("Purchases PDF generated: {$path}", ['user_id' => $this->userId]);

        $user->notify(new ReportReadyNotification(
            title: 'Purchases Report Ready',
            filename: $filename,
            period: $start->format('d M Y') . ' – ' . $end->format('d M Y'),
        ));
    }

    public function failed(\Throwable $e): void
    {
        Log::channel('daily')->error('GeneratePurchasesPdfJob failed', [
            'user_id' => $this->userId,
            'error'   => $e->getMessage(),
        ]);
    }
}
