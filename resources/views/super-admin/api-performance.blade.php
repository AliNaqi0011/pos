@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="mdi mdi-api"></i> API Performance</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ $performance['avg_response_time'] }}ms</h3><p>Avg Response Time</p></div></div></div>
                <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>{{ $performance['requests_per_second'] }}</h3><p>Requests/Second</p></div></div></div>
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>{{ $performance['error_rate'] }}%</h3><p>Error Rate</p></div></div></div>
                <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>{{ $performance['uptime'] }}%</h3><p>Uptime</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection