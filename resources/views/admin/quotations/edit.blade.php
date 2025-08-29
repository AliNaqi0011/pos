@extends('layouts.main')
@section('content')
    <div class="container">

        <nav aria-label="breadcrumb" class="main-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('quotations') }}">Quotations</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
        <div class="main-body">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Quotation</h4>s
                        <form class="forms-sample" action="{{ route('quotations.update', $quotation->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $quotation->id }}">

                            <div class="form-group">
                                <label for="quotationNumber">Quotation Number</label>
                                <input type="text" name="quotation_number" class="form-control" id="quotationNumber"
                                       value="{{ $quotation->quotation_number }}" required>
                            </div>

                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <select class="form-control" name="customer_id" id="customer" required>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $quotation->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="quotationDate">Quotation Date</label>
                                <input type="date" name="quotation_date" class="form-control" id="quotationDate"
                                value="{{ $quotation->quotation_date->format('Y-m-d') }}"
                            </div>

                            <div class="form-group">
                                <label for="validUntil">Valid Until</label>
                                <input type="date" name="valid_until" class="form-control" id="validUntil"
                                       value="{{ $quotation->valid_until->format('Y-m-d') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="pending" {{ $quotation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $quotation->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $quotation->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="notes">Additional Notes</label>
                                <textarea name="notes" class="form-control" id="notes" rows="3">{{ $quotation->notes }}</textarea>
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
