@extends('layouts.main')

@section('content')
<style>
    .card-3d {
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: white;
        border: none;
    }
    .card-3d:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">Super Admin Dashboard</h2>
    <span class="badge badge-success">Super Admin Panel</span>
</div>

<!-- Super Admin Statistics Cards - Only User Management -->
<div class="row">
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-primary">
            <div class="card-body">
                <h4 class="card-title">Total Users</h4>
                <h2 id="totalUsers">{{ $totalUsers ?? 0 }}</h2>
                <small>All system users</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-warning">
            <div class="card-body">
                <h4 class="card-title">Total Admins</h4>
                <h2 id="totalAdmins">{{ $totalAdmins ?? 0 }}</h2>
                <small>System administrators</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-info">
            <div class="card-body">
                <h4 class="card-title">Pending Approvals</h4>
                <h2 id="pendingApprovals">{{ $pendingApprovals ?? 0 }}</h2>
                <small>Awaiting approval</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-success">
            <div class="card-body">
                <h4 class="card-title">Active Tenants</h4>
                <h2 id="activeTenants">{{ $activeTenants ?? 0 }}</h2>
                <small>SaaS tenants</small>
            </div>
        </div>
    </div>
</div>

<!-- User Management Overview -->
<div class="row mt-4">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">User Registration Trends</h4>
                <canvas id="userChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Recent Users</h4>
                <div class="list-group">
                    @if(isset($recentUsers))
                        @foreach($recentUsers as $user)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $user->name }}</strong>
                                <br><small class="text-muted">{{ $user->email }}</small>
                            </div>
                            <span class="badge badge-primary">{{ $user->getRoleNames()->first() ?? 'User' }}</span>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Approvals Table -->
<div class="row mt-4">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Pending User Approvals</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registration Date</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($pendingUsers))
                            @foreach($pendingUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge badge-info">{{ $user->getRoleNames()->first() ?? 'User' }}</span></td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($user->payment_verified)
                                        <span class="badge badge-success">Verified</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.approvals.pending') }}" class="btn btn-sm btn-primary">Review</a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">No pending approvals</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // User Registration Chart
    const ctx = document.getElementById('userChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(75, 192, 192, 0.8)');
    gradient.addColorStop(1, 'rgba(75, 192, 192, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($userChart['dates'] ?? []),
            datasets: [{
                label: 'New Users',
                data: @json($userChart['userData'] ?? []),
                fill: true,
                backgroundColor: gradient,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 3,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: { color: '#333', font: { size: 14, weight: 'bold' } }
                }
            },
            scales: {
                x: { ticks: { color: '#666' }, grid: { display: false } },
                y: { ticks: { color: '#666' }, grid: { color: 'rgba(0,0,0,0.05)' } }
            }
        }
    });
});
</script>
@endpush