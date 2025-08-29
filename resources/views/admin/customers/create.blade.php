@extends('layouts.main')
@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customers') }}">Customers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Customer</h4>
                    <form class="forms-sample" action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="customerName">Customer Name</label>
                            <input type="text" name="name" class="form-control" id="customerName"
                                   placeholder="Enter Customer Name" required>
                        </div>

                        <div class="form-group">
                            <label for="customerEmail">Email</label>
                            <input type="email" name="email" class="form-control" id="customerEmail"
                                   placeholder="Enter Email (optional)">
                        </div>

                        <div class="form-group">
                            <label for="customerPhone">Phone</label>
                            <input type="text" name="phone" class="form-control" id="customerPhone"
                                   placeholder="Enter Phone Number (optional)">
                        </div>

                        <div class="form-group">
                            <label for="customerAddress">Address</label>
                            <textarea name="address" class="form-control" id="customerAddress"
                                      rows="3" placeholder="Enter Address (optional)"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <button type="reset" class="btn btn-light">Reset Form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
