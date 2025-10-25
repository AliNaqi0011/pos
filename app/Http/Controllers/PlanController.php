<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PlanController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        $plans = Plan::get();

        return view("admin.plans", compact("plans"));
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function show(Plan $plan, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }
        
        try {
            $intent = $user->createSetupIntent();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to process payment setup. Please try again.');
        }

        return view("admin.subscription", compact("plan", "intent"));
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function subscription(Request $request)
    {
        $request->validate([
            'plan' => 'required|exists:plans,id',
            'token' => 'required|string'
        ]);
        
        $plan = Plan::find($request->plan);
        if (!$plan || !$plan->stripe_plan) {
            return redirect()->back()->with('error', 'Invalid plan selected.');
        }
        
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }
        
        try {
            $subscription = $user->newSubscription($request->plan, $plan->stripe_plan)
                ->create($request->token);
        } catch (\Laravel\Cashier\Exceptions\IncompletePayment $e) {
            return redirect()->route('cashier.payment', [$e->payment->id, 'redirect' => route('blog.listing')]);
        } catch (\Stripe\Exception\CardException $e) {
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            \Log::error('Stripe invalid request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Invalid payment request.');
        } catch (\Exception $e) {
            \Log::error('Subscription error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Subscription failed. Please try again.');
        }

        return redirect()->route('blog.listing')->with('success', 'Subscription Done!');
    }
}
