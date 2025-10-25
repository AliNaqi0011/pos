@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0"><i class="mdi mdi-account-remove"></i> Churn Analysis</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><div class="card bg-danger text-white"><div class="card-body text-center"><h2>{{ $churn['monthly_churn'] }}%</h2><p>Monthly Churn Rate</p></div></div></div>
                <div class="col-md-8">
                    <h5>Churn Reasons</h5>
                    @foreach($churn['reasons'] as $reason)
                    <div class="progress mb-2"><div class="progress-bar" style="width: {{ rand(10, 40) }}%">{{ $reason }}</div></div>
                    @endforeach
                </div>
            </div>
            <div class="mt-4">
                <h5>At-Risk Tenants</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Tenant</th><th>Risk Level</th><th>Last Activity</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($churn['at_risk_tenants'] as $tenant)
                            <tr><td>{{ $tenant->name }}</td><td><span class="badge badge-warning">Medium</span></td><td>{{ $tenant->updated_at->diffForHumans() }}</td><td><button class="btn btn-sm btn-primary">Contact</button></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection