@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>📈 Analytics Dashboard</h4>
                <div class="float-right">
                    <select class="form-control form-control-sm">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                        <option>Last 3 Months</option>
                        <option>Last Year</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body">
                                <h5>Total Revenue</h5>
                                <h3>${{ number_format($data['totalRevenue'], 2) }}</h3>
                                <small>↗️ Real-time data</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-success text-white">
                            <div class="card-body">
                                <h5>Profit Margin</h5>
                                <h3>{{ number_format($data['profitMargin'], 1) }}%</h3>
                                <small>↗️ Calculated live</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-warning text-white">
                            <div class="card-body">
                                <h5>Avg Order Value</h5>
                                <h3>${{ number_format($data['avgOrderValue'], 2) }}</h3>
                                <small>↗️ From sales data</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-info text-white">
                            <div class="card-body">
                                <h5>Total Sales</h5>
                                <h3>{{ $data['totalSales'] }}</h3>
                                <small>↗️ Live count</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Revenue Trend</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Top Products</h5>
                            </div>
                            <div class="card-body">
                                @if($data['topProducts']->count() > 0)
                                    @php $maxRevenue = $data['topProducts']->first()->total_revenue; @endphp
                                    @foreach($data['topProducts'] as $product)
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $product->product->name ?? 'Unknown Product' }}</span>
                                            <span>${{ number_format($product->total_revenue, 0) }}</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" style="width: {{ ($product->total_revenue / $maxRevenue) * 100 }}%"></div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No sales data available</p>
                                @endif
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
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Revenue',
            data: [12000, 19000, 15000, 25000, 22000, 30000],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endsection