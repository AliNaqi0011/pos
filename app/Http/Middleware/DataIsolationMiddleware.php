<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataIsolationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }

        // Super admin can see all data
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Set tenant context
        if ($user->tenant_id) {
            config(['app.current_tenant_id' => $user->tenant_id]);
        }

        // Set data scope for admin and sellers
        if ($user->hasRole('admin')) {
            $allowedUserIds = collect([$user->id]);
            
            // Safely check for created_by column and get sellers
            try {
                if (\Schema::hasColumn('users', 'created_by')) {
                    $sellers = \App\Models\User::where('created_by', $user->id)
                        ->where('tenant_id', $user->tenant_id)
                        ->whereHas('roles', function($q) {
                            $q->where('name', 'seller');
                        })->pluck('id');
                    $allowedUserIds = $allowedUserIds->merge($sellers);
                }
            } catch (\Exception $e) {
                \Log::warning('Data isolation query failed', ['error' => $e->getMessage()]);
            }
            
            session(['data_scope_user_ids' => $allowedUserIds->toArray()]);
        } elseif ($user->hasRole('seller') || $user->hasRole('manager') || $user->hasRole('sales')) {
            // Seller should see products created by their admin (created_by) and themselves
            $allowedIds = [$user->id];
            if ($user->created_by) {
                $allowedIds[] = $user->created_by;
            }
            session(['data_scope_user_ids' => $allowedIds]);
        }

        return $next($request);
    }
}