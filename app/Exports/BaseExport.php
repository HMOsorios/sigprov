<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BaseExport
{
    public function toCsv(Collection $rows, array $headers, string $filename): StreamedResponse
    {
        $callback = function () use ($rows, $headers) {
            $fh = fopen('php://output', 'w');
            fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($fh, $headers, ';');

            foreach ($rows as $row) {
                $data = [];
                foreach ($headers as $key => $label) {
                    $data[] = $row[$key] ?? '';
                }
                fputcsv($fh, $data, ';');
            }

            fclose($fh);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
