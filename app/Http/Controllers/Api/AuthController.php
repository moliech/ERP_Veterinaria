<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/v1/login → entrega un token Bearer con la habilidad del rol activo.
     * Si el usuario tiene varios roles, debe indicar con cuál trabajará ("role").
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
            'role'        => ['nullable', 'string'],
        ]);

        $deviceName = $data['device_name'] ?? 'dispositivo-api';

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Credenciales incorrectas.']);
        }

        if (! $user->active) {
            throw ValidationException::withMessages(['email' => 'Su usuario está inactivo.']);
        }

        $roles = $user->getRoleNames();
        $role = $data['role'] ?? ($roles->count() === 1 ? $roles->first() : null);

        if ($roles->count() > 1 && ! $role) {
            throw ValidationException::withMessages([
                'role' => 'Su usuario tiene varios roles. Indique con cuál desea trabajar: '.$roles->join(', ').'.',
            ]);
        }

        if ($role && ! $roles->contains($role)) {
            throw ValidationException::withMessages(['role' => "El rol «{$role}» no está asignado a su usuario."]);
        }

        // El rol activo viaja DENTRO del token como una «ability»: rol:vendedor
        $token = $user->createToken($deviceName, $role ? ["rol:{$role}"] : []);

        return response()->json([
            'token'      => $token->plainTextToken,
            'token_type' => 'Bearer',
            'rol_activo' => $role,
        ], 201);
    }

    /**
     * GET /api/v1/me → perfil, rol activo y permisos efectivos
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $role = $user->activeRole();

        // Permisos efectivos = permisos del rol activo + permisos directos
        $permissions = ($role?->permissions->pluck('name') ?? collect())
            ->merge($user->getDirectPermissions()->pluck('name'))
            ->unique()->sort()->values();

        return response()->json([
            'id'         => $user->id,
            'nombre'     => $user->name,
            'email'      => $user->email,
            'rol_activo' => $role?->name,
            'permisos'   => $permissions,
        ]);
    }

    /**
     * POST /api/v1/logout → revoca el token con el que se autenticó la petición
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada. El token fue revocado.']);
    }
}
