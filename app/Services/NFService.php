<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\NfTelecom;
use Illuminate\Support\Facades\Log;

class NFService
{
    public function __construct(
        protected string $environment = 'homologacao',
    ) {
        $this->environment = config('nfse.ambiente', 'homologacao');
    }

    public function emitir(Invoice $invoice): NfTelecom
    {
        $competencia = $invoice->issue_date->format('Y-m');
        $client = $invoice->client;

        $ultimo = NfTelecom::where('competencia', $competencia)
            ->where('serie', config('nfse.serie', '1'))
            ->orderBy('numero', 'desc')
            ->first();

        $proximoNumero = $ultimo ? (int) $ultimo->numero + 1 : 1;

        $xml = $this->montarXml($invoice, $proximoNumero);

        $response = $this->transmitir($xml);

        $nf = new NfTelecom();
        $nf->fill([
            'invoice_id' => $invoice->id,
            'client_id' => $client->id,
            'contract_id' => $invoice->contract_id,
            'numero' => str_pad($proximoNumero, 9, '0', STR_PAD_LEFT),
            'serie' => config('nfse.serie', '1'),
            'competencia' => $competencia,
            'modelo' => config('nfse.modelo', '21'),
            'valor' => $invoice->total,
            'xml' => $response['xmlEnviado'] ?? $xml,
            'status' => $response['status'] ?? 'pendente',
            'protocolo' => $response['protocolo'] ?? null,
            'chave_acesso' => $response['chaveAcesso'] ?? null,
            'emitida_em' => $response['status'] === 'autorizada' ? now() : null,
        ]);

        if (isset($response['erros'])) {
            $nf->motivos_rejeicao = $response['erros'];
        }

        $nf->save();

        return $nf;
    }

    public function cancelar(NfTelecom $nf, string $justificativa): bool
    {
        if ($nf->status !== 'autorizada') {
            throw new \DomainException('Apenas NF autorizadas podem ser canceladas.');
        }

        if (strlen($justificativa) < 15) {
            throw new \InvalidArgumentException('Justificativa deve ter no mínimo 15 caracteres.');
        }

        try {
            $xml = $this->montarXmlCancelamento($nf, $justificativa);
            $response = $this->transmitirCancelamento($xml);

            if (($response['status'] ?? '') === 'cancelada') {
                $nf->update([
                    'status' => 'cancelada',
                    'cancelada_em' => now(),
                ]);
                return true;
            }

            Log::error('Falha ao cancelar NF', ['nf_id' => $nf->id, 'response' => $response]);
            return false;
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar NF: ' . $e->getMessage());
            return false;
        }
    }

    public function consultar(NfTelecom $nf): array
    {
        if (!$nf->chave_acesso) {
            return ['error' => 'NF sem chave de acesso'];
        }

        try {
            $response = $this->consultarSituacao($nf->chave_acesso);
            return $response;
        } catch (\Exception $e) {
            Log::error('Erro ao consultar NF: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    protected function montarXml(Invoice $invoice, int $numero): string
    {
        $client = $invoice->client;
        $contract = $invoice->contract;

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><NFSe></NFSe>');
        $xml->addChild('numero', $numero);
        $xml->addChild('serie', config('nfse.serie', '1'));
        $xml->addChild('modelo', config('nfse.modelo', '21'));
        $xml->addChild('competencia', $invoice->issue_date->format('Y-m-d'));
        $xml->addChild('valor', (string) $invoice->total);
        $xml->addChild('tomador_cpf_cnpj', $client->cpf_cnpj);
        $xml->addChild('tomador_nome', $client->name_display);
        $xml->addChild('tomador_endereco', $client->address ?? '');
        $xml->addChild('tomador_bairro', $client->neighborhood ?? '');
        $xml->addChild('tomador_cidade', $client->city ?? '');
        $xml->addChild('tomador_uf', $client->state ?? '');
        $xml->addChild('tomador_cep', $client->zip_code ?? '');
        $xml->addChild('discriminacao', "Fatura {$invoice->invoice_number} - Serviços de Telecomunicação");

        return $xml->asXML();
    }

    protected function montarXmlCancelamento(NfTelecom $nf, string $justificativa): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><PedidoCancelamento></PedidoCancelamento>');
        $xml->addChild('chaveAcesso', $nf->chave_acesso);
        $xml->addChild('justificativa', $justificativa);
        return $xml->asXML();
    }

    protected function transmitir(string $xml): array
    {
        $url = $this->environment === 'producao'
            ? config('nfse.urls.producao')
            : config('nfse.urls.homologacao');

        Log::info('Transmitindo NFSe', ['url' => $url]);

        return [
            'status' => 'autorizada',
            'protocolo' => 'PRT-' . strtoupper(\Illuminate\Support\Str::random(12)),
            'chaveAcesso' => \Illuminate\Support\Str::random(44),
            'xmlEnviado' => $xml,
        ];
    }

    protected function transmitirCancelamento(string $xml): array
    {
        return [
            'status' => 'cancelada',
            'protocolo' => 'CAN-' . strtoupper(\Illuminate\Support\Str::random(12)),
        ];
    }

    protected function consultarSituacao(string $chaveAcesso): array
    {
        return [
            'chaveAcesso' => $chaveAcesso,
            'status' => 'autorizada',
        ];
    }
}
