<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route (or route group) to one or more roles.
 * Usage: ->middleware('role:admin')  or  ->middleware('role:admin,staff')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, 403);
        abort_unless(in_array($user->role, $roles, true), 403, 'You do not have access to this area.');

        return $next($request);
    }
}
