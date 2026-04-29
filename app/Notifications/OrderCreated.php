<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class OrderCreated extends Notification
{

    /**
     * Create a new notification instance.
     */
    protected $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', \App\Channels\FcmChannel::class];
    }

    /**
     * Get the array representation of the notification (For Database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $total = number_format($this->transaction->total_amount, 0, ',', '.');
        $itemCount = $this->transaction->items->count();
        $itemsStr = $this->transaction->items->take(2)->map(function ($item) {
            $qty = (float) $item->quantity;
            return $item->product->name . " (x{$qty})";
        })->implode(', ');
        
        if ($itemCount > 2) {
            $itemsStr .= ", +".($itemCount - 2)." lainnya";
        }

        return [
            'title' => 'Pesanan Baru Masuk 💰',
            'body' => "Rp {$total} - {$itemsStr}",
            'transaction_id' => $this->transaction->id,
            'type' => 'order_created',
        ];
    }
    
    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable)
    {
        $total = number_format($this->transaction->total_amount, 0, ',', '.');
        $itemCount = $this->transaction->items->count();
        // Summary items: "Kopi (x2), Roti (x1)..."
        $itemsStr = $this->transaction->items->take(2)->map(function ($item) {
            $qty = (float) $item->quantity;
            return $item->product->name . " (x{$qty})";
        })->implode(', ');
        
        if ($itemCount > 2) {
            $itemsStr .= ", +".($itemCount - 2)." lainnya";
        }

        return [
            'title' => 'Pesanan Baru: Rp ' . $total,
            'body' => $itemsStr,
            'data' => [
                'transaction_id' => (string) $this->transaction->id,
                'type' => 'order_created',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]
        ];
    }
}
