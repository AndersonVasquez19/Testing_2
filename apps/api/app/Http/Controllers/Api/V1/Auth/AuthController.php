<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\AuditarAccionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(LoginRequest $request, AuditarAccionAction $auditar): JsonResponse
    {
        /** @var User|null $user */
        $user = User::query()->where('email', strtolower($request->string('email')->toString()))->first();

        if ($user === null || ! Hash::check($request->string('password')->toString(), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no son válidas.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Tu cuenta se encuentra desactivada.'], 403);
        }

        $token = $user->createToken('horus-web')->plainTextToken;

        $auditar->execute($user, 'auth_login', $user, ['role' => $user->role], $request);

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function register(RegisterRequest $request, AuditarAccionAction $auditar): JsonResponse
    {
        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => strtolower($request->string('email')->toString()),
            'dni' => $request->filled('dni') ? $request->string('dni')->toString() : null,
            'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
            'password' => $request->string('password')->toString(),
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
        ]);

        if (Role::query()->where('name', User::ROLE_STUDENT)->exists()) {
            $user->syncRoles([User::ROLE_STUDENT]);
        }

        $token = $user->createToken('horus-web')->plainTextToken;

        $auditar->execute($user, 'auth_register', $user, ['role' => User::ROLE_STUDENT], $request);

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user instanceof User) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
}
