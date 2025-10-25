@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="mdi mdi-credit-card"></i> Payment Processing</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ $payments['successful_payments'] }}</h3><p>Successful Payments</p></div></div></div>
                <div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body text-center"><h3>{{ $payments['failed_payments'] }}</h3><p>Failed Payments</p></div></div></div>
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>{{ $payments['pending_payments'] }}</h3><p>Pending Payments</p></div></div></div>
                <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>${{ number_format($payments['total_revenue']) }}</h3><p>Total Revenue</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection