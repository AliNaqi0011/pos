@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-purple text-white">
            <h4 class="mb-0"><i class="mdi mdi-account-group"></i> Global User Analytics</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>{{ number_format($analytics['total_users']) }}</h3><p>Total Users</p></div></div></div>
                <div class="col-md-4"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ number_format($analytics['active_users']) }}</h3><p>Active Users (30d)</p></div></div></div>
                <div class="col-md-4"><div class="card bg-info text-white"><div class="card-body text-center"><h3>{{ number_format(($analytics['active_users']/$analytics['total_users'])*100, 1) }}%</h3><p>Activity Rate</p></div></div></div>
            </div>
            <div class="mt-4">
                <h5>Role Distribution</h5>
                @foreach($analytics['role_distribution'] as $role => $count)
                <div class="d-flex justify-content-between mb-2"><span>{{ ucfirst($role) }}</span><span class="badge badge-primary">{{ $count }}</span></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection