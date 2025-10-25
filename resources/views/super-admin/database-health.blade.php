@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0"><i class="mdi mdi-database"></i> Database Health</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>{{ $health['connections'] }}</h3><p>Active Connections</p></div></div></div>
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>{{ $health['slow_queries'] }}</h3><p>Slow Queries</p></div></div></div>
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ $health['query_time'] }}ms</h3><p>Avg Query Time</p></div></div></div>
                <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>{{ $health['size'] }}</h3><p>Database Size</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection