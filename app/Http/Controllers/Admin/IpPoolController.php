<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpAssignment;
use App\Models\IpPool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IpPoolController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:developer,admin,technician');
    }

    public function index(Request $request): View
    {
        $query = IpPool::withCount(['activeAssignments']);

        $pools = $query->latest()->paginate(20);

        return view('admin.ip_pools.index', compact('pools'));
    }

    public function create(): View
    {
        return view('admin.ip_pools.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'subnet' => 'required|string|max:45',
            'gateway' => 'nullable|string|max:45',
            'dns1' => 'nullable|string|max:45',
            'dns2' => 'nullable|string|max:45',
            'range_start' => 'required|string|max:45',
            'range_end' => 'required|string|max:45',
            'type' => 'required|in:ipv4,ipv6',
            'is_cgnat' => 'nullable|boolean',
            'server_id' => 'nullable|exists:servers,id',
            'notes' => 'nullable|string',
        ]);

        $start = ip2long($validated['range_start']);
        $end = ip2long($validated['range_end']);
        $validated['total'] = ($end - $start) + 1;
        $validated['used'] = 0;
        $validated['is_cgnat'] = $request->boolean('is_cgnat');

        IpPool::create($validated);

        return redirect()->route('admin.ip-pools.index')
            ->with('success', 'Pool de IP criado com sucesso.');
    }

    public function show(IpPool $ipPool): View
    {
        $ipPool->load(['activeAssignments.link.contract.client']);
        return view('admin.ip_pools.show', compact('ipPool'));
    }

    public function edit(IpPool $ipPool): View
    {
        return view('admin.ip_pools.edit', compact('ipPool'));
    }

    public function update(Request $request, IpPool $ipPool): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'gateway' => 'nullable|string|max:45',
            'dns1' => 'nullable|string|max:45',
            'dns2' => 'nullable|string|max:45',
            'range_start' => 'required|string|max:45',
            'range_end' => 'required|string|max:45',
            'type' => 'required|in:ipv4,ipv6',
            'is_cgnat' => 'nullable|boolean',
            'server_id' => 'nullable|exists:servers,id',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $start = ip2long($validated['range_start']);
        $end = ip2long($validated['range_end']);
        $validated['total'] = ($end - $start) + 1;
        $validated['is_cgnat'] = $request->boolean('is_cgnat');

        $ipPool->update($validated);

        return redirect()->route('admin.ip-pools.index')
            ->with('success', 'Pool de IP atualizado com sucesso.');
    }

    public function destroy(IpPool $ipPool): RedirectResponse
    {
        if ($ipPool->activeAssignments()->count() > 0) {
            return back()->withErrors('Pool possui IPs alocados e não pode ser excluído.');
        }

        $ipPool->delete();

        return redirect()->route('admin.ip-pools.index')
            ->with('success', 'Pool de IP removido com sucesso.');
    }
}
