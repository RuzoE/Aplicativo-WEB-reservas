<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\AllowedEmailDomain;
use App\Rules\AlphaSpace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', new AlphaSpace(), 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', new AllowedEmailDomain(), 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised(),
            ],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $deviceName = $validated['device_name'] ?? 'postman';
        $abilities = $this->resolveAbilities($user, $deviceName);
        $expiresAt = $this->resolveTokenExpirationDate();
        $token = $user->createToken($deviceName, $abilities, $expiresAt)->plainTextToken;

        registrarAuditoria(
            'CREATE',
            'usuarios',
            $user->id,
            'Usuario API registrado correctamente para el usuario ID ' . $user->id,
            $user->id,
            ['skip_duplicate' => false]
        );

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado correctamente.',
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => $user->load('roles'),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', new AllowedEmailDomain()],
            'password' => ['required'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::with('roles')->where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            registrarAuditoria(
                'LOGIN_FAILED',
                'usuarios',
                $user?->id,
                'Intento de inicio de sesion API fallido para el correo ' . $validated['email'] . ' desde IP ' . $request->ip(),
                $user?->id,
                ['skip_duplicate' => false]
            );

            return response()->json([
                'success' => false,
                'message' => __('auth.failed'),
            ], 422);
        }

        $deviceName = $validated['device_name'] ?? 'postman';
        $abilities = $this->resolveAbilities($user, $deviceName);
        $expiresAt = $this->resolveTokenExpirationDate();

        $token = $user->createToken($deviceName, $abilities, $expiresAt)->plainTextToken;

        registrarAuditoria(
            'LOGIN',
            'usuarios',
            $user->id,
            'Inicio de sesion API exitoso para el usuario ID ' . $user->id,
            $user->id,
            ['skip_duplicate' => false]
        );

        return response()->json([
            'success' => true,
            'message' => 'Autenticación exitosa.',
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()->load('roles'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión API cerrada correctamente.',
        ]);
    }

    private function resolveAbilities(User $user, string $deviceName): array
    {
        $abilities = ['profile:read'];

        if ($user->hasRole('administrador')) {
            return ['*'];
        }

        if ($user->hasRole('minibar')) {
            $abilities[] = 'minibar:write';
        }

        if ($user->hasAnyRole(['reservas', 'recepcion'])) {
            $abilities[] = 'reception:write';
        }

        if ($user->hasRole('mantenimiento')) {
            $abilities[] = 'maintenance:write';
        }

        if (str_contains(strtolower($deviceName), 'mobile')) {
            $abilities[] = 'mobile:client';
        }

        return array_values(array_unique($abilities));
    }

    private function resolveTokenExpirationDate(): ?Carbon
    {
        $expirationMinutes = (int) config('sanctum.expiration');
        if ($expirationMinutes <= 0) {
            return null;
        }

        return now()->addMinutes($expirationMinutes);
    }
}
