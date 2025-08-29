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

        // Set data scope for admin and sellers
        if ($user->hasRole('admin')) {
            // Admin can see their own data + their sellers' data
            $allowedUserIds = collect([$user->id]);
            
            // Check if users table has created_by column before querying
            if (\Schema::hasColumn('users', 'created_by')) {
                $sellers = \App\Models\User::where('created_by', $user->id)
                    ->whereHas('roles', function($q) {
                        $q->where('name', 'seller');
                    })->pluck('id');
                $allowedUserIds = $allowedUserIds->merge($sellers);
            }
            
            session(['data_scope_user_ids' => $allowedUserIds->toArray()]);
        } elseif ($user->hasRole('seller')) {
            // Seller can only see their own data
            session(['data_scope_user_ids' => [$user->id]]);
        }

        return $next($request);
    }
}