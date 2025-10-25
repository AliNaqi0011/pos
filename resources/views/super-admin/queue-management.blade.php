@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h4 class="mb-0"><i class="mdi mdi-format-list-bulleted"></i> Queue Management</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><div class="card bg-warning text-white"><div class="card-body text-center"><h3>{{ $queue['pending_jobs'] }}</h3><p>Pending Jobs</p></div></div></div>
                <div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body text-center"><h3>{{ $queue['failed_jobs'] }}</h3><p>Failed Jobs</p></div></div></div>
                <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ number_format($queue['processed_today']) }}</h3><p>Processed Today</p></div></div></div>
                <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3>{{ $queue['avg_processing_time'] }}s</h3><p>Avg Processing Time</p></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection