<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name_display,
            'cpf_cnpj' => $this->cpf_cnpj,
            'email' => $this->email,
            'phone' => $this->phone,
            'cellphone' => $this->cellphone,
            'address' => [
                'street' => $this->address,
                'number' => $this->number,
                'complement' => $this->complement,
                'neighborhood' => $this->neighborhood,
                'city' => $this->city,
                'state' => $this->state,
                'zip_code' => $this->zip_code,
            ],
            'contacts' => $this->contacts,
        ];
    }
}
