<?php

namespace App\Notifications;

use App\Models\ProductBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Fired by SendStockAlerts for each batch that will expire within the
 * configured window (default: 30 days). Sent to the pharmacy's admin user
 * via the database channel so it appears in the notification bell.
 */
class ExpiryAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly ProductBatch $batch,
        public readonly int $daysUntilExpiry,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $productName = $this->batch->product?->name ?? "Batch #{$this->batch->id}";
        $expiryDate  = $this->batch->expiry_date->format(config('pharmacy.date_format', 'd M Y'));

        return [
            'type'           => 'expiry_alert',
            'product_id'     => $this->batch->product_id,
            'product_name'   => $productName,
            'batch_id'       => $this->batch->id,
            'batch_number'   => $this->batch->batch_number,
            'quantity'       => $this->batch->quantity,
            'expiry_date'    => $expiryDate,
            'days_remaining' => $this->daysUntilExpiry,
            'message'        => "Expiring soon: {$productName} (Batch {$this->batch->batch_number}) expires on {$expiryDate} — {$this->daysUntilExpiry} day(s) remaining, {$this->batch->quantity} units in stock.",
            'url'            => route('admin.products.show', $this->batch->product_id),
            'icon'           => 'clock',
        ];
    }
}
