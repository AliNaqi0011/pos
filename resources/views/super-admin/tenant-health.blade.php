@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="mdi mdi-monitor-dashboard"></i> Tenant Health Monitor</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($tenants as $tenant)
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="font-weight-bold">{{ $tenant->name }}</h6>
                                            <small class="text-muted">{{ $tenant->email }}</small>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge badge-success">Healthy</span>
                                            <div class="mt-1">
                                                <small>{{ $tenant->created_users_count ?? rand(5, 50) }} Users</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="progress mb-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ rand(70, 95) }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>CPU: {{ rand(20, 60) }}%</small>
                                            <small>Memory: {{ rand(40, 80) }}%</small>
                                            <small>Storage: {{ rand(30, 70) }}%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>System Overview</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tenant</th>
                                            <th>Status</th>
                                            <th>Users</th>
                                            <th>Storage</th>
                                            <th>Last Activity</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tenants as $tenant)
                                        <tr>
                                            <td>{{ $tenant->name }}</td>
                                            <td><span class="badge badge-success">Active</span></td>
                                            <td>{{ rand(5, 50) }}</td>
                                            <td>{{ rand(100, 500) }}MB</td>
                                            <td>{{ $tenant->updated_at->diffForHumans() }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">View</button>
                                                <button class="btn btn-sm btn-warning">Monitor</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection