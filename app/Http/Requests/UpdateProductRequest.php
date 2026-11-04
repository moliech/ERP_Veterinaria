<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('sale_price') && $this->route('product')) {
            $this->merge(['sale_price' => $this->route('product')->sale_price]);
        }
        if (! $this->filled('cost_price') && $this->route('product')) {
            $this->merge(['cost_price' => $this->route('product')->cost_price]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'nullable|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'batch_code' => 'nullable|string|max:50',
            'expiration_date' => 'nullable|date',
        ];
    }
}
