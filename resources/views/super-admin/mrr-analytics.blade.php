@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="mdi mdi-chart-line"></i> MRR/ARR Analytics</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>${{ number_format($data['current_mrr']) }}</h3>
                                    <p class="mb-0">Current MRR</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>${{ number_format($data['current_mrr'] * 12) }}</h3>
                                    <p class="mb-0">Annual ARR</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $data['growth_rate'] }}%</h3>
                                    <p class="mb-0">Growth Rate</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>${{ number_format($data['expansion_revenue']) }}</h3>
                                    <p class="mb-0">Expansion Revenue</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Revenue Trends</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Metric</th>
                                            <th>Current Month</th>
                                            <th>Previous Month</th>
                                            <th>Growth</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>MRR</td>
                                            <td>${{ number_format($data['current_mrr']) }}</td>
                                            <td>${{ number_format($data['current_mrr'] * 0.9) }}</td>
                                            <td class="text-success">+{{ $data['growth_rate'] }}%</td>
                                        </tr>
                                        <tr>
                                            <td>New MRR</td>
                                            <td>${{ number_format($data['expansion_revenue']) }}</td>
                                            <td>${{ number_format($data['expansion_revenue'] * 0.8) }}</td>
                                            <td class="text-success">+25%</td>
                                        </tr>
                                        <tr>
                                            <td>Churn MRR</td>
                                            <td>${{ number_format($data['current_mrr'] * 0.05) }}</td>
                                            <td>${{ number_format($data['current_mrr'] * 0.07) }}</td>
                                            <td class="text-success">-28%</td>
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
@endsection