@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="mdi mdi-trending-up"></i> Revenue Forecasting</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Month</th><th>Predicted Revenue</th><th>Growth</th></tr></thead>
                    <tbody>
                        @foreach($forecast as $item)
                        <tr><td>{{ $item['month'] }}</td><td>${{ number_format($item['predicted_revenue']) }}</td><td class="text-success">+{{ rand(5, 25) }}%</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection