@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>⭐ Customer Points</h4>
                <button class="btn btn-success btn-sm float-right">Add Points</button>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search customer...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Current Points</th>
                                <th>Total Earned</th>
                                <th>Total Redeemed</th>
                                <th>Tier</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                            @php
                                $points = floor($customer->total_spent / 10); // 1 point per $10 spent
                                $tier = $customer->total_spent > 5000 ? 'VIP' : ($customer->total_spent > 2000 ? 'Gold' : 'Silver');
                                $tierClass = $tier == 'VIP' ? 'danger' : ($tier == 'Gold' ? 'warning' : 'secondary');
                            @endphp
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td><span class="badge badge-primary">{{ number_format($points) }}</span></td>
                                <td>{{ number_format($points) }}</td>
                                <td>0</td>
                                <td><span class="badge badge-{{ $tierClass }}">{{ $tier }}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-success">Add Points</button>
                                    <button class="btn btn-sm btn-info">View History</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No customer data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection