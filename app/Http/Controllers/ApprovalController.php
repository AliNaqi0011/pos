<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function pendingUsers()
    {
        $user = Auth::user();
        
        if ($user->hasRole('super_admin')) {
            $pendingUsers = User::where('status', 'pending')
                ->whereHas('roles', function($q) {
                    $q->where('name', 'admin');
                })->get();
        } elseif ($user->hasRole('admin')) {
            $pendingUsers = User::where('status', 'pending')
                ->whereHas('roles', function($q) {
                    $q->where('name', 'seller');
                })->get();
        } else {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        return view('admin.approvals.pending', compact('pendingUsers'));
    }

    public function approve(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        // Super admin can approve admins, admin can approve sellers
        if ($currentUser->hasRole('super_admin') && $user->hasRole('admin')) {
            $user->update([
                'status' => 'approved',
                'approved_by' => $currentUser->id,
                'approved_at' => now()
            ]);
        } elseif ($currentUser->hasRole('admin') && $user->hasRole('seller')) {
            $user->update([
                'status' => 'approved',
                'approved_by' => $currentUser->id,
                'approved_at' => now()
            ]);
        } else {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        return redirect()->back()->with('success', 'User approved successfully');
    }

    public function reject(Request $request, User $user)
    {
        $request->validate(['reason' => 'required|string']);
        
        $currentUser = Auth::user();
        
        if (($currentUser->hasRole('super_admin') && $user->hasRole('admin')) ||
            ($currentUser->hasRole('admin') && $user->hasRole('seller'))) {
            
            $user->update([
                'status' => 'rejected',
                'approved_by' => $currentUser->id,
                'approved_at' => now(),
                'rejection_reason' => $request->reason
            ]);
        }

        return redirect()->back()->with('success', 'User rejected');
    }

    public function verifyPayment(User $user)
    {
        if (!Auth::user()->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $user->update(['payment_verified' => true]);
        return redirect()->back()->with('success', 'Payment verified');
    }
}