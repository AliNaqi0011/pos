@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Sale</h4>

                    {{-- Validation Error Alert --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="forms-sample" action="{{ route('admin.sales.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customerName">Customer</label>
                                    <select name="customer_id" class="form-control" required>
                                        <option value="">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="salesperson">Salesperson</label>
                                    <select name="user_id" class="form-control" required>
                                        <option value="">Select Salesperson</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Product Rows --}}
                        <div id="product-rows"></div>

                        <button type="button" id="add-product-row" class="btn btn-info mb-3">Add Product</button>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Total Amount</label>
                                    <input type="number" step="0.01" name="total_amount" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Discount Amount</label>
                                    <input type="number" step="0.01" name="discount_amount" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Final Total</label>
                                    <input type="number" step="0.01" name="final_total" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="canceled">Canceled</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <button type="reset" class="btn btn-light">Reset</button>
                    </form>

                    {{-- Hidden template for JS --}}
                    <template id="product-row-template">
                        <div class="product-row row mb-2">
                            <div class="col-md-3">
                                <select name="__index__[product_id]" class="form-control" required>
                                    <option value="">Select Product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="__index__[quantity]" class="form-control" placeholder="Qty" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="__index__[tax_percent]" class="form-control" placeholder="Tax %" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="__index__[discount_percent]" class="form-control" placeholder="Disc %" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="__index__[total]" class="form-control" placeholder="Total" step="0.01" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger remove-row">X</button>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    let rowCount = 0;
    const addButton = document.getElementById('add-product-row');
    const productRows = document.getElementById('product-rows');
    const rowTemplate = document.getElementById('product-row-template').innerHTML;

    addButton.addEventListener('click', () => {
        const newRow = rowTemplate.replace(/__index__/g, `products[${rowCount}]`);
        productRows.insertAdjacentHTML('beforeend', newRow);
        rowCount++;
    });

    // Delegate remove button
    productRows.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.product-row').remove();
        }
    });

    // Add first row by default
    window.addEventListener('DOMContentLoaded', () => {
        addButton.click();
    });
</script>
@endsection
