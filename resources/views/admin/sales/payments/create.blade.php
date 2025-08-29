@extends('layouts.main')

@section('content')
<div class="container">
    <h2>Add Payment for Sale #{{ $sale->id }}</h2>

    <form action="{{ route('sales.payments.store', $sale->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" class="form-control" required>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="online">Online</option>
            </select>
        </div>
        <div class="form-group">
            <label for="payment_date">Payment Date</label>
            <input type="date" name="payment_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Payment</button>
    </form>
</div>
@endsection
