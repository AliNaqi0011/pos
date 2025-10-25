<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Set tenant context
            if ($user->tenant_id) {
                config(['app.current_tenant_id' => $user->tenant_id]);
            }
        }

        return $next($request);
    }
}