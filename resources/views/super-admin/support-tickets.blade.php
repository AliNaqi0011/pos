@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="mdi mdi-ticket"></i> Support Tickets</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>{{ $tickets['open'] }}</h3><p>Open Tickets</p></div></div></div>
                <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>{{ $tickets['in_progress'] }}</h3><p>In Progress</p></div></div></div>
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ $tickets['resolved_today'] }}</h3><p>Resolved Today</p></div></div></div>
                <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>{{ $tickets['avg_response_time'] }}</h3><p>Avg Response Time</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection