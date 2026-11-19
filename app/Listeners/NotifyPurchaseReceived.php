<?php

namespace App\Listeners;

use App\Events\PurchaseReceived;
use App\Notifications\PurchaseReceivedNotification;

class NotifyPurchaseReceived
{
    public function handle(PurchaseReceived $event): void
    {
        $event->purchase->user?->notify(
            new PurchaseReceivedNotification($event->purchase, $event->complete)
        );
    }
}