<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar la solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para crear un cliente/propietario.
     */
    public function rules(): array
    {
        return [
            'document_type'   => ['required', Rule::in(['CC', 'NIT', 'CE', 'PP'])],
            'document_number' => ['required', 'string', 'max:20', 'unique:owners,document_number'],
            'first_name'      => ['required', 'string', 'max:50'],
            'last_name'       => ['required', 'string', 'max:50'],
            'email'           => ['nullable', 'email', 'max:100'],
            'address'         => ['nullable', 'string', 'max:150'],
        ];
    }

    /**
     * Nombres legibles para los atributos en caso de error.
     */
    public function attributes(): array
    {
        return [
            'document_type'   => 'tipo de documento',
            'document_number' => 'número de documento',
            'first_name'      => 'nombres',
            'last_name'       => 'apellidos',
            'email'           => 'correo electrónico',
            'address'         => 'dirección',
        ];
    }
}
