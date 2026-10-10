<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para registrar una venta.
     */
    public function rules(): array
    {
        return [
            // Propietario / Cliente (debe existir en la tabla owners)
            'client_id' => [
                'required',
                Rule::exists('owners', 'id'),
            ],

            // Fecha de venta
            'sale_date' => ['required', 'date'],

            // Notas u observaciones opcionales
            'notes' => ['nullable', 'string', 'max:500'],

            // Detalle de la venta (debe llevar al menos 1 producto)
            'items' => ['required', 'array', 'min:1'],

            // Cada producto debe existir en la tabla products y no estar eliminado
            'items.*.product_id' => [
                'required',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],

            // Cantidad por producto
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Nombres personalizados para los atributos en las alertas de error.
     */
    public function attributes(): array
    {
        return [
            'client_id'          => 'cliente',
            'sale_date'          => 'fecha de venta',
            'notes'              => 'notas',
            'items'              => 'productos',
            'items.*.product_id' => 'producto',
            'items.*.quantity'   => 'cantidad',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Debe agregar al menos un producto a la venta.',
            'items.min'      => 'Debe agregar al menos un producto a la venta.',
        ];
    }
}
