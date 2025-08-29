@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></li>
            <li class="breadcrumb-item active" aria-current="page">View</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Sale Details</h4>
                    <div class="form-group">
                        <label for="saleDate">Sale Date</label>
                        <input type="text" class="form-control" id="saleDate" value="{{ $sale->sale_date }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="customerName">Customer</label>
                        <input type="text" class="form-control" id="customerName" value="{{ $sale->customer->name }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="totalAmount">Total Amount</label>
                        <input type="text" class="form-control" id="totalAmount" value="${{ $sale->total_amount }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="discount">Discount</label>
                        <input type="text" class="form-control" id="discount" value="${{ $sale->discount }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="paymentMethod">Payment Method</label>
                        <input type="text" class="form-control" id="paymentMethod" value="{{ ucfirst($sale->payment_method) }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="saleStatus">Status</label>
                        <input type="text" class="form-control" id="saleStatus" value="{{ ucfirst($sale->status) }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="saleItems">Sale Items</label>
                        <textarea class="form-control" id="saleItems" rows="3" readonly>{{ $sale->sale_items }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="createdAt">Created At</label>
                        <input type="text" class="form-control" id="createdAt" value="{{ $sale->created_at->format('d M Y, H:i') }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="updatedAt">Last Updated</label>
                        <input type="text" class="form-control" id="updatedAt" value="{{ $sale->updated_at->format('d M Y, H:i') }}" readonly>
                    </div>

                    <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">Back to Sales List</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
