@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h4 class="mb-0"><i class="mdi mdi-file-document-outline"></i> Audit Logs</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Action</th><th>User</th><th>Details</th><th>Time</th></tr></thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr><td>{{ $log['action'] }}</td><td>{{ $log['user'] }}</td><td>{{ $log['details'] }}</td><td>{{ $log['time']->diffForHumans() }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection