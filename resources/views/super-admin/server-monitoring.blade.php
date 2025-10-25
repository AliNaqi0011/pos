@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="mdi mdi-server"></i> Server Monitoring</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($servers as $server)
                        <div class="col-md-4 mb-4">
                            <div class="card {{ $server['status'] == 'healthy' ? 'border-success' : 'border-warning' }}">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ $server['name'] }}</h6>
                                    <span class="badge badge-{{ $server['status'] == 'healthy' ? 'success' : 'warning' }}">
                                        {{ ucfirst($server['status']) }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small>CPU Usage</small>
                                            <small>{{ $server['cpu'] }}%</small>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $server['cpu'] > 70 ? 'danger' : ($server['cpu'] > 50 ? 'warning' : 'success') }}" 
                                                 style="width: {{ $server['cpu'] }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small>Memory Usage</small>
                                            <small>{{ $server['memory'] }}%</small>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $server['memory'] > 80 ? 'danger' : ($server['memory'] > 60 ? 'warning' : 'info') }}" 
                                                 style="width: {{ $server['memory'] }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button class="btn btn-sm btn-outline-primary">Details</button>
                                        <button class="btn btn-sm btn-outline-secondary">Logs</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">System Alerts</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <i class="mdi mdi-alert"></i> DB-01 memory usage is above 80%
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information"></i> Scheduled maintenance in 2 days
                                    </div>
                                    <div class="alert alert-success">
                                        <i class="mdi mdi-check"></i> All backups completed successfully
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Performance Metrics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <h4 class="text-success">99.9%</h4>
                                            <small>Uptime</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-info">150ms</h4>
                                            <small>Avg Response</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-primary">1.2K</h4>
                                            <small>Requests/min</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection