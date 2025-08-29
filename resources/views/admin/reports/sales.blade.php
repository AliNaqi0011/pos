@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Sales Report - {{ ucfirst($period) }}</h4>
                    <div>
                        <a href="{{ route('reports.sales', ['period' => $period, 'date' => $date, 'export' => 'csv']) }}" class="btn btn-success btn-sm">
                            <i class="typcn typcn-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="period" class="form-control">
                                <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date" value="{{ $date }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3>{{ $total_sales }}</h3>
                                <p>Total Sales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3>${{ number_format($total_amount, 2) }}</h3>
                                <p>Total Revenue</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3>${{ $total_sales > 0 ? number_format($total_amount / $total_sales, 2) : '0.00' }}</h3>
                                <p>Average Sale</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sale ID</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                            <tr>
                                <td>#{{ $sale->id }}</td>
                                <td>{{ $sale->sale_date->format('M d, Y H:i') }}</td>
                                <td>{{ $sale->customer->name }}</td>
                                <td>{{ $sale->total_items }}</td>
                                <td>${{ number_format($sale->final_total, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $sale->status == 'final' ? 'success' : 'warning' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No sales found for this period</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection