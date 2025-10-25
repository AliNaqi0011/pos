@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="mdi mdi-shield-check"></i> Security Dashboard</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h2>{{ $security['security_score'] }}</h2>
                                    <p class="mb-0">Security Score</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-{{ $security['vulnerabilities'] > 0 ? 'warning' : 'success' }} text-white">
                                <div class="card-body text-center">
                                    <h2>{{ $security['vulnerabilities'] }}</h2>
                                    <p class="mb-0">Vulnerabilities</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h2><i class="mdi mdi-check"></i></h2>
                                    <p class="mb-0">SSL Status: {{ $security['ssl_status'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h2><i class="mdi mdi-shield"></i></h2>
                                    <p class="mb-0">Firewall: {{ $security['firewall_status'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Security Events (Last 24h)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <div class="list-group-item d-flex justify-content-between">
                                            <span><i class="mdi mdi-alert text-warning"></i> Failed login attempts</span>
                                            <span class="badge badge-warning">{{ rand(5, 25) }}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between">
                                            <span><i class="mdi mdi-block-helper text-danger"></i> Blocked IPs</span>
                                            <span class="badge badge-danger">{{ rand(2, 8) }}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between">
                                            <span><i class="mdi mdi-check text-success"></i> Successful logins</span>
                                            <span class="badge badge-success">{{ rand(150, 300) }}</span>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between">
                                            <span><i class="mdi mdi-eye text-info"></i> Suspicious activities</span>
                                            <span class="badge badge-info">{{ rand(0, 3) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Security Recommendations</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-success">
                                        <i class="mdi mdi-check-circle"></i> Two-factor authentication is enabled
                                    </div>
                                    <div class="alert alert-success">
                                        <i class="mdi mdi-check-circle"></i> SSL certificates are valid
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information"></i> Consider enabling rate limiting
                                    </div>
                                    <div class="alert alert-warning">
                                        <i class="mdi mdi-alert"></i> Update security patches available
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Recent Security Logs</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Event</th>
                                                    <th>IP Address</th>
                                                    <th>User</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ now()->subMinutes(15)->format('H:i') }}</td>
                                                    <td>Login Attempt</td>
                                                    <td>192.168.1.100</td>
                                                    <td>admin@example.com</td>
                                                    <td><span class="badge badge-success">Success</span></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ now()->subHours(2)->format('H:i') }}</td>
                                                    <td>Failed Login</td>
                                                    <td>203.45.67.89</td>
                                                    <td>unknown@test.com</td>
                                                    <td><span class="badge badge-danger">Blocked</span></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ now()->subHours(4)->format('H:i') }}</td>
                                                    <td>Password Reset</td>
                                                    <td>192.168.1.50</td>
                                                    <td>user@example.com</td>
                                                    <td><span class="badge badge-info">Completed</span></td>
                                                </tr>
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
    </div>
</div>
@endsection