<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminRedirectMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            // Super Admin should not access store operations
            $restrictedRoutes = [
                'products.*', 'categories.*', 'brands.*', 'warehouses.*',
                'customers.*', 'sales.*', 'purchases.*', 'pos.*',
                'expenses.*', 'quotations.*', 'barcodes.*'
            ];
            
            foreach ($restrictedRoutes as $pattern) {
                if ($request->routeIs($pattern)) {
                    return redirect()->route('admin.tenants.index')
                        ->with('error', 'Super Admin manages system, not store operations. Please access tenant management.');
                }
            }
        }
        
        return $next($request);
    }
}