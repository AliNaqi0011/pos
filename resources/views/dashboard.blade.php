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
 
<!-- Dashboard Statistics Cards -->
<div class="row">
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-warning">
            <div class="card-body">
                <h4 class="card-title">Total Products</h4>
                <h2 id="totalProducts">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-danger">
            <div class="card-body">
                <h4 class="card-title">Total Customers</h4>
                <h2 id="totalCustomers">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-success">
            <div class="card-body">
                <h4 class="card-title">Total Users</h4>
                <h2 id="totalUsers">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-dark">
            <div class="card-body">
                <h4 class="card-title">Total Sales</h4>
                <h2 id="totalSales">0</h2>
            </div>
        </div>
    </div>

    <!-- ✅ New Card: Daily Sales -->
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-light ">
            <div class="card-body">
                <h4 class="card-title">Today's Sales</h4>
                <h2 id="dailySales">0</h2>
            </div>
        </div>
    </div>

    
    
    
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-info">
            <div class="card-body">
                <h4 class="card-title">Total Sales Returns</h4>
                <h2 id="totalSalesReturns">0</h2>
                <small id="totalSalesReturnAmount"></small>
            </div>
        </div>
    </div>
    <!-- ✅ New Card: Daily Sale Returns -->
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-3d bg-gradient-danger">
            <div class="card-body">
                <h4 class="card-title">Today's Sale Returns</h4>
                <h2 id="dailySaleReturns">0</h2>
            </div>
        </div>
    </div>
    
</div>

<!-- Sales Chart -->
<div class="row mt-4">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Sales Last 7 Days</h4>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Customers and Sales -->
<div class="row mt-4">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Top 5 Customers</h4>
                <canvas id="topCustomersChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Top 5 Sales</h4>
                <canvas id="topSalesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Products and Stock Alert Tables -->
<div class="row mt-4">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Top Selling Products</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Sold Qty</th>
                        </tr>
                    </thead>
                    <tbody id="topProductsTable">
                        <!-- Filled via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card card-3d">
            <div class="card-body">
                <h4 class="card-title">Stock Alert</h4>
                <table class="table table-danger table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock Qty</th>
                        </tr>
                    </thead>
                    <tbody id="stockAlertTable">
                        <!-- Filled via JS -->
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
    $.ajax({
        url: "{{ route('dashboard.stats') }}",
        method: "GET",
        success: function(data) {
            $('#totalProducts').text(data.totalProducts);
            $('#totalUsers').text(data.totalUsers);
            $('#totalSales').text(data.totalSales);
            $('#totalCustomers').text(data.totalCustomers);
            $('#totalSalesReturns').text(data.totalSalesReturns);
            $('#totalSalesReturnAmount').text('Amount: $' + data.totalSalesReturnAmount);
            $('#dailySales').text('$' + data.todaySaleAmount);
            $('#dailySaleReturns').text('$' + data.todaySaleReturnAmount);




// Sales Line Chart
const ctxLine = document.getElementById('salesChart').getContext('2d');

// Gradient for Sales
const gradientSales = ctxLine.createLinearGradient(0, 0, 0, 300);
gradientSales.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
gradientSales.addColorStop(1, 'rgba(54, 162, 235, 0)');

// Gradient for Sale Returns
const gradientReturns = ctxLine.createLinearGradient(0, 0, 0, 300);
gradientReturns.addColorStop(0, 'rgba(255, 99, 132, 0.6)');
gradientReturns.addColorStop(1, 'rgba(255, 99, 132, 0)');

new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: data.salesChart.dates,
        datasets: [
            {
                label: 'Sales',
                data: data.salesChart.salesData,
                fill: true,
                backgroundColor: gradientSales,
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgba(54, 162, 235, 1)',
                pointBorderWidth: 2,
                pointStyle: 'circle'
            },
            {
                label: 'Sale Returns',
                data: data.salesChart.saleReturnsData,
                fill: true,
                backgroundColor: gradientReturns,
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgba(255, 99, 132, 1)',
                pointBorderWidth: 2,
                pointStyle: 'rectRot'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: {
                    color: '#333',
                    font: { size: 14, weight: 'bold' }
                }
            },
            tooltip: {
                backgroundColor: '#222',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#ddd',
                borderWidth: 1
            }
        },
        elements: {
            point: { radius: 5, hoverRadius: 7, hitRadius: 10 }
        },
        scales: {
            x: { ticks: { color: '#666' }, grid: { display: false } },
            y: { ticks: { color: '#666' }, grid: { color: 'rgba(0,0,0,0.05)' } }
        }
    }
});


            // Top Customers Doughnut Chart
            new Chart(document.getElementById('topCustomersChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: data.topCustomers.map(c => c.name),
                    datasets: [{
                        data: data.topCustomers.map(c => c.sales_sum_amount),
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d'],
                        borderColor: '#fff',
                        borderWidth: 3,
                        hoverOffset: 20
                    }]
                },
                options: {
                    cutout: '50%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#333',
                                font: { size: 13, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#000',
                            bodyColor: '#fff',
                            titleColor: '#fff'
                        }
                    }
                }
            });

            // Top Sales Bar Chart
            new Chart(document.getElementById('topSalesChart'), {
                type: 'bar',
                data: {
                    labels: data.topSales.map(s => 'Sale #' + s.id),
                    datasets: [{
                        label: 'Amount',
                        data: data.topSales.map(s => s.amount),
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        borderRadius: 10,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#444',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    },
                    scales: {
                        x: { ticks: { color: '#333' }, grid: { display: false } },
                        y: { ticks: { color: '#333' }, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });

            // Top Products Table
            $('#topProductsTable').html(data.topProducts.map(p =>
                `<tr><td>${p.name}</td><td>${p.sale_items_sum_quantity}</td></tr>`
            ).join(''));

            // Stock Alert Table
            $('#stockAlertTable').html(data.stockAlertProducts.map(p =>
                `<tr><td>${p.name}</td><td>${p.stock_quantity}</td></tr>`
            ).join(''));
        },
        error: function(err) {
            console.error("Dashboard data loading failed", err);
        }
    });
});
</script>
@endpush
