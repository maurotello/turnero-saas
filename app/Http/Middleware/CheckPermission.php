<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect('login');
        }

        // Check company-level permission for the user's role
        if (!$user->company || !$user->company->hasPermission($user->role, $permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción.'], 403);
            }
            abort(403, 'No tienes permisos para realizar esta acción. Contacta al administrador del sistema.');
        }

        return $next($request);
    }
}
