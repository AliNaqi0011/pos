<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SaasController extends Controller
{
    public function tenantDashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            return redirect()->route('saas.setup');
        }

        $stats = [
            'tenant' => $tenant,
            'users' => $tenant->users()->count(),
            'subscription' => $tenant->subscriptionPlan,
            'isOnTrial' => $tenant->isOnTrial(),
            'subscriptionActive' => $tenant->subscriptionActive(),
            'trialDaysLeft' => $tenant->trial_ends_at ? $tenant->trial_ends_at->diffInDays(now()) : 0,
        ];

        return view('saas.tenant-dashboard', $stats);
    }

    public function setupTenant()
    {
        return view('saas.setup');
    }

    public function createTenant(Request $request)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'domain' => 'required|string|unique:tenants,domain',
            'plan_id' => 'required|exists:plans,id'
        ]);

        $user = Auth::user();
        
        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->tenant_name,
            'domain' => $request->domain,
            'database' => 'tenant_' . Str::random(8),
            'subscription_plan_id' => $request->plan_id,
            'trial_ends_at' => now()->addDays(14), // 14-day trial
            'status' => 'active'
        ]);

        // Assign user to tenant
        $user->update(['tenant_id' => $tenant->id]);
        
        // Assign super admin role if not already assigned
        if (!$user->hasRole('super_admin')) {
            $user->assignRole('admin');
        }

        return redirect()->route('saas.tenant-dashboard')
            ->with('success', 'Tenant created successfully! You have a 14-day free trial.');
    }

    public function manageUsers()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            return redirect()->route('saas.setup');
        }

        $users = $tenant->users()->with('roles')->paginate(10);
        
        return view('saas.manage-users', compact('users', 'tenant'));
    }

    public function inviteUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,seller,manager'
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        // Create new user
        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
            'tenant_id' => $tenant->id,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Assign role
        $newUser->assignRole($request->role);

        // Send invitation email (implement as needed)
        
        return redirect()->route('saas.manage-users')
            ->with('success', 'User invited successfully!');
    }

    public function subscriptionStatus()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            return redirect()->route('saas.setup');
        }

        $plans = Plan::where('active', true)->get();
        
        return view('saas.subscription-status', compact('tenant', 'plans'));
    }

    public function upgradeSubscription(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;
        $plan = Plan::findOrFail($request->plan_id);

        // Update tenant subscription
        $tenant->update([
            'subscription_plan_id' => $plan->id,
            'subscription_ends_at' => now()->addMonth(),
            'trial_ends_at' => null // End trial
        ]);

        return redirect()->route('saas.subscription-status')
            ->with('success', 'Subscription upgraded successfully!');
    }

    public function tenantSettings()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            return redirect()->route('saas.setup');
        }

        return view('saas.tenant-settings', compact('tenant'));
    }

    public function updateTenantSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'settings' => 'array'
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        $tenant->update([
            'name' => $request->name,
            'settings' => $request->settings ?? []
        ]);

        return redirect()->route('saas.tenant-settings')
            ->with('success', 'Settings updated successfully!');
    }
}