@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0"><i class="mdi mdi-bullhorn"></i> System Announcements</h4>
        </div>
        <div class="card-body">
            @foreach($announcements as $announcement)
            <div class="alert alert-{{ $announcement['type'] == 'maintenance' ? 'warning' : ($announcement['type'] == 'security' ? 'danger' : 'info') }}">
                <h6>{{ $announcement['title'] }}</h6>
                <small>Scheduled for: {{ $announcement['date']->format('M d, Y') }}</small>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection