<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $clientIds = $user->clients->pluck('id');

        $invoices = Invoice::whereIn('client_id', $clientIds)
            ->with('contract.plan')
            ->latest()
            ->paginate(15);

        $totalPending = Invoice::whereIn('client_id', $clientIds)
            ->pending()->sum('total');

        $totalOverdue = Invoice::whereIn('client_id', $clientIds)
            ->overdue()->sum('total');

        return view('client.invoices.index', compact('invoices', 'totalPending', 'totalOverdue'));
    }

    public function show(Invoice $invoice): View
    {
        $user = Auth::user();
        $clientIds = $user->clients->pluck('id');

        if (!in_array($invoice->client_id, $clientIds->toArray())) {
            abort(403);
        }

        $invoice->load(['contract.plan', 'payments']);

        return view('client.invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $user = Auth::user();
        $clientIds = $user->clients->pluck('id');

        if (!in_array($invoice->client_id, $clientIds->toArray())) {
            abort(403);
        }

        if (!$invoice->boleto_url) {
            return back()->withErrors('Boleto não disponível para esta fatura.');
        }

        return redirect($invoice->boleto_url);
    }

    public function payments(): View
    {
        $user = Auth::user();
        $clientIds = $user->clients->pluck('id');

        $invoices = Invoice::whereIn('client_id', $clientIds)
            ->where('status', 'paid')
            ->with('contract.plan')
            ->latest()
            ->paginate(20);

        return view('client.invoices.payments', compact('invoices'));
    }
}
