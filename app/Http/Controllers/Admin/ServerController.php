<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class ServerController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): View
    {
        $query = Server::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('hostname', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $servers = $query->orderBy('name')->paginate(15);

        return view('admin.servers.index', compact('servers'));
    }

    public function create(): View
    {
        return view('admin.servers.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'hostname' => ['required', 'string', 'max:200'],
            'ip_address' => ['required', 'string', 'max:45'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'type' => ['required', 'in:router,switch,server,firewall,nas,radius,dhcp,dns,other'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'firmware_version' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:200'],
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_monitored' => ['boolean'],
        ]);

        if (!empty($validated['password'])) {
            $validated['encrypted_password'] = Crypt::encryptString($validated['password']);
        }
        unset($validated['password']);

        $validated['created_by'] = auth()->id();
        $validated['is_monitored'] = $request->boolean('is_monitored', true);

        $server = Server::create($validated);

        $this->auditService->logCreate('server', $server->id, "Servidor {$server->name} criado", $validated);

        return $this->redirectWith('admin.servers.index', 'Servidor cadastrado com sucesso!');
    }

    public function show(Server $server): View
    {
        $server->load(['logs' => function ($q) {
            $q->latest()->limit(50);
        }]);

        return view('admin.servers.show', compact('server'));
    }

    public function edit(Server $server): View
    {
        return view('admin.servers.form', compact('server'));
    }

    public function update(Request $request, Server $server): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'hostname' => ['required', 'string', 'max:200'],
            'ip_address' => ['required', 'string', 'max:45'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'type' => ['required', 'in:router,switch,server,firewall,nas,radius,dhcp,dns,other'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'firmware_version' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:200'],
            'username' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:online,offline,maintenance,error'],
            'notes' => ['nullable', 'string'],
            'is_monitored' => ['boolean'],
        ]);

        if (!empty($validated['password'])) {
            $validated['encrypted_password'] = Crypt::encryptString($validated['password']);
        }
        unset($validated['password']);

        $validated['is_monitored'] = $request->boolean('is_monitored', true);

        $oldValues = $server->toArray();
        $server->update($validated);

        $this->auditService->logUpdate('server', $server->id, "Servidor {$server->name} atualizado", $oldValues, $validated);

        return $this->redirectWith('admin.servers.index', 'Servidor atualizado com sucesso!');
    }

    public function destroy(Server $server): RedirectResponse
    {
        $this->auditService->logDelete('server', $server->id, "Servidor {$server->name} excluído", $server->toArray());
        $server->delete();

        return $this->redirectWith('admin.servers.index', 'Servidor excluído com sucesso!');
    }

    public function ping(Server $server): RedirectResponse
    {
        $output = [];
        $returnCode = 0;
        exec("ping -n 1 {$server->ip_address}", $output, $returnCode);

        $isOnline = $returnCode === 0;
        $server->update([
            'status' => $isOnline ? 'online' : 'offline',
            'last_ping_at' => now(),
        ]);

        return $this->redirectWith('admin.servers.show', "Servidor {$server->name}: " . ($isOnline ? 'Online' : 'Offline'));
    }
}
