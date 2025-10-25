@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="mdi mdi-backup-restore"></i> Backup Status</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Type</th><th>Last Backup</th><th>Size</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($backups as $backup)
                        <tr><td>{{ $backup['type'] }}</td><td>{{ $backup['last_backup']->diffForHumans() }}</td><td>{{ $backup['size'] }}</td><td><span class="badge badge-success">{{ $backup['status'] }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection