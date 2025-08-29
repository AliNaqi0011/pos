@extends('layouts.main')
@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotations') }}">Quotations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Quotation</h4>
                    <form class="forms-sample" action="{{ route('quotations.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="quotationNumber">Quotation Number</label>
                            <input type="text" name="quotation_number" class="form-control" id="quotationNumber"
                                   placeholder="Enter Quotation Number" required>
                        </div>

                        <div class="form-group">
                            <label for="customer">Customer</label>
                            <select class="form-control" name="customer_id" id="customer" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quotationDate">Quotation Date</label>
                            <input type="date" name="quotation_date" class="form-control" id="quotationDate" required>
                        </div>

                        <div class="form-group">
                            <label for="validUntil">Valid Until</label>
                            <input type="date" name="valid_until" class="form-control" id="validUntil" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea name="notes" class="form-control" id="notes" rows="3" placeholder="Enter additional notes (optional)"></textarea>
                        </div>

                        <hr>
                        <h5>Quotation Items</h5>
                        <table class="table table-bordered" id="quotation-items">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th><button type="button" class="btn btn-sm btn-success" onclick="addItem()">+</button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="items[0][product_id]" class="form-control" required>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control" min="1" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)">x</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <button type="reset" class="btn btn-light">Reset Form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript to handle dynamic item rows --}}
<script>
let itemIndex = 1;

function addItem() {
    const tableBody = document.querySelector('#quotation-items tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <select name="items[${itemIndex}][product_id]" class="form-control" required>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)">x</button>
        </td>
    `;
    tableBody.appendChild(newRow);
    itemIndex++;
}

function removeItem(button) {
    button.closest('tr').remove();
}
</script>
@endsection
