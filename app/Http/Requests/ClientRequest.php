<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->route('client')?->id;

        return [
            'company_name' => ['required', 'string', 'max:200'],
            'fantasy_name' => ['nullable', 'string', 'max:200'],
            'cpf_cnpj' => ['required', 'string', 'max:18', 'unique:clients,cpf_cnpj,' . $clientId],
            'rg_ie' => ['nullable', 'string', 'max:20'],
            'person_type' => ['required', 'in:pf,pj'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'cellphone' => ['nullable', 'string', 'max:20'],
            'zipcode' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:200'],
            'address_number' => ['required', 'string', 'max:10'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:2'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'observations' => ['nullable', 'string'],
        ];
    }
}
