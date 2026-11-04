<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdjustmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'counted_stock' => ['required', 'integer', 'min:0'],
            'reason'        => ['required', 'string', 'min:10', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'counted_stock.required' => 'Digite la cantidad contada en bodega.',
            'counted_stock.integer'  => 'La cantidad contada debe ser un número entero.',
            'counted_stock.min'      => 'La cantidad contada no puede ser negativa.',
            'reason.required'        => 'El motivo del ajuste es obligatorio.',
            'reason.min'             => 'El motivo del ajuste debe tener al menos 10 caracteres.',
            'reason.max'             => 'El motivo del ajuste no puede superar los 255 caracteres.',
        ];
    }
}