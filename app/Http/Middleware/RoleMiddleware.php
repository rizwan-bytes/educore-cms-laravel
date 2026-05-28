<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (! $user->isActive()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', __('auth.account_inactive'));
        }

        if (! in_array($user->role, $roles)) {
            abort(403, __('auth.unauthorized'));
        }

        return $next($request);
    }
}
