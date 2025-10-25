@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>📊 Customer Analytics</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>Total Customers</h5>
                                <h3>{{ $data['totalCustomers'] }}</h3>
                                <small>Real-time count</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>Active Members</h5>
                                <h3>{{ $data['activeMembers'] }}</h3>
                                <small>{{ $data['totalCustomers'] > 0 ? number_format(($data['activeMembers'] / $data['totalCustomers']) * 100, 1) : 0 }}% of total</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5>Avg. Purchase</h5>
                                <h3>${{ number_format($data['avgPurchase'], 2) }}</h3>
                                <small>From sales data</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5>Retention Rate</h5>
                                <h3>{{ number_format($data['retentionRate'], 1) }}%</h3>
                                <small>Calculated live</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Top Customers by Spending</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Total Spent</th>
                                        <th>Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data['topCustomers'] as $customer)
                                    <tr>
                                        <td>{{ $customer->name }}</td>
                                        <td>${{ number_format($customer->total_spent, 2) }}</td>
                                        <td>{{ $customer->total_orders }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No customer data available</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Customer Segments</h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>VIP Customers</span>
                                <span>145 (11.6%)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" style="width: 11.6%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Gold Members</span>
                                <span>450 (36.1%)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: 36.1%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Silver Members</span>
                                <span>650 (52.2%)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-secondary" style="width: 52.2%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection