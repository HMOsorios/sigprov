<?php

namespace App\Exports;

use App\Models\Client;

class ClientExport extends BaseExport
{
    public function export(array $filters = [])
    {
        $query = Client::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['person_type'])) {
            $query->where('person_type', $filters['person_type']);
        }

        $clients = $query->withCount(['activeContracts'])->latest()->get();

        $rows = $clients->map(fn($c) => [
            'id' => $c->id,
            'company_name' => $c->company_name,
            'fantasy_name' => $c->fantasy_name ?? '-',
            'cpf_cnpj' => $c->cpf_cnpj,
            'person_type' => $c->person_type === 'pf' ? 'PF' : 'PJ',
            'email' => $c->email,
            'phone' => $c->phone,
            'city' => $c->city,
            'state' => $c->state,
            'status' => $c->status,
            'active_contracts' => $c->active_contracts_count,
            'created_at' => $c->created_at->format('d/m/Y'),
        ]);

        $headers = [
            'id' => 'ID',
            'company_name' => 'Razão Social',
            'fantasy_name' => 'Fantasia',
            'cpf_cnpj' => 'CPF/CNPJ',
            'person_type' => 'Tipo',
            'email' => 'Email',
            'phone' => 'Telefone',
            'city' => 'Cidade',
            'state' => 'UF',
            'status' => 'Status',
            'active_contracts' => 'Contratos Ativos',
            'created_at' => 'Cadastro',
        ];

        return $this->toCsv($rows, $headers, 'clientes.csv');
    }
}
