@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.purchases.index') }}">Purchases</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Purchase</h4>
                    <form class="forms-sample" action="{{ route('admin.purchases.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="receivedBy">Purchased By</label>
                                    <select name="user_id" class="form-control" required>
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="warehouse">Warehouse</label>
                                    <select name="warehouse_id" class="form-control" required>
                                        <option value="">Select Warehouse</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="date">Purchase Date</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
    </div>
</div>


                        <!-- Product Rows Section -->
                        <div id="product-rows">
                            <div class="product-row row mb-2">
                                <div class="col-md-2">
                                    <select name="products[0][product_id]" class="form-control" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="products[0][quantity]" class="form-control" placeholder="Qty" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="products[0][product_cost]" class="form-control" placeholder="Cost" step="0.01" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="products[0][tax_value]" class="form-control" placeholder="Tax %" step="0.01">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="products[0][discount_value]" class="form-control" placeholder="Discount %" step="0.01">
                                </div>
                                <div class="col-md-1">
                                    <input type="number" name="products[0][sub_total]" class="form-control" placeholder="Subtotal" step="0.01" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger remove-row">X</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-product-row" class="btn btn-info mb-3">Add Another Product</button>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Total Amount</label>
                                <input type="number" step="0.01" name="total_amount" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Discount</label>
                                <input type="number" step="0.01" name="discount" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Shipping</label>
                                <input type="number" step="0.01" name="shipping" class="form-control">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Tax Amount</label>
                                <input type="number" step="0.01" name="tax_amount" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Grand Total</label>
                                <input type="number" step="0.01" name="grand_total" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Received Amount</label>
                                <input type="number" step="0.01" name="received_amount" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Paid Amount</label>
                                <input type="number" step="0.01" name="paid_amount" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Payment Type</label>
                                <select name="payment_type" class="form-control">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank">Bank</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="notes">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS to add product rows -->
<script>
    document.getElementById('add-product-row').addEventListener('click', function () {
        const productRows = document.getElementById('product-rows');
        const rowCount = productRows.querySelectorAll('.product-row').length;

        const newRow = `
            <div class="product-row row mb-2">
                <div class="col-md-2">
                    <select name="products[${rowCount}][product_id]" class="form-control" required>
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="products[${rowCount}][quantity]" class="form-control" placeholder="Qty" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="products[${rowCount}][product_cost]" class="form-control" placeholder="Cost" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="products[${rowCount}][tax_value]" class="form-control" placeholder="Tax %" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="number" name="products[${rowCount}][discount_value]" class="form-control" placeholder="Discount %" step="0.01">
                </div>
                <div class="col-md-1">
                    <input type="number" name="products[${rowCount}][sub_total]" class="form-control" placeholder="Subtotal" step="0.01" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-row">X</button>
                </div>
            </div>
        `;

        productRows.insertAdjacentHTML('beforeend', newRow);
    });

    document.getElementById('product-rows').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.product-row').remove();
        }
    });
</script>
@endsection
