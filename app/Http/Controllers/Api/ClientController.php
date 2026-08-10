<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $client = $request->user()->clients()->with(['contracts.plan', 'contracts.links'])->first();
        if (!$client) {
            return response()->json(['message' => 'Nenhum cliente vinculado.'], 404);
        }
        return response()->json($client);
    }

    public function update(Request $request): JsonResponse
    {
        $client = $request->user()->clients()->first();
        if (!$client) {
            return response()->json(['message' => 'Nenhum cliente vinculado.'], 404);
        }

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:20'],
            'cellphone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $client->update($validated);
        return response()->json($client);
    }
}
