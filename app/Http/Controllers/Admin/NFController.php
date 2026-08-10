<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\NfTelecom;
use App\Services\NFService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NFController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,developer');
    }

    public function index(Request $request): View
    {
        $query = NfTelecom::with(['client', 'invoice']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('competencia')) {
            $query->where('competencia', $request->competencia);
        }

        $nfs = $query->latest()->paginate(15);

        return view('admin.nf.index', compact('nfs'));
    }

    public function show(NfTelecom $nf): View
    {
        $nf->load(['client', 'invoice', 'contract']);
        return view('admin.nf.show', compact('nf'));
    }

    public function emitir(Invoice $invoice, NFService $service): RedirectResponse
    {
        if ($invoice->nfTelecom()->exists()) {
            return back()->withErrors('Esta fatura já possui NF emitida.');
        }

        try {
            $nf = $service->emitir($invoice);

            if ($nf->status === 'autorizada') {
                return redirect()->route('admin.nf.show', $nf)
                    ->with('success', 'NF emitida com sucesso!');
            }

            return redirect()->route('admin.nf.show', $nf)
                ->with('warning', 'NF emitida com status: ' . $nf->status_label);
        } catch (\Exception $e) {
            return back()->withErrors('Erro ao emitir NF: ' . $e->getMessage());
        }
    }

    public function cancelar(Request $request, NfTelecom $nf, NFService $service): RedirectResponse
    {
        $validated = $request->validate([
            'justificativa' => 'required|string|min:15',
        ]);

        try {
            $cancelado = $service->cancelar($nf, $validated['justificativa']);

            if ($cancelado) {
                return redirect()->route('admin.nf.show', $nf)
                    ->with('success', 'NF cancelada com sucesso!');
            }

            return back()->withErrors('Falha ao cancelar NF. Verifique os logs.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function consultar(NfTelecom $nf, NFService $service): RedirectResponse
    {
        $situacao = $service->consultar($nf);

        if (isset($situacao['error'])) {
            return back()->withErrors($situacao['error']);
        }

        return back()->with('success', 'Situação: ' . ($situacao['status'] ?? 'desconhecida'));
    }

    public function createForInvoice(Invoice $invoice): View
    {
        return view('admin.nf.create', compact('invoice'));
    }
}
