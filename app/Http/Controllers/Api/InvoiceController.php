<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with('client');

        if ($request->user()->role?->name === 'client') {
            $clientIds = $request->user()->clients()->pluck('clients.id');
            $query->whereIn('client_id', $clientIds);
        }

        $invoices = $query->orderBy('due_date', 'desc')->paginate(15);
        return response()->json($invoices);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['client', 'contract.plan', 'payments']);
        return response()->json($invoice);
    }
}
