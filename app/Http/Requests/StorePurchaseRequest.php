<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_id'          => ['required', 'exists:providers,id'],
            'purchase_date'        => ['required', 'date'],
            'notes'                => ['nullable', 'string', 'max:500'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'exists:products,id', 'distinct'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'    => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function attributes(): array
    {
        return [
            'provider_id'          => 'proveedor',
            'purchase_date'        => 'fecha de la compra',
            'notes'                => 'observaciones',
            'items'                => 'detalle de productos',
            'items.*.product_id'   => 'producto',
            'items.*.quantity'     => 'cantidad',
            'items.*.unit_cost'    => 'costo unitario',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'            => 'Debe agregar al menos un producto a la orden de compra.',
            'items.min'                 => 'Debe agregar al menos un producto a la orden de compra.',
            'items.*.product_id.distinct' => 'No puede repetir el mismo producto en varias filas; incremente la cantidad.',
        ];
    }
}
