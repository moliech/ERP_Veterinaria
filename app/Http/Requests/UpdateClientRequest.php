<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Owner;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar la solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar un cliente/propietario.
     */
    public function rules(): array
    {
        $client = $this->route('client');
        $clientId = $client instanceof Owner ? $client->id : $client;

        return [
            'document_type'   => ['required', Rule::in(['CC', 'NIT', 'CE', 'PP'])],
            'document_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('owners', 'document_number')->ignore($clientId),
            ],
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
