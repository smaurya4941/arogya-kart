<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Sent to a user when a queued PDF report has finished generating.
 * Stored in the notifications table and displayed in the navbar bell.
 *
 * The notification data includes a `filename` key that the download
 * route uses to serve the file from storage/app/exports/.
 */
class ReportReadyNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $title,
        public readonly string $filename,
        public readonly string $period,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'     => 'report_ready',
            'title'    => $this->title,
            'filename' => $this->filename,
            'period'   => $this->period,
            'message'  => "{$this->title} for {$this->period} is ready to download.",
            'url'      => route('admin.reports.download', ['filename' => $this->filename]),
            'icon'     => 'document-download',
        ];
    }
}
