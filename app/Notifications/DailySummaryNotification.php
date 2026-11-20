<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailySummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $summary) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $s = $this->summary;

        $mail = (new MailMessage)
            ->subject("Resumen Gerencial del Día ({$s['date']}) - VetPets ERP")
            ->greeting("Estimado(a) {$notifiable->name},")
            ->line("Ventas del día: {$s['sales_count']} transacciones facturadas por $".number_format($s['sales_total'], 2).'.')
            ->line('Compras registradas hoy: $'.number_format($s['purchases_total'], 2).'.')
            ->line("Órdenes de compra pendientes de recibir: {$s['pending_purchases']}.");

        if (! empty($s['low_stock'])) {
            $mail->line('Productos en stock crítico: '.implode(', ', $s['low_stock']).'.');
        }

        return $mail->action('Abrir Dashboard de Operaciones', route('dashboard'))
            ->line('Reporte generado automáticamente por la tarea programada del ERP.');
    }
}
