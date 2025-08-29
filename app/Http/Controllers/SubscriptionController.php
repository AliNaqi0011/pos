<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $plans = Plan::where('active', true)->get();
        $currentSubscription = $user->subscription('default');
        
        return view('subscriptions.index', compact('plans', 'currentSubscription'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|string'
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        try {
            $user->newSubscription('default', $plan->stripe_plan_id)
                ->create($request->payment_method);

            // Update tenant subscription
            if ($user->tenant) {
                $user->tenant->update([
                    'subscription_plan_id' => $plan->id,
                    'subscription_ends_at' => now()->addMonth()
                ]);
            }

            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscription created successfully!');

        } catch (IncompletePayment $exception) {
            return redirect()->route('cashier.payment', [$exception->payment->id]);
        }
    }

    public function cancel()
    {
        $user = Auth::user();
        $user->subscription('default')->cancel();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription cancelled successfully!');
    }

    public function resume()
    {
        $user = Auth::user();
        $user->subscription('default')->resume();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription resumed successfully!');
    }

    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        $user->subscription('default')->swap($plan->stripe_plan_id);

        // Update tenant subscription
        if ($user->tenant) {
            $user->tenant->update([
                'subscription_plan_id' => $plan->id
            ]);
        }

        return redirect()->route('subscriptions.index')
            ->with('success', 'Plan changed successfully!');
    }

    public function invoices()
    {
        $user = Auth::user();
        $invoices = $user->invoices();

        return view('subscriptions.invoices', compact('invoices'));
    }

    public function downloadInvoice($invoiceId)
    {
        $user = Auth::user();
        return $user->downloadInvoice($invoiceId, [
            'vendor' => config('app.name'),
            'product' => 'Subscription',
        ]);
    }
}