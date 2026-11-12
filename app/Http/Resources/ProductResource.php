<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Define qué datos de un producto salen por la API y con qué nombres.
 * Estandariza la respuesta pública y desacopla la API del esquema interno.
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->name,
            'descripcion' => $this->description ?? null,
            'precio' => (float) ($this->sale_price ?? $this->price),
            'stock' => (int) $this->current_stock,
            'disponible' => (bool) ($this->active && $this->current_stock > 0),
            'categoria' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'nombre' => $this->category?->name,
            ]),
        ];
    }
}
