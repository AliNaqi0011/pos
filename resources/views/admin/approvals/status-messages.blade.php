@extends('layouts.main')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        @if(request()->routeIs('approval.pending.message'))
                            <div class="mb-4">
                                <i class="typcn typcn-time text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-warning">Account Pending Approval</h3>
                            <p class="lead">Your account is currently under review.</p>
                            <p>
                                @if(auth()->user()->hasRole('admin'))
                                    The super admin will review your payment and approve your account soon.
                                @else
                                    Your admin will review and approve your account soon.
                                @endif
                            </p>
                        @elseif(request()->routeIs('approval.rejected.message'))
                            <div class="mb-4">
                                <i class="typcn typcn-times text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-danger">Account Rejected</h3>
                            <p class="lead">Your account has been rejected.</p>
                            @if(auth()->user()->rejection_reason)
                                <div class="alert alert-danger">
                                    <strong>Reason:</strong> {{ auth()->user()->rejection_reason }}
                                </div>
                            @endif
                        @elseif(request()->routeIs('payment.verification.pending'))
                            <div class="mb-4">
                                <i class="typcn typcn-credit-card text-info" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-info">Payment Verification Pending</h3>
                            <p class="lead">Your payment is being verified by the super admin.</p>
                            <p>Once verified, you'll have full access to the system.</p>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('logout') }}" class="btn btn-outline-secondary"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection