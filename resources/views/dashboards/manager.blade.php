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

<!-- Manager Dashboard Statistics Cards -->
<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-warning">
            <div class="card-body">
                <h4 class="card-title">Total Products</h4>
                <h2 id="totalProducts">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card card-3d bg-danger">
            <div class="card-body">
                <h4 class="card-title">Total Customers</h4>
                <h2 id="totalCustomers">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-dark">
            <div class="card-body">
                <h4 class="card-title">Total Sales</h4>
                <h2 id="totalSales">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-light">
            <div class="card-body">
                <h4 class="card-title">Today's Sales</h4>
                <h2 id="dailySales">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card card-3d bg-info">
            <div class="card-body">
                <h4 class="card-title">Total Sales Returns</h4>
                <h2 id="totalSalesReturns">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card card-3d bg-primary">
            <div class="card-body">
                <h4 class="card-title">Monthly Revenue</h4>
                <h2 id="monthlyRevenue">0</h2>
            </div>
        </div>
    </div>
</div>

<!-- Sales Performance Chart -->
<div class="row mt-4">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Sales Performance Last 7 Days</h4>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Top Customers</h4>
                <canvas id="topCustomersChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Management -->
<div class="row mt-4">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Low Stock Alert</h4>
                <table class="table table-warning table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock Qty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="stockAlertTable">
                        <!-- Filled via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Recent Sales Activity</h4>
                <div id="recentSales">
                    <!-- Filled via JS -->
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
            $('#totalProducts').text(data.totalProducts);
            $('#totalCustomers').text(data.totalCustomers);
            $('#totalSales').text(data.totalSales);
            $('#totalSalesReturns').text(data.totalSalesReturns);
            $('#dailySales').text('$' + data.todaySaleAmount);
            $('#monthlyRevenue').text('$' + data.monthlyRevenue);

            // Sales Line Chart
            const ctxLine = document.getElementById('salesChart').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: data.salesChart.dates,
                    datasets: [{
                        label: 'Sales',
                        data: data.salesChart.salesData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true }
                    }
                }
            });

            // Top Customers Pie Chart
            new Chart(document.getElementById('topCustomersChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: data.topCustomers.map(c => c.name),
                    datasets: [{
                        data: data.topCustomers.map(c => c.sales_sum_amount),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Stock Alert Table
            $('#stockAlertTable').html(data.stockAlertProducts.map(p =>
                `<tr>
                    <td>${p.name}</td>
                    <td>${p.stock_quantity}</td>
                    <td><span class="badge badge-warning">Low Stock</span></td>
                </tr>`
            ).join(''));

            // Recent Sales Activity
            $('#recentSales').html(data.topSales.slice(0, 5).map(s =>
                `<div class="d-flex justify-content-between mb-2">
                    <span>Sale #${s.id}</span>
                    <span class="text-success">$${s.amount}</span>
                </div>`
            ).join(''));
        }
    });
});
</script>
@endpush