@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="mdi mdi-account-star"></i> Customer Metrics</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>${{ $metrics['cac'] }}</h3><p>Customer Acquisition Cost</p></div></div></div>
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>${{ $metrics['ltv'] }}</h3><p>Lifetime Value</p></div></div></div>
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>{{ $metrics['payback_period'] }} months</h3><p>Payback Period</p></div></div></div>
                <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>{{ $metrics['retention_rate'] }}%</h3><p>Retention Rate</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection