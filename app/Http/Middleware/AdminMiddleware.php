<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // funciona com session e tokens

        if (!$user || method_exists($user, 'isAdmin') === false || !$user->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
