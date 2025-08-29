@extends('layouts.main  ')

@section('content')
<div class="container">
    <h2>Payments for Sale #{{ $sale->id }}</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Payment Method</th>
                <th>Amount</th>
                <th>Payment Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_method }}</td>
                    <td>${{ number_format($payment->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $payment->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('sales.payments.create', $sale->id) }}" class="btn btn-primary">Add Payment</a>
</div>
@endsection
