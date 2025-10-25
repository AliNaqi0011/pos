@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="mdi mdi-file-document"></i> Access Logs</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>User</th><th>Action</th><th>IP Address</th><th>Time</th></tr></thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr><td>{{ $log['user'] }}</td><td>{{ $log['action'] }}</td><td>{{ $log['ip'] }}</td><td>{{ $log['time']->diffForHumans() }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection