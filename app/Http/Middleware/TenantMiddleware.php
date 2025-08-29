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
            
            // Set tenant context based on user's organization/company
            if ($user->tenant_id) {
                config(['database.connections.tenant.database' => 'tenant_' . $user->tenant_id]);
            }
        }

        return $next($request);
    }
}