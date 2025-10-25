@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="mdi mdi-chart-bar"></i> Billing Reports</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>${{ number_format($reports['monthly_revenue']) }}</h3><p>Monthly Revenue</p></div></div></div>
                <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>{{ $reports['quarterly_growth'] }}%</h3><p>Quarterly Growth</p></div></div></div>
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>${{ number_format($reports['annual_projection']) }}</h3><p>Annual Projection</p></div></div></div>
                <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>${{ $reports['average_revenue_per_user'] }}</h3><p>ARPU</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection