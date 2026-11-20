<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('importar-productos') ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:5120', // Máximo 5MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Debe seleccionar un archivo para importar.',
            'file.mimes' => 'El archivo debe estar en formato Excel (.xlsx, .xls) o CSV (.csv).',
            'file.max' => 'El archivo no debe exceder los 5 MB de tamaño.',
        ];
    }
}
