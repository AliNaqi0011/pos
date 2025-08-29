@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Customers Report</h4>
                    <div>
                        <a href="{{ route('reports.customers', ['export' => 'csv']) }}" class="btn btn-success btn-sm">
                            <i class="typcn typcn-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3>{{ $total_customers }}</h3>
                                <p>Total Customers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3>{{ $active_customers }}</h3>
                                <p>Active Customers</p>
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
                                <th>Phone</th>
                                <th>Total Orders</th>
                                <th>Total Spent</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->sales_count }}</td>
                                <td>${{ number_format($customer->sales_sum_final_total ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $customer->sales_count > 0 ? 'success' : 'secondary' }}">
                                        {{ $customer->sales_count > 0 ? 'Active' : 'Inactive' }}
                                    </span>
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
@endsection