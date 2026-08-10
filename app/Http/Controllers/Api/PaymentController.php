<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PaymentGatewayManager;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $request, Invoice $invoice, PaymentGatewayManager $gateway)
    {
        if ($invoice->client_id !== $request->user()->client?->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($invoice->paid_at) {
            return response()->json(['error' => 'Invoice already paid'], 422);
        }

        $validated = $request->validate([
            'method' => 'required|in:pix,boleto,credit_card',
            'card_data' => 'required_if:method,credit_card|array|nullable',
        ]);

        $method = $validated['method'];

        $result = match ($method) {
            'pix' => $gateway->createPix($invoice),
            'boleto' => $gateway->createBoleto($invoice),
            'credit_card' => $gateway->createCard($invoice, $validated['card_data'] ?? []),
        };

        if (isset($result['boleto_barcode']) || isset($result['boleto_url'])) {
            $invoice->update([
                'boleto_barcode' => $result['boleto_barcode'] ?? null,
                'boleto_url' => $result['boleto_url'] ?? null,
                'payment_gateway' => $result['gateway'],
                'payment_gateway_id' => $result['gateway_id'],
            ]);
        }

        if (isset($result['pix_code']) || isset($result['pix_qrcode'])) {
            $invoice->update([
                'pix_code' => $result['pix_code'] ?? null,
                'pix_qrcode' => $result['pix_qrcode'] ?? null,
                'payment_gateway' => $result['gateway'],
                'payment_gateway_id' => $result['gateway_id'],
            ]);
        }

        return response()->json($result);
    }

    public function status(Invoice $invoice, PaymentGatewayManager $gateway)
    {
        if (!$invoice->payment_gateway_id) {
            return response()->json(['status' => $invoice->status]);
        }

        $status = $gateway->getStatus($invoice->payment_gateway_id);

        return response()->json([
            'gateway' => $invoice->payment_gateway,
            'gateway_status' => $status,
            'local_status' => $invoice->status,
            'paid_at' => $invoice->paid_at,
        ]);
    }
}
