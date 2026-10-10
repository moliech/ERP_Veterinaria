<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    /**
     * Registra una venta completa en la base de datos dentro de una transacción.
     * Descuenta automáticamente el stock de los productos.
     *
     * @param array $data Contiene 'client_id', 'sale_date', 'notes' e 'items' [[product_id, quantity], ...]
     * @param int $userId ID del usuario vendedor que registra la venta
     * @return Sale
     * @throws ValidationException
     */
    public function register(array $data, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $userId) {
            
            // 1. Agrupar cantidades por producto si se envió el mismo producto en varias filas
            $quantities = [];
            foreach ($data['items'] as $item) {
                $productId = $item['product_id'];
                $quantities[$productId] = ($quantities[$productId] ?? 0) + (int) $item['quantity'];
            }

            // 2. Validar disponibilidad de stock y calcular subtotales
            $itemsToProcess = [];
            $subtotal = 0;

            foreach ($quantities as $productId => $quantity) {
                // lockForUpdate() bloquea la fila en MySQL hasta terminar la transacción (evita concurrencia)
                $product = Product::lockForUpdate()->findOrFail($productId);

                // Regla de Negocio 1: Producto inactivo
                if (isset($product->active) && !$product->active) {
                    throw ValidationException::withMessages([
                        'items' => "El producto «{$product->name}» está inactivo y no se puede vender.",
                    ]);
                }

                // Regla de Negocio 2: Stock insuficiente
                if ($product->current_stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para «{$product->name}». Disponible: {$product->current_stock} unidades, Solicitado: {$quantity}.",
                    ]);
                }

                $lineSubtotal = $product->sale_price * $quantity;
                $subtotal += $lineSubtotal;

                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $product->sale_price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            // 3. Cálculo de IVA (19% en Colombia) y Total
            $tax = round($subtotal * 0.19, 2);
            $total = round($subtotal + $tax, 2);

            // 4. Crear la cabecera de la venta (Factura)
            $sale = Sale::create([
                'invoice_number' => $this->generateNextInvoiceNumber(),
                'client_id'      => $data['client_id'],
                'user_id'        => $userId,
                'sale_date'      => $data['sale_date'],
                'subtotal'       => $subtotal,
                'tax'            => $tax,
                'total'          => $total,
                'status'         => 'pagada',
                'notes'          => $data['notes'] ?? null,
            ]);

            // 5. Crear los detalles de la venta y descontar el inventario
            foreach ($itemsToProcess as $item) {
                $sale->details()->create([
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal'   => $item['subtotal'],
                ]);

                // Descuento automático de inventario
                $item['product']->decrement('current_stock', $item['quantity']);
            }

            return $sale;
        });
    }

    /**
     * Anula una venta y aplica la regla de negocio inversa:
     * Devuelve las cantidades vendidas al inventario de productos.
     *
     * @param Sale $sale
     * @return Sale
     */
    public function cancel(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            // Regla Inversa: Devolver stock a cada producto
            foreach ($sale->details as $detail) {
                if ($detail->product) {
                    $detail->product->increment('current_stock', $detail->quantity);
                }
            }

            // Cambiar estado a anulada y aplicar SoftDelete
            $sale->update(['status' => 'anulada']);
            $sale->delete();

            return $sale;
        });
    }

    /**
     * Genera el siguiente número secuencial de factura: FV-00001, FV-00002, etc.
     *
     * @return string
     */
    private function generateNextInvoiceNumber(): string
    {
        $lastSale = Sale::withTrashed()->orderByDesc('id')->first();
        $nextId = $lastSale ? $lastSale->id + 1 : 1;

        return 'FV-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
    }
}