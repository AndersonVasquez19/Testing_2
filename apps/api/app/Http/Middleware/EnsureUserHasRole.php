<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Tu cuenta se encuentra desactivada.'], 403);
        }

        if ($roles !== [] && ! $user->hasPortalRole(...$roles)) {
            return response()->json(['message' => 'No tienes permisos para acceder a este módulo.'], 403);
        }

        return $next($request);
    }
}
