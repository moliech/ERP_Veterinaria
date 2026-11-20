<?php

namespace App\Listeners;

use App\Events\SaleRegistered;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;

class CheckLowStock
{
    public function handle(SaleRegistered $event): void
    {
        $threshold = config('erp.low_stock_threshold', 5);
        $event->sale->loadMissing('details.product');

        foreach ($event->sale->details as $detail) {
            $product = $detail->product?->fresh();
            if (! $product) {
                continue;
            }

            $before = $product->current_stock + $detail->quantity;

            // Se alerta únicamente si el producto cruza el umbral (evita repetir en cada venta)
            if ($before > $threshold && $product->current_stock <= $threshold) {
                Notification::send($this->recipients(), new LowStockNotification($product));
            }
        }
    }

    private function recipients()
    {
        return User::permission('crear-compras')->where('active', true)->get();
    }
}
