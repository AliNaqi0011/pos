@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>SaaS Tenant Dashboard</h4>
            </div>
            <div class="card-body">
                @if(isset($tenant))
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Tenant Information</h5>
                            <p><strong>Name:</strong> {{ $tenant->name }}</p>
                            <p><strong>Domain:</strong> {{ $tenant->domain }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge badge-{{ $tenant->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </p>
                            <p><strong>Users:</strong> {{ $users ?? 0 }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Subscription Status</h5>
                            @if($isOnTrial)
                                <div class="alert alert-warning">
                                    <strong>Trial Period</strong><br>
                                    {{ $trialDaysLeft }} days remaining
                                </div>
                            @elseif($subscriptionActive)
                                <div class="alert alert-success">
                                    <strong>Active Subscription</strong><br>
                                    Plan: {{ $subscription->name ?? 'N/A' }}
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <strong>No Active Subscription</strong><br>
                                    Please upgrade to continue using the service.
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Quick Actions</h5>
                            <div class="btn-group" role="group">
                                <a href="{{ route('saas.manage-users') }}" class="btn btn-primary">Manage Users</a>
                                <a href="{{ route('saas.subscription-status') }}" class="btn btn-info">Subscription</a>
                                <a href="{{ route('saas.tenant-settings') }}" class="btn btn-secondary">Settings</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <h5>Welcome to SaaS Platform!</h5>
                        <p>You need to set up your tenant to get started.</p>
                        <a href="{{ route('saas.setup') }}" class="btn btn-primary">Setup Tenant</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection