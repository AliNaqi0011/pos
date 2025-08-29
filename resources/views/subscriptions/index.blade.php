@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Subscription Management</h4>
            </div>
            <div class="card-body">
                @if($currentSubscription && $currentSubscription->active())
                    <div class="alert alert-success">
                        <h5>Current Subscription: {{ $currentSubscription->name }}</h5>
                        <p>Status: {{ $currentSubscription->stripe_status }}</p>
                        @if($currentSubscription->onTrial())
                            <p>Trial ends: {{ $currentSubscription->trial_ends_at->format('M d, Y') }}</p>
                        @endif
                        @if($currentSubscription->cancelled())
                            <p>Ends: {{ $currentSubscription->ends_at->format('M d, Y') }}</p>
                        @endif
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            @if($currentSubscription->cancelled())
                                <form action="{{ route('subscriptions.resume') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Resume Subscription</button>
                                </form>
                            @else
                                <form action="{{ route('subscriptions.cancel') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure?')">Cancel Subscription</button>
                                </form>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('subscriptions.invoices') }}" class="btn btn-info">View Invoices</a>
                        </div>
                    </div>
                @endif

                <h5>Available Plans</h5>
                <div class="row">
                    @foreach($plans as $plan)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header text-center">
                                <h5>{{ $plan->name }}</h5>
                                <h3>${{ number_format($plan->price, 2) }}<small>/month</small></h3>
                            </div>
                            <div class="card-body">
                                <p>{{ $plan->description }}</p>
                            </div>
                            <div class="card-footer text-center">
                                <button class="btn btn-primary">Subscribe</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection