<?php

namespace App\Http\Controllers\Admin;

use App\Events\InvoiceGenerated;
use App\Events\PaymentConfirmed;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\AuditService;
use App\Services\BoletoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private AuditService $auditService,
        private BoletoService $boletoService,
    ) {}

    public function index(Request $request): View
    {
        $query = Invoice::with(['client']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $query->whereBetween('issue_date', [$startDate, $endDate]);

        $totalAmount = (clone $query)->sum('total');
        $totalPending = (clone $query)->whereIn('status', ['pending', 'overdue'])->sum('total');
        $totalPaid = (clone $query)->where('status', 'paid')->sum('total');

        $invoices = $query->orderBy('due_date', 'desc')->paginate(15);

        return view('admin.invoices.index', compact(
            'invoices', 'totalAmount', 'totalPending', 'totalPaid'
        ));
    }

    public function create(): View
    {
        $contracts = Contract::with('client')->where('status', 'active')->get();
        return view('admin.invoices.form', compact('contracts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contract_id' => ['required', 'exists:contracts,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $contract = Contract::findOrFail($validated['contract_id']);

        $validated['client_id'] = $contract->client_id;
        $validated['invoice_number'] = 'INV-' . now()->format('Ymd') . '-' . str_pad(Invoice::max('id') + 1, 5, '0', STR_PAD_LEFT);
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['total'] = $validated['amount'] - $validated['discount'];
        $validated['created_by'] = auth()->id();

        $invoice = Invoice::create($validated);

        // Generate boleto/PIX
        $boletoData = $this->boletoService->generateBoleto($invoice);
        $pixData = $this->boletoService->generatePix($invoice);

        $invoice->update([
            'boleto_barcode' => $boletoData['barcode'],
            'boleto_url' => $boletoData['url'],
            'pix_code' => $pixData['code'],
            'pix_qrcode' => $pixData['qrcode'],
        ]);

        $this->auditService->logCreate('invoice', $invoice->id, "Fatura {$invoice->invoice_number} criada", $validated);

        InvoiceGenerated::dispatch($invoice);

        return $this->redirectWith('admin.invoices.index', 'Fatura criada com sucesso!');
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['client', 'contract.plan', 'payments']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        $contracts = Contract::with('client')->where('status', 'active')->get();
        return view('admin.invoices.form', compact('invoice', 'contracts'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'contract_id' => ['required', 'exists:contracts,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'status' => ['required', 'in:pending,overdue,paid,canceled,refunded'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'late_fee' => ['nullable', 'numeric', 'min:0'],
            'interest' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['late_fee'] = $validated['late_fee'] ?? 0;
        $validated['interest'] = $validated['interest'] ?? 0;
        $validated['total'] = $validated['amount'] - $validated['discount'] + $validated['late_fee'] + $validated['interest'];

        if ($validated['status'] === 'paid' && !$invoice->paid_date) {
            $validated['paid_date'] = now();
        }

        $oldValues = $invoice->toArray();
        $invoice->update($validated);

        $this->auditService->logUpdate('invoice', $invoice->id, "Fatura {$invoice->invoice_number} atualizada", $oldValues, $validated);

        return $this->redirectWith('admin.invoices.index', 'Fatura atualizada com sucesso!');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === 'paid') {
            return $this->error('Fatura paga não pode ser excluída.');
        }

        $invoice->payments()->delete();
        $invoice->delete();

        return $this->redirectWith('admin.invoices.index', 'Fatura excluída com sucesso!');
    }

    public function markAsPaid(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', 'in:boleto,pix,credit_card,debit_card,transfer,cash,other'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'gateway' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = $invoice->payments()->create([
            'payment_code' => 'PAY-' . now()->format('Ymd') . '-' . str_pad(Payment::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'method' => $validated['method'],
            'status' => 'confirmed',
            'amount' => $validated['amount'],
            'fee' => 0,
            'net_amount' => $validated['amount'],
            'gateway' => $validated['gateway'],
            'paid_at' => now(),
            'notes' => $validated['notes'],
            'confirmed_by' => auth()->id(),
        ]);

        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
            'total' => $validated['amount'],
        ]);

        $this->auditService->logCreate('payment', $payment->id, "Pagamento {$payment->payment_code} registrado para fatura {$invoice->invoice_number}", $validated);

        PaymentConfirmed::dispatch($invoice);

        return $this->redirectWith('admin.invoices.show', 'Pagamento registrado com sucesso!');
    }
}
