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
    <h2 class="text-success">Admin Dashboard</h2>
    <span class="badge badge-primary">Admin Panel</span>
</div>

<!-- Admin Statistics Cards -->
<div class="row">
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-warning">
            <div class="card-body">
                <h4 class="card-title">Total Products</h4>
                <h2 id="totalProducts">{{ $totalProducts ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-danger">
            <div class="card-body">
                <h4 class="card-title">Total Customers</h4>
                <h2 id="totalCustomers">{{ $totalCustomers ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-success">
            <div class="card-body">
                <h4 class="card-title">Total Sales</h4>
                <h2 id="totalSales">{{ $totalSales ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-dark">
            <div class="card-body">
                <h4 class="card-title">Total Revenue</h4>
                <h2 id="totalRevenue">${{ number_format($totalRevenue ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-light text-dark">
            <div class="card-body">
                <h4 class="card-title">Today's Revenue</h4>
                <h2 id="todayRevenue">${{ number_format($todayRevenue ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-info">
            <div class="card-body">
                <h4 class="card-title">Monthly Revenue</h4>
                <h2 id="monthlyRevenue">${{ number_format($monthlyRevenue ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-secondary">
            <div class="card-body">
                <h4 class="card-title">Total Purchases</h4>
                <h2 id="totalPurchases">{{ $totalPurchases ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-danger">
            <div class="card-body">
                <h4 class="card-title">Low Stock Alert</h4>
                <h2 id="lowStockProducts">{{ $lowStockProducts ?? 0 }}</h2>
                <small>Products need restock</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mt-4">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Sales Last 7 Days</h4>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Expenses Last 7 Days</h4>
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Customers and Quick Actions -->
<div class="row mt-4">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Top 5 Customers</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Total Spent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($topCustomers))
                            @foreach($topCustomers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>${{ number_format($customer->sales_sum_final_total ?? 0, 2) }}</td>
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
                <h4 class="card-title">Quick Actions</h4>
                <div class="d-grid gap-2">
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-lg">Add New Product</a>
                    <a href="{{ route('customers.create') }}" class="btn btn-success btn-lg">Add New Customer</a>
                    <a href="{{ route('admin.sales.create') }}" class="btn btn-warning btn-lg">Create Sale</a>
                    <a href="{{ route('admin.purchases.create') }}" class="btn btn-info btn-lg">Create Purchase</a>
                    <a href="{{ route('pos') }}" class="btn btn-dark btn-lg">Open POS</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 300);
    salesGradient.addColorStop(0, 'rgba(40, 167, 69, 0.8)');
    salesGradient.addColorStop(1, 'rgba(40, 167, 69, 0)');

    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesChart['dates'] ?? []),
            datasets: [{
                label: 'Sales',
                data: @json($salesChart['salesData'] ?? []),
                fill: true,
                backgroundColor: salesGradient,
                borderColor: 'rgba(40, 167, 69, 1)',
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

    // Expense Chart
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    const expenseGradient = expenseCtx.createLinearGradient(0, 0, 0, 300);
    expenseGradient.addColorStop(0, 'rgba(220, 53, 69, 0.8)');
    expenseGradient.addColorStop(1, 'rgba(220, 53, 69, 0)');

    new Chart(expenseCtx, {
        type: 'bar',
        data: {
            labels: @json($expenseChart['dates'] ?? []),
            datasets: [{
                label: 'Expenses',
                data: @json($expenseChart['expenseData'] ?? []),
                backgroundColor: expenseGradient,
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 2,
                borderRadius: 10
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