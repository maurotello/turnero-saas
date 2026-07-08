<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePatientAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('patient')->check()) {
            $slug = $request->route('slug');
            return redirect()->route('booking.login', $slug)
                ->with('error', 'Inicia sesión para continuar.');
        }

        return $next($request);
    }
}
