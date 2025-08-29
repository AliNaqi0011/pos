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
    <h2 class="text-warning">Seller Dashboard</h2>
    <span class="badge badge-warning">Seller Panel</span>
</div>

<!-- Seller Statistics Cards -->
<div class="row">
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-success">
            <div class="card-body">
                <h4 class="card-title">My Sales</h4>
                <h2 id="mySales">{{ $mySales ?? 0 }}</h2>
                <small>Total sales made</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-primary">
            <div class="card-body">
                <h4 class="card-title">My Revenue</h4>
                <h2 id="myRevenue">${{ number_format($myRevenue ?? 0, 2) }}</h2>
                <small>Total earnings</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-warning">
            <div class="card-body">
                <h4 class="card-title">Today's Revenue</h4>
                <h2 id="todayRevenue">${{ number_format($todayRevenue ?? 0, 2) }}</h2>
                <small>Today's earnings</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-info">
            <div class="card-body">
                <h4 class="card-title">Monthly Revenue</h4>
                <h2 id="monthlyRevenue">${{ number_format($monthlyRevenue ?? 0, 2) }}</h2>
                <small>This month</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-dark">
            <div class="card-body">
                <h4 class="card-title">Available Products</h4>
                <h2 id="totalProducts">{{ $totalProducts ?? 0 }}</h2>
                <small>Products to sell</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-secondary">
            <div class="card-body">
                <h4 class="card-title">Total Customers</h4>
                <h2 id="totalCustomers">{{ $totalCustomers ?? 0 }}</h2>
                <small>Customer base</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions for Seller -->
<div class="row mt-4">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Quick Actions</h4>
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('pos') }}" class="btn btn-success btn-lg btn-block">
                            <i class="typcn typcn-calculator"></i> Open POS
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-lg btn-block">
                            <i class="typcn typcn-plus"></i> New Sale
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('customers.create') }}" class="btn btn-warning btn-lg btn-block">
                            <i class="typcn typcn-user-add"></i> Add Customer
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('products.index') }}" class="btn btn-info btn-lg btn-block">
                            <i class="typcn typcn-shopping-cart"></i> View Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Top Products and Recent Sales -->
<div class="row mt-4">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">My Top Selling Products</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Sold Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($myTopProducts))
                            @foreach($myTopProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sale_items_sum_quantity ?? 0 }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Recent Sales</h4>
                <div class="list-group">
                    @if(isset($recentSales))
                        @foreach($recentSales as $sale)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Sale #{{ $sale->id }}</strong>
                                <br><small class="text-muted">{{ $sale->customer->name ?? 'Walk-in Customer' }}</small>
                                <br><small class="text-muted">{{ $sale->created_at->format('M d, Y H:i') }}</small>
                            </div>
                            <span class="badge badge-success">${{ number_format($sale->final_total, 2) }}</span>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Tips -->
<div class="row mt-4">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Sales Tips</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="alert alert-info">
                            <strong>Tip 1:</strong> Use the POS system for faster checkout and better customer experience.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-success">
                            <strong>Tip 2:</strong> Add customer details to build a loyal customer base and track preferences.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-warning">
                            <strong>Tip 3:</strong> Check product availability before promising delivery to customers.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add any seller-specific JavaScript here
    console.log('Seller dashboard loaded');
});
</script>
@endpush