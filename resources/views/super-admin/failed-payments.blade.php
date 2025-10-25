@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0"><i class="mdi mdi-alert-circle"></i> Failed Payments</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Tenant</th><th>Amount</th><th>Reason</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($failed as $payment)
                        <tr><td>{{ $payment['tenant'] }}</td><td>{{ $payment['amount'] }}</td><td>{{ $payment['reason'] }}</td><td>{{ $payment['date']->format('M d, Y') }}</td><td><button class="btn btn-sm btn-primary">Retry</button></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection