<?php

namespace App\Notifications;

use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Purchase $purchase, public bool $complete) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $purchase = $this->purchase->load(['provider', 'user', 'details.product']);

        // Generar el PDF oficial de la orden de compra en memoria
        $pdfView = view()->exists('pdf.purchase') ? 'pdf.purchase' : 'purchases.pdf';
        $pdf = Pdf::loadView($pdfView, ['purchase' => $purchase])
            ->setPaper('letter')
            ->output();

        $number = $purchase->purchase_number ?? "OC-{$purchase->id}";

        return (new MailMessage)
            ->subject("Mercancía recibida: {$number}")
            ->greeting("Hola, {$notifiable->name}")
            ->line($this->complete
                ? "La orden de compra {$number} de «{$purchase->provider?->name}» fue recibida COMPLETA."
                : "Se registró una recepción PARCIAL de la orden de compra {$number}.")
            ->action('Ver la orden de compra', route('purchases.show', $purchase))
            ->attachData($pdf, "{$number}.pdf", ['mime' => 'application/pdf']);
    }

    public function toArray(object $notifiable): array
    {
        $number = $this->purchase->purchase_number ?? "OC-{$this->purchase->id}";

        return [
            'title' => $this->complete ? 'Compra recibida completa' : 'Recepción parcial de compra',
            'message' => "Orden {$number}: ".($this->complete ? 'toda la mercancía ingresó a bodega.' : 'ingresó un despacho parcial.'),
            'url' => route('purchases.show', $this->purchase),
        ];
    }
}
