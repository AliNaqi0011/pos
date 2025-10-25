@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0"><i class="mdi mdi-alert"></i> Security Alerts</h4>
        </div>
        <div class="card-body">
            @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['severity'] == 'high' ? 'danger' : ($alert['severity'] == 'medium' ? 'warning' : 'info') }}">
                <strong>{{ $alert['type'] }}</strong>: {{ $alert['count'] }} incidents detected
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection