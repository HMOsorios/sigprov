<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): View
    {
        $query = User::with('role');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleId = $request->get('role_id')) {
            $query->where('role_id', $roleId);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.form', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'cpf_cnpj' => ['nullable', 'string', 'max:18', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);

        $this->auditService->logCreate('user', $user->id, "Usuário {$user->name} criado", $validated);

        return $this->redirectWith('admin.users.index', 'Usuário criado com sucesso!');
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('admin.users.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'cpf_cnpj' => ['nullable', 'string', 'max:18', 'unique:users,cpf_cnpj,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'password' => ['nullable', Rules\Password::defaults()],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $oldValues = $user->toArray();
        $user->update($validated);

        $this->auditService->logUpdate('user', $user->id, "Usuário {$user->name} atualizado", $oldValues, $validated);

        return $this->redirectWith('admin.users.index', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isAdmin() && User::where('role_id', $user->role_id)->count() <= 1) {
            return $this->error('Não é possível excluir o único administrador.');
        }

        if (auth()->id() === $user->id) {
            return $this->error('Você não pode excluir sua própria conta.');
        }

        $this->auditService->logDelete('user', $user->id, "Usuário {$user->name} excluído", $user->toArray());
        $user->delete();

        return $this->redirectWith('admin.users.index', 'Usuário excluído!');
    }
}
