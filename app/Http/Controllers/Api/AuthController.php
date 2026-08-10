<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $abilities = match ($user->role?->name) {
            'client' => ['client'],
            'technician' => ['tech'],
            default => ['admin', 'tech', 'client'],
        };

        $token = $user->createToken(
            $request->device_name ?? 'api',
            $abilities,
            now()->addDays(30),
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->name,
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Registro deve ser feito pelo administrador.'], 403);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Desconectado com sucesso.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role?->name,
            'created_at' => $user->created_at,
        ]);
    }
}
