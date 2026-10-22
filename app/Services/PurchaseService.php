<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    /**
     * 1. Registrar una nueva orden de compra (Estado PENDIENTE, NO modifica stock).
     */
    public function register(array $data, int $userId): Purchase
    {
        return DB::transaction(function () use ($data, $userId) {
            $subtotal = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $lineSubtotal = $item['quantity'] * $item['unit_cost'];
                $subtotal += $lineSubtotal;

                $items[] = [
                    'product_id'        => $product->id,
                    'quantity'          => $item['quantity'],
                    'received_quantity' => 0,
                    'unit_cost'         => $item['unit_cost'],
                    'subtotal'          => $lineSubtotal,
                ];
            }

            $tax = round($subtotal * 0.19, 2); // IVA 19%
            $total = $subtotal + $tax;

            // Generar número consecutivo OC-00001
            $nextId = (Purchase::withTrashed()->max('id') ?? 0) + 1;
            $purchaseNumber = 'OC-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $purchase = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'provider_id'     => $data['provider_id'],
                'user_id'         => $userId,
                'purchase_date'   => $data['purchase_date'],
                'subtotal'        => $subtotal,
                'tax'             => $tax,
                'total'           => $total,
                'status'          => 'pendiente',
                'notes'           => $data['notes'] ?? null,
            ]);

            $purchase->details()->createMany($items);

            return $purchase;
        });
    }

    /**
     * 2. Registrar Recepción Física de Mercancía (Total o Parcial).
     * El stock AUMENTA únicamente en este paso.
     */
    public function receive(Purchase $purchase, array $receivedQuantities): Purchase
    {
        return DB::transaction(function () use ($purchase, $receivedQuantities) {
            if (in_array($purchase->status, ['recibida', 'anulada'])) {
                throw ValidationException::withMessages([
                    'error' => "Esta orden ya se encuentra {$purchase->status} y no admite más recepciones.",
                ]);
            }

            $totalUnitsReceivedThisBatch = 0;

            foreach ($purchase->details as $detail) {
                $qtyToReceive = (int) ($receivedQuantities[$detail->id] ?? 0);

                if ($qtyToReceive < 0) {
                    throw ValidationException::withMessages([
                        'error' => "La cantidad recibida no puede ser negativa.",
                    ]);
                }

                if ($qtyToReceive > 0) {
                    $pending = $detail->quantity - $detail->received_quantity;

                    // Regla obligatoria 1: No recibir más de lo pedido
                    if ($qtyToReceive > $pending) {
                        throw ValidationException::withMessages([
                            'error' => "No se puede recibir más de lo pedido para el producto: {$detail->product->name}. Pendientes: {$pending}.",
                        ]);
                    }

                    // Aumentar cantidad recibida acumulada en la línea
                    $detail->increment('received_quantity', $qtyToReceive);

                    // Aumentar stock físico en la bodega
                    $detail->product->increment('current_stock', $qtyToReceive);

                    $totalUnitsReceivedThisBatch += $qtyToReceive;
                }
            }

            // Regla obligatoria 2: La recepción debe traer al menos una unidad
            if ($totalUnitsReceivedThisBatch === 0) {
                throw ValidationException::withMessages([
                    'error' => "Debe ingresar al menos una unidad recibida para procesar la entrega.",
                ]);
            }

            // Determinar si la orden quedó completa (recibida) o parcial
            $purchase->refresh();
            $allCompleted = $purchase->details->every(fn($d) => $d->received_quantity >= $d->quantity);

            $purchase->update([
                'status'      => $allCompleted ? 'recibida' : 'parcial',
                'received_at' => $allCompleted ? now() : $purchase->received_at,
            ]);

            return $purchase;
        });
    }

    /**
     * 3. Anular Orden de Compra.
     * Regla obligatoria 3: Solo se puede anular si NO tiene mercancía recibida (estado pendiente).
     */
    public function cancel(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            if ($purchase->status !== 'pendiente') {
                throw ValidationException::withMessages([
                    'error' => "Solo se pueden anular órdenes pendientes sin mercancía recibida. Estado actual: {$purchase->status}.",
                ]);
            }

            $purchase->update(['status' => 'anulada']);
            $purchase->delete(); // Soft Delete
        });
    }
}
