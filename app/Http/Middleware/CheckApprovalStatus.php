<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApprovalStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }

        // Super admin always has access
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Check if user is approved
        if ($user->status !== 'approved') {
            if ($user->status === 'pending') {
                return redirect()->route('approval.pending.message');
            } elseif ($user->status === 'rejected') {
                return redirect()->route('approval.rejected.message');
            }
        }

        // For admin, also check payment verification
        if ($user->hasRole('admin') && !$user->payment_verified) {
            return redirect()->route('payment.verification.pending');
        }

        return $next($request);
    }
}