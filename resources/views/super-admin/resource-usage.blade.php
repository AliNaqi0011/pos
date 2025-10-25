@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="mdi mdi-chart-pie"></i> Resource Usage Analytics</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>{{ $usage['storage'] }}</h3><p>Storage Used</p></div></div></div>
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ $usage['bandwidth'] }}</h3><p>Bandwidth</p></div></div></div>
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>{{ number_format($usage['api_calls']) }}</h3><p>API Calls</p></div></div></div>
                <div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body text-center"><h3>{{ $usage['database_size'] }}</h3><p>Database Size</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection