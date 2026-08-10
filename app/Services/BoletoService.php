<?php

namespace App\Services;

use App\Models\Invoice;

class BoletoService
{
    public function generateBoleto(Invoice $invoice): array
    {
        $barcode = $this->generateBarcode($invoice);
        return [
            'barcode' => $barcode,
            'barcode_digitable' => $this->formatDigitableLine($barcode),
            'url' => '#',
            'nosso_numero' => $invoice->invoice_number,
            'value' => $invoice->total,
            'due_date' => $invoice->due_date->format('Y-m-d'),
            'payer_name' => $invoice->client->company_name,
            'payer_document' => $invoice->client->cpf_cnpj,
        ];
    }

    public function generatePix(Invoice $invoice): array
    {
        $payload = $this->generatePixPayload($invoice);
        return [
            'code' => $payload,
            'qrcode' => 'data:image/png;base64,' . base64_encode($payload),
            'value' => $invoice->total,
        ];
    }

    private function generateBarcode(Invoice $invoice): string
    {
        $bank = '001';
        $currency = '9';
        $value = str_pad(number_format($invoice->total, 2, '', ''), 11, '0', STR_PAD_LEFT);
        $freeField = str_pad($invoice->id, 25, '0', STR_PAD_LEFT);
        $code = $bank . $currency . $freeField . $value;
        $dv = $this->calculateBarcodeDv($code);
        return $code . $dv;
    }

    private function calculateBarcodeDv(string $code): string
    {
        $weights = [4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < min(strlen($code), count($weights)); $i++) {
            $sum += intval($code[$i]) * $weights[$i];
        }
        $rest = $sum % 11;
        $dv = 11 - $rest;
        return $dv >= 10 ? '1' : (string) $dv;
    }

    private function formatDigitableLine(string $barcode): string
    {
        if (strlen($barcode) !== 44) {
            return $barcode;
        }
        return substr($barcode, 0, 5) . '.' . substr($barcode, 5, 5)
            . ' ' . substr($barcode, 10, 5) . '.' . substr($barcode, 15, 6)
            . ' ' . substr($barcode, 21, 5) . '.' . substr($barcode, 26, 6)
            . ' ' . substr($barcode, 32, 1)
            . ' ' . substr($barcode, 33, 14);
    }

    private function generatePixPayload(Invoice $invoice): string
    {
        $key = 'sisprov@sisprov.com.br';
        $merchantName = 'SisProv Telecom';
        $merchantCity = 'São Paulo';
        $value = number_format($invoice->total, 2, '.', '');
        $txid = $invoice->invoice_number;

        $payload = '00020126' . sprintf('%02d', strlen('BR.COM.SPI.PIX')) . 'BR.COM.SPI.PIX'
            . '01' . sprintf('%02d', strlen($key)) . $key
            . '0208' . sprintf('%02d', strlen($txid)) . $txid
            . '52040000'
            . '5303986'
            . '54' . sprintf('%02d', strlen($value)) . $value
            . '5802BR'
            . '59' . sprintf('%02d', strlen($merchantName)) . $merchantName
            . '60' . sprintf('%02d', strlen($merchantCity)) . $merchantCity
            . '62070503***'
            . '6304';

        $crc16 = $this->calculateCrc16($payload);
        return $payload . strtoupper(dechex($crc16));
    }

    private function calculateCrc16(string $data): int
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 1) {
                    $crc = ($crc >> 1) ^ 0x8408;
                } else {
                    $crc >>= 1;
                }
            }
        }
        return $crc ^ 0xFFFF;
    }
}
