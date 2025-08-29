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
    .quick-action-btn {
        border-radius: 15px;
        padding: 15px;
        margin: 10px 0;
        transition: all 0.3s ease;
    }
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
</style>

<!-- Sales Dashboard Statistics Cards -->
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d bg-success">
            <div class="card-body">
                <h4 class="card-title">Today's Sales</h4>
                <h2 id="dailySales">$0</h2>
                <p class="text-light">Your daily target progress</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d bg-info">
            <div class="card-body">
                <h4 class="card-title">This Week's Sales</h4>
                <h2 id="weeklySales">$0</h2>
                <p class="text-light">Weekly performance</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d bg-warning">
            <div class="card-body">
                <h4 class="card-title">Total Customers</h4>
                <h2 id="totalCustomers">0</h2>
                <p class="text-light">Active customer base</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d bg-primary">
            <div class="card-body">
                <h4 class="card-title">Available Products</h4>
                <h2 id="totalProducts">0</h2>
                <p class="text-light">Ready for sale</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Quick Actions</h4>
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('pos') }}" class="btn btn-success btn-block quick-action-btn">
                            <i class="typcn typcn-shopping-cart"></i> New Sale
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('customers.create') }}" class="btn btn-info btn-block quick-action-btn">
                            <i class="typcn typcn-user-add"></i> Add Customer
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('products.index') }}" class="btn btn-warning btn-block quick-action-btn">
                            <i class="typcn typcn-th-list"></i> View Products
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.sales.index') }}" class="btn btn-primary btn-block quick-action-btn">
                            <i class="typcn typcn-chart-bar"></i> Sales History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Performance and Top Products -->
<div class="row mt-4">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Your Sales This Week</h4>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Top Selling Products</h4>
                <div id="topProductsList">
                    <!-- Filled via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mt-4">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Recent Sales Activity</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sale ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentSalesTable">
                            <!-- Filled via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $.ajax({
        url: "{{ route('dashboard.stats') }}",
        method: "GET",
        success: function(data) {
            $('#dailySales').text('$' + data.todaySaleAmount);
            $('#weeklySales').text('$' + data.weeklySales);
            $('#totalCustomers').text(data.totalCustomers);
            $('#totalProducts').text(data.totalProducts);

            // Sales Bar Chart for the week
            const ctxBar = document.getElementById('salesChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: data.salesChart.dates,
                    datasets: [{
                        label: 'Daily Sales',
                        data: data.salesChart.salesData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });

            // Top Products List
            $('#topProductsList').html(data.topProducts.map((p, index) =>
                `<div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="badge badge-primary">${index + 1}</span>
                        <span class="ml-2">${p.name}</span>
                    </div>
                    <span class="text-success">${p.sale_items_sum_quantity} sold</span>
                </div>`
            ).join(''));

            // Recent Sales Table
            $('#recentSalesTable').html(data.topSales.map(s =>
                `<tr>
                    <td>#${s.id}</td>
                    <td>Customer ${s.id}</td>
                    <td class="text-success">$${s.amount}</td>
                    <td>Today</td>
                    <td><span class="badge badge-success">Completed</span></td>
                </tr>`
            ).join(''));
        }
    });
});
</script>
@endpush