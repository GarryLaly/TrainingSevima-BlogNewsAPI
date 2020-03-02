<?php

namespace App\Http\Middleware;

use Closure;
use Str;

class CheckPrivilege
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        $route =  $request->route()->getName();
        $menuPrivileges = $user->menuPrivileges;

        $accessibles = collect([
            // Primary routes
            Str::contains($route, 'dashboard'),

            // Secondary routes which usually contained inside primary routes
            Str::contains($route, 'store'),
            Str::contains($route, 'update'),
            Str::contains($route, 'datatables'),
            Str::contains($route, 'api'),
            Str::contains($route, 'deletePhoto'),
        ]);

        $hasAccess = $accessibles->contains(true);

        $isPermitted = !!$menuPrivileges->first(function($privilege) use ($route) {
            $isValidRoute = $privilege->menuAdmin->link === $route;
            $isAccessible = $privilege->can_access === 1;

            return $isValidRoute && $isAccessible;
        });

        if (!$hasAccess && !$isPermitted) {
            return abort(404);
        }

        return $next($request);
    }
}
