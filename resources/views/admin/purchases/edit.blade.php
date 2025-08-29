@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.purchases.index') }}">Purchases</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Purchase</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Purchase</h4>

                    <form action="{{ route('admin.purchases.update', $purchase->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchaseDate">Purchase Date</label>
                                    <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier">Supplier</label>
                                    <select name="supplier_id" class="form-control" required>
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ $supplier->id == old('supplier_id', $purchase->supplier_id) ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Product Rows --}}
                        <div id="product-rows">
                            @foreach ($purchase->items as $index => $item)
                                <div class="product-row row mb-2">
                                    <div class="col-md-3">
                                        <select name="products[{{ $index }}][product_id]" class="form-control" required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" {{ $product->id == $item->product_id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="products[{{ $index }}][quantity]" class="form-control" value="{{ $item->quantity }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="products[{{ $index }}][tax_percent]" class="form-control" value="{{ $item->tax_percent }}" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="products[{{ $index }}][discount_percent]" class="form-control" value="{{ $item->discount_percent }}" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="products[{{ $index }}][total]" class="form-control" value="{{ $item->total }}" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger remove-row">X</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-product-row" class="btn btn-info mb-3">Add Another Product</button>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Total Amount</label>
                                <input type="number" step="0.01" name="total_amount" class="form-control" value="{{ $purchase->total_amount }}" required>
                            </div>
                            <div class="col-md-4">
                                <label>Discount Amount</label>
                                <input type="number" step="0.01" name="discount_amount" class="form-control" value="{{ $purchase->discount_amount }}">
                            </div>
                            <div class="col-md-4">
                                <label>Final Total</label>
                                <input type="number" step="0.01" name="final_total" class="form-control" value="{{ $purchase->final_total }}" required>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="notes">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $purchase->notes }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="received" {{ $purchase->status == 'received' ? 'selected' : '' }}>Received</option>
                                <option value="pending" {{ $purchase->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Purchase</button>
                        <a href="{{ route('admin.purchases.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-product-row').addEventListener('click', function () {
        const productRows = document.getElementById('product-rows');
        const rowCount = productRows.querySelectorAll('.product-row').length;

        const newRow = `
            <div class="product-row row mb-2">
                <div class="col-md-3">
                    <select name="products[${rowCount}][product_id]" class="form-control" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="products[${rowCount}][quantity]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="products[${rowCount}][tax_percent]" class="form-control" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="number" name="products[${rowCount}][discount_percent]" class="form-control" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="number" name="products[${rowCount}][total]" class="form-control" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-row">X</button>
                </div>
            </div>
        `;
        productRows.insertAdjacentHTML('beforeend', newRow);
    });

    document.getElementById('product-rows').addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-row')) {
            event.target.closest('.product-row').remove();
        }
    });
</script>
@endsection
