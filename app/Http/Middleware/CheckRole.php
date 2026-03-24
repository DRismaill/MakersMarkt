<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user's role is in the allowed roles
        if (in_array(auth()->user()->role->value, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access');
    }
}
