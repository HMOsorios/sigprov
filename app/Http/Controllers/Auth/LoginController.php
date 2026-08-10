<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Sua conta está desativada.']);
            }

            $this->auditService->logLogin($user->id, "Login realizado: {$user->email}");

            if ($user->two_factor_enabled) {
                session(['two_factor_required' => true]);
                return redirect()->route('two-factor.challenge');
            }

            return $this->redirectToDashboard($user);
        }

        throw ValidationException::withMessages([
            'email' => 'Credenciais inválidas.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            $this->auditService->logLogout($user->id, "Logout realizado: {$user->email}");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectToDashboard($user): RedirectResponse
    {
        return match ($user->role?->name) {
            'developer', 'admin', 'technician', 'administrativo' => redirect()->route('admin.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
