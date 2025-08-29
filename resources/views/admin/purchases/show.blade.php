@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.purchases.index') }}">Purchases</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Purchase</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Purchase Details</h4>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Purchase Date</label>
<input type="text" class="form-control" value="{{ optional($purchase->date)->format('d M Y') }}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label>Purchased By</label>
                            <input type="text" class="form-control" value="{{ $purchase->user->name ?? 'N/A' }}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label>Status</label>
                            <input type="text" class="form-control" value="{{ ucfirst($purchase->status) }}" readonly>
                        </div>
                    </div>

                    <h5>Items Purchased</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Tax %</th>
                                <th>Discount %</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchase->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->product_price, 2) }}</td>
                                    <td>{{ $item->tax_percent ?? 0 }}%</td>
                                    <td>{{ $item->discount_percent ?? 0 }}%</td>
                                    <td>{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <label>Total Amount</label>
                            <input type="text" class="form-control" value="{{ number_format($purchase->total_amount, 2) }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Discount</label>
                            <input type="text" class="form-control" value="{{ number_format($purchase->discount_amount, 2) }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Final Total</label>
                            <input type="text" class="form-control" value="{{ number_format($purchase->final_total, 2) }}" readonly>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label>Notes</label>
                        <textarea class="form-control" rows="3" readonly>{{ $purchase->notes }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Created At</label>
                            <input type="text" class="form-control" value="{{ $purchase->created_at->format('d M Y, H:i') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Last Updated</label>
                            <input type="text" class="form-control" value="{{ $purchase->updated_at->format('d M Y, H:i') }}" readonly>
                        </div>
                    </div>

                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-secondary mt-3">Back to Purchases</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
