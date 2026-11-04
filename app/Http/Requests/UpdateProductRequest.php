<?php

namespace App\Http\Requests;

class UpdateProductRequest extends StoreProductRequest
{
    /**
     * Clase 12: Al editar un producto NO se recibe stock. Solo muta por movimientos de Kardex.
     */
    public function rules(): array
    {
        return collect(parent::rules())->except('stock')->all();
    }
}