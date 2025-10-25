@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>🔮 Sales Forecasting</h4>
                <button class="btn btn-primary btn-sm float-right">Generate Forecast</button>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-gradient-info text-white">
                            <div class="card-body">
                                <h5>Next Month Forecast</h5>
                                <h3>$145,200</h3>
                                <small>Based on 6-month trend</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-gradient-success text-white">
                            <div class="card-body">
                                <h5>Confidence Level</h5>
                                <h3>87%</h3>
                                <small>High accuracy prediction</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-gradient-warning text-white">
                            <div class="card-body">
                                <h5>Growth Rate</h5>
                                <h3>+12.5%</h3>
                                <small>Expected vs current month</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Sales Forecast Chart</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="forecastChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>Seasonal Trends</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Season</th>
                                        <th>Avg Growth</th>
                                        <th>Peak Month</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Spring</td>
                                        <td>+8.5%</td>
                                        <td>March</td>
                                    </tr>
                                    <tr>
                                        <td>Summer</td>
                                        <td>+15.2%</td>
                                        <td>July</td>
                                    </tr>
                                    <tr>
                                        <td>Fall</td>
                                        <td>+22.8%</td>
                                        <td>November</td>
                                    </tr>
                                    <tr>
                                        <td>Winter</td>
                                        <td>+5.1%</td>
                                        <td>December</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Forecast Factors</h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                Historical Data Weight
                                <span class="badge badge-primary">70%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                Seasonal Adjustment
                                <span class="badge badge-success">20%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                Market Trends
                                <span class="badge badge-warning">10%</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('forecastChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul (F)', 'Aug (F)', 'Sep (F)'],
        datasets: [{
            label: 'Actual Sales',
            data: [120000, 135000, 128000, 142000, 138000, 155000, null, null, null],
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)'
        }, {
            label: 'Forecasted Sales',
            data: [null, null, null, null, null, 155000, 165000, 172000, 168000],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            borderDash: [5, 5]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endsection