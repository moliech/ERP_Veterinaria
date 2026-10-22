<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Provider;

class UpdateProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $provider = $this->route('provider');
        $providerId = $provider instanceof Provider ? $provider->id : $provider;

        return [
            'nit'            => [
                'required',
                'string',
                'max:20',
                Rule::unique('providers', 'nit')->ignore($providerId),
            ],
            'company_name'   => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'email'          => ['nullable', 'email', 'max:100'],
            'address'        => ['nullable', 'string', 'max:200'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nit'            => 'NIT o documento fiscal',
            'company_name'   => 'razón social',
            'contact_person' => 'persona de contacto',
            'phone'          => 'teléfono',
            'email'          => 'correo electrónico',
            'address'        => 'dirección',
        ];
    }
}
