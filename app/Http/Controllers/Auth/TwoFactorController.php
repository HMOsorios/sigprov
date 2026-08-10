<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function showChallenge(): View
    {
        return view('auth.two-factor');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $user = Auth::user();

        if (!$user || !$user->two_factor_secret) {
            return redirect()->route('login');
        }

        $secret = decrypt($user->two_factor_secret);
        $google2fa = app('pragmarx.google2fa');

        if ($google2fa->verifyKey($secret, $request->code)) {
            session(['two_factor_authenticated' => true]);
            session()->forget('two_factor_required');

            return match ($user->role?->name) {
                'developer', 'admin', 'technician', 'administrativo' => redirect()->route('admin.dashboard'),
                'client' => redirect()->route('client.dashboard'),
                default => redirect()->route('login'),
            };
        }

        return back()->withErrors(['code' => 'Código inválido.']);
    }

    public function showEnable(): View
    {
        return view('auth.two-factor-enable');
    }

    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'secret' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        if ($google2fa->verifyKey($request->secret, $request->code)) {
            $user->update([
                'two_factor_enabled' => true,
                'two_factor_secret' => encrypt($request->secret),
                'two_factor_recovery_codes' => $this->generateRecoveryCodes(),
            ]);

            return redirect()->route('admin.settings')->with('success', '2FA ativado com sucesso!');
        }

        return back()->withErrors(['code' => 'Código inválido.']);
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = Auth::user();
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return redirect()->route('admin.settings')->with('success', '2FA desativado.');
    }

    public function useRecoveryCode(Request $request): RedirectResponse
    {
        $request->validate(['recovery_code' => ['required', 'string']]);

        $user = Auth::user();
        $codes = $user->two_factor_recovery_codes ?? [];

        $index = array_search($request->recovery_code, $codes);
        if ($index !== false) {
            unset($codes[$index]);
            $user->update([
                'two_factor_recovery_codes' => array_values($codes),
                'two_factor_authenticated' => true,
            ]);

            session(['two_factor_authenticated' => true]);

            return match ($user->role?->name) {
                'developer', 'admin', 'technician', 'administrativo' => redirect()->route('admin.dashboard'),
                'client' => redirect()->route('client.dashboard'),
                default => redirect()->route('login'),
            };
        }

        return back()->withErrors(['recovery_code' => 'Código de recuperação inválido.']);
    }

    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        }
        return $codes;
    }
}
