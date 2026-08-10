<?php

namespace App\Services;

use App\Models\Contract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ContractSigningService
{
    protected string $provider;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->provider = config('services.signing.provider', 'local');
        $this->apiKey = config('services.signing.api_key', '');
        $this->baseUrl = config('services.signing.base_url', '');
    }

    public function generateContractPdf(Contract $contract): string
    {
        $contract->load(['client', 'plan']);

        $html = view('pdfs.contract', compact('contract'))->render();

        $path = "contracts/{$contract->id}/contrato_{$contract->contract_number}.html";
        Storage::disk('local')->put($path, $html);

        return $path;
    }

    public function sendForSigning(Contract $contract): array
    {
        $contract->load(['client', 'plan']);

        $pdfPath = $this->generateContractPdf($contract);

        return match ($this->provider) {
            'clicksign' => $this->sendClicksign($contract, $pdfPath),
            'zapsign' => $this->sendZapsign($contract, $pdfPath),
            default => $this->sendLocal($contract, $pdfPath),
        };
    }

    public function processWebhook(array $payload): ?Contract
    {
        return match ($this->provider) {
            'clicksign' => $this->processClicksignWebhook($payload),
            'zapsign' => $this->processZapsignWebhook($payload),
            default => $this->processLocalWebhook($payload),
        };
    }

    public function isProviderConfigured(): bool
    {
        if ($this->provider === 'local') {
            return true;
        }
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }

    protected function sendClicksign(Contract $contract, string $path): array
    {
        $client = $contract->client;

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/api/v1/documents", [
            'document' => [
                'path' => Storage::path($path),
                'deadline_delta' => 15,
                'signers' => [[
                    'email' => $client->email,
                    'full_name' => $client->fantasy_name ?? $client->company_name ?? $client->name_display,
                    'phone_number' => preg_replace('/\D/', '', $client->cellphone ?? $client->phone),
                ]],
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $contract->update([
                'signature_status' => 'sent',
                'signature_id' => $data['document']['key'] ?? null,
            ]);
            return ['status' => 'sent', 'signature_id' => $contract->signature_id];
        }

        Log::error('Clicksign error', ['response' => $response->body()]);
        return ['status' => 'error', 'message' => $response->body()];
    }

    protected function sendZapsign(Contract $contract, string $path): array
    {
        $client = $contract->client;

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post("{$this->baseUrl}/api/v1/documents", [
            'name' => "Contrato {$contract->contract_number}",
            'pdf' => Storage::path($path),
            'signers' => [[
                'name' => $client->fantasy_name ?? $client->company_name ?? $client->name_display,
                'email' => $client->email,
            ]],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $contract->update([
                'signature_status' => 'sent',
                'signature_id' => $data['id'] ?? null,
            ]);
            return ['status' => 'sent', 'signature_id' => $contract->signature_id];
        }

        Log::error('Zapsign error', ['response' => $response->body()]);
        return ['status' => 'error', 'message' => $response->body()];
    }

    protected function sendLocal(Contract $contract, string $path): array
    {
        $contract->update([
            'signature_status' => 'pending',
            'signed_pdf_path' => $path,
        ]);

        return ['status' => 'pending', 'pdf_path' => $path];
    }

    protected function processClicksignWebhook(array $payload): ?Contract
    {
        $documentKey = $payload['document']['key'] ?? null;
        $status = $payload['document']['status'] ?? null;

        if (!$documentKey) {
            return null;
        }

        $contract = Contract::where('signature_id', $documentKey)->first();
        if (!$contract) {
            return null;
        }

        if (in_array($status, ['signed', 'closed'])) {
            $contract->update(['signature_status' => 'signed', 'signed_at' => now()]);
        }

        return $contract;
    }

    protected function processZapsignWebhook(array $payload): ?Contract
    {
        $docId = $payload['id'] ?? null;
        $status = $payload['status'] ?? null;

        if (!$docId) {
            return null;
        }

        $contract = Contract::where('signature_id', $docId)->first();
        if (!$contract) {
            return null;
        }

        if ($status === 'signed') {
            $contract->update(['signature_status' => 'signed', 'signed_at' => now()]);
        }

        return $contract;
    }

    protected function processLocalWebhook(array $payload): ?Contract
    {
        $contractId = $payload['contract_id'] ?? null;
        if (!$contractId) {
            return null;
        }

        $contract = Contract::find($contractId);
        if (!$contract) {
            return null;
        }

        $contract->update(['signature_status' => 'signed', 'signed_at' => now()]);
        return $contract;
    }
}
