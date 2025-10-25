@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0"><i class="mdi mdi-account-settings"></i> Role Distribution</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Role</th><th>Count</th><th>Percentage</th></tr></thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr><td>{{ ucfirst($role->role) }}</td><td>{{ $role->count }}</td><td>{{ number_format(($role->count / $roles->sum('count')) * 100, 1) }}%</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection