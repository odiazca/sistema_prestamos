<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /** Solo el administrador accede al Asistente IA y al Chat IA. */
    public function handle(Request $request, Closure $next): Response
    {
        $u = $request->user();
        abort_unless($u && $u->esAdmin(), 403, 'Solo el administrador puede usar el asistente de IA.');

        return $next($request);
    }
}
