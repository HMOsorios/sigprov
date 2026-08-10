<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentGatewayManager;
use App\Events\PaymentConfirmed;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __invoke(Request $request, PaymentGatewayManager $gateway)
    {
        $gatewayName = $request->route('gateway');

        try {
            $driver = $gateway->driver($gatewayName);
        } catch (\Exception $e) {
            Log::warning("Webhook called for unknown gateway: {$gatewayName}");
            return response()->json(['error' => 'Gateway not supported'], 400);
        }

        $data = $driver->processWebhook($request->all());

        if ($data['ignored'] ?? false) {
            return response()->json(['status' => 'ignored']);
        }

        $invoiceId = $data['invoice_id'] ?? null;
        if (!$invoiceId) {
            Log::warning('Webhook without invoice reference', $data);
            return response()->json(['status' => 'no_invoice']);
        }

        $invoice = Invoice::find($invoiceId);
        if (!$invoice) {
            Log::warning("Webhook for non-existent invoice: {$invoiceId}");
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $status = $data['status'] ?? 'pending';

        if ($status === 'paid' && !$invoice->paid_at) {
            $invoice->update([
                'paid_at' => $data['paid_at'] ?? now(),
                'paid_amount' => $data['amount'] ?? $invoice->total,
                'payment_method' => $data['method'] ?? $invoice->payment_method,
                'payment_gateway' => $data['gateway'] ?? $gatewayName,
                'payment_gateway_id' => $data['gateway_id'] ?? null,
                'status' => 'paid',
            ]);

            event(new PaymentConfirmed($invoice));
        }

        if (in_array($status, ['canceled', 'refunded', 'failed']) && !$invoice->paid_at) {
            $invoice->update(['status' => $status]);
        }

        return response()->json(['status' => 'processed']);
    }
}
