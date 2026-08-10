<?php

namespace App\Services\Contracts;

use App\Models\Invoice;
use App\Models\NfTelecom;

interface NFServiceInterface
{
    public function emitir(Invoice $invoice): NfTelecom;
    public function cancelar(NfTelecom $nf, string $justificativa): bool;
    public function consultar(NfTelecom $nf): array;
    public function inutilizar(string $numeroInicial, string $numeroFinal, string $justificativa): bool;
}
