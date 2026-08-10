<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $client = auth()->user()->clients()->first();
        return view('client.profile.edit', compact('client'));
    }

    public function update(Request $request): RedirectResponse
    {
        $client = auth()->user()->clients()->first();
        if (!$client) {
            return back()->withErrors('Cliente não encontrado.');
        }

        $validated = $request->validate([
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'zipcode' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:200',
            'address_number' => 'nullable|string|max:10',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
        ]);

        $client->update($validated);

        return back()->with('success', 'Dados atualizados com sucesso.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Senha alterada com sucesso.');
    }

    public function notifications(): View
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('client.profile.notifications', compact('notifications'));
    }

    public function markNotification(Notification $notification): RedirectResponse
    {
        $notification->markAsRead();
        return back();
    }

    public function markAllNotifications(): RedirectResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'Todas as notificações marcadas como lidas.');
    }

    public function contracts(): View
    {
        $user = auth()->user();
        $client = $user->clients()->first();
        $contracts = $client?->contracts()->with('plan')->latest()->get() ?? collect();

        return view('client.profile.contracts', compact('contracts'));
    }

    public function contractPdf(Contract $contract)
    {
        $user = auth()->user();
        $client = $user->clients()->first();

        if (!$client || $contract->client_id !== $client->id) {
            abort(403);
        }

        if (!$contract->signed_pdf_path || !Storage::disk('local')->exists($contract->signed_pdf_path)) {
            return back()->withErrors('Contrato não disponível para download.');
        }

        return Storage::disk('local')->download(
            $contract->signed_pdf_path,
            "contrato_{$contract->contract_number}.pdf"
        );
    }

    public function cancelRequest(Contract $contract): View
    {
        $user = auth()->user();
        $client = $user->clients()->first();

        if (!$client || $contract->client_id !== $client->id) {
            abort(403);
        }

        return view('client.profile.cancel', compact('contract'));
    }

    public function submitCancelRequest(Request $request, Contract $contract): RedirectResponse
    {
        $user = auth()->user();
        $client = $user->clients()->first();

        if (!$client || $contract->client_id !== $client->id) {
            abort(403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $contract->update([
            'notes' => ($contract->notes ? $contract->notes . "\n---\n" : '') .
                "[" . now()->format('d/m/Y H:i') . "] Solicitação de cancelamento: {$validated['reason']}",
        ]);

        return redirect()->route('client.contracts')
            ->with('warning', 'Solicitação de cancelamento registrada. Entraremos em contato.');
    }
}
