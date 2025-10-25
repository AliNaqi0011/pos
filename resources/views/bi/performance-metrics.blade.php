@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>⚡ Performance Metrics</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Sales Growth</h5>
                                <h3 class="text-success">+15.2%</h3>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: 76%"></div>
                                </div>
                                <small>Target: 20%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Customer Retention</h5>
                                <h3 class="text-primary">78%</h3>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: 78%"></div>
                                </div>
                                <small>Target: 80%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Inventory Turnover</h5>
                                <h3 class="text-warning">6.2x</h3>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: 62%"></div>
                                </div>
                                <small>Target: 8x</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Profit Margin</h5>
                                <h3 class="text-success">32.5%</h3>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: 81%"></div>
                                </div>
                                <small>Target: 40%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Order Fulfillment</h5>
                                <h3 class="text-success">95%</h3>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: 95%"></div>
                                </div>
                                <small>Target: 98%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Customer Satisfaction</h5>
                                <h3 class="text-success">4.6/5</h3>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: 92%"></div>
                                </div>
                                <small>Target: 4.8/5</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Monthly Performance Trends</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="performanceChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Department Performance</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Department</th>
                                                <th>Revenue</th>
                                                <th>Growth</th>
                                                <th>Performance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Electronics</td>
                                                <td>$85,200</td>
                                                <td class="text-success">+18%</td>
                                                <td><span class="badge badge-success">Excellent</span></td>
                                            </tr>
                                            <tr>
                                                <td>Accessories</td>
                                                <td>$32,150</td>
                                                <td class="text-success">+12%</td>
                                                <td><span class="badge badge-primary">Good</span></td>
                                            </tr>
                                            <tr>
                                                <td>Services</td>
                                                <td>$18,900</td>
                                                <td class="text-warning">+5%</td>
                                                <td><span class="badge badge-warning">Average</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('performanceChart').getContext('2d');
new Chart(ctx, {
    type: 'radar',
    data: {
        labels: ['Sales Growth', 'Customer Retention', 'Inventory Turnover', 'Profit Margin', 'Order Fulfillment', 'Customer Satisfaction'],
        datasets: [{
            label: 'Current Performance',
            data: [76, 78, 62, 81, 95, 92],
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)'
        }, {
            label: 'Target',
            data: [100, 80, 80, 100, 98, 96],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            r: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>
@endsection