@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Sale</h4>
                    <form class="forms-sample" action="{{ route('admin.sales.update', $sale->id) }}" method="POST">
                        @csrf
                        @method('PUT') 

                        <!-- Sale ID is passed through the URL, so no need for a hidden field for the id -->
                        <div class="form-group">
                            <label for="saleDate">Sale Date</label>
                            <input type="date" name="sale_date" class="form-control" id="saleDate" value="{{ old('sale_date', $sale->sale_date) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="customerName">Customer</label>
                            <select name="customer_id" class="form-control" id="customerName" required>
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $customer->id == old('customer_id', $sale->customer_id) ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="totalAmount">Total Amount</label>
                            <input type="number" name="total_amount" class="form-control" id="totalAmount" value="{{ old('total_amount', $sale->total_amount) }}" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="discount">Discount</label>
                            <input type="number" name="discount" class="form-control" id="discount" value="{{ old('discount', $sale->discount) }}" step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="paymentMethod">Payment Method</label>
                            <select name="payment_method" class="form-control" id="paymentMethod" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash" {{ old('payment_method', $sale->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="credit_card" {{ old('payment_method', $sale->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="bank_transfer" {{ old('payment_method', $sale->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="saleStatus">Status</label>
                            <select name="status" class="form-control" id="saleStatus" required>
                                <option value="completed" {{ old('status', $sale->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ old('status', $sale->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="cancelled" {{ old('status', $sale->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="saleItems">Sale Items</label>
                            <textarea name="sale_items" class="form-control" id="saleItems" rows="3" required>{{ old('sale_items', $sale->sale_items) }}</textarea>
                            <small class="text-muted">List the items included in the sale (e.g., Product1, Product2).</small>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <button type="reset" class="btn btn-light">Reset Form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
