<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fired by SendStockAlerts when a product's total stock falls at or below
 * its min_stock_alert threshold. Delivered to the pharmacy admin via the
 * database channel (displayed in the notification bell in the navbar).
 *
 * Implements ShouldQueue so it does not block the Artisan command — each
 * notification is pushed onto the database queue and processed by workers.
 */
class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Product $product,
        public readonly int $currentStock,
    ) {}

    /**
     * Deliver via the DB channel only. Add 'mail' here if SMTP is configured.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data stored in the notifications table.
     * Accessed via $notification->data['product_name'] etc.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'low_stock',
            'product_id'   => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock'=> $this->currentStock,
            'min_alert'    => $this->product->min_stock_alert,
            'message'      => "Low stock: {$this->product->name} has only {$this->currentStock} units left (minimum: {$this->product->min_stock_alert}).",
            'url'          => route('admin.products.show', $this->product),
            'icon'         => 'warning',
        ];
    }
}
