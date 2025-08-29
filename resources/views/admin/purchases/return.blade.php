@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.purchases.index') }}">Purchases</a></li>
            <li class="breadcrumb-item active" aria-current="page">Return</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Return Items for Purchase #{{ $purchase->id }}</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> Please fix the following errors:
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.purchases.processReturn', $purchase->id) }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="return_date">Return Date</label>
                                <input type="date" name="return_date" class="form-control" id="return_date" value="{{ old('return_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="reason">Reason for Return</label>
                                <textarea name="notes" class="form-control" rows="2" id="reason" required>{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <h5 class="mt-4">Returnable Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered mt-2">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Purchased Qty</th>
                                        <th>Return Qty</th>
                                        <th>Unit Price</th>
                                        <th>Return Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>
                                                <input 
                                                    type="number" 
                                                    name="return_items[{{ $item->id }}][quantity]" 
                                                    class="form-control return-qty" 
                                                    min="0" 
                                                    max="{{ $item->quantity }}" 
                                                    value="0"
                                                    data-price="{{ $item->product_cost }}"
                                                    data-id="{{ $item->id }}"
                                                >
                                            </td>
                                            <td>Rs {{ number_format($item->product_cost, 2) }}</td>
                                            <td>
                                                <input 
                                                    type="number" 
                                                    name="return_items[{{ $item->id }}][sub_total]" 
                                                    class="form-control return-total" 
                                                    id="sub_total_{{ $item->id }}"
                                                    readonly
                                                    value="0"
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4 offset-md-8">
                                <label><strong>Grand Total:</strong></label>
                                <input type="text" class="form-control" id="grand_total" readonly value="0.00">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-danger">Submit Return</button>
                            <a href="{{ route('admin.purchases.show', $purchase->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const qtyInputs = document.querySelectorAll('.return-qty');
        const grandTotalInput = document.getElementById('grand_total');

        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.return-total').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            grandTotalInput.value = total.toFixed(2);
        }

        qtyInputs.forEach(input => {
            input.addEventListener('input', function () {
                const price = parseFloat(this.dataset.price) || 0;
                const qty = parseFloat(this.value) || 0;
                const id = this.dataset.id;
                const subTotal = price * qty;

                const totalInput = document.getElementById('sub_total_' + id);
                if (totalInput) {
                    totalInput.value = subTotal.toFixed(2);
                }

                updateGrandTotal();
            });
        });
    });
</script>
@endsection
