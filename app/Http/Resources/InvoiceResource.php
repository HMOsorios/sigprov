<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'client' => new ClientResource($this->whenLoaded('client')),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'issue_date' => $this->issue_date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'paid_date' => $this->paid_date?->toDateString(),
            'amount' => (float) $this->amount,
            'discount' => (float) $this->discount,
            'late_fee' => (float) $this->late_fee,
            'interest' => (float) $this->interest,
            'total' => (float) $this->total,
            'total_formatted' => $this->total_formatted,
            'items' => $this->items,
            'boleto_barcode' => $this->boleto_barcode,
            'boleto_url' => $this->boleto_url,
            'pix_code' => $this->pix_code,
            'pix_qrcode' => $this->pix_qrcode,
            'payment_gateway' => $this->payment_gateway,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
