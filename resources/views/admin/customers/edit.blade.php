@extends('layouts.main')
@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="main-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customers') }}">Customers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
        <div class="main-body">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Customer</h4>
                        <form class="forms-sample" action="{{ route('customers.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $customer->id }}">

                            <div class="form-group">
                                <label for="customerName">Customer Name</label>
                                <input type="text" name="name" class="form-control" id="customerName"
                                       value="{{ $customer->name }}" required>
                            </div>

                            <div class="form-group">
                                <label for="customerEmail">Email</label>
                                <input type="email" name="email" class="form-control" id="customerEmail"
                                       value="{{ $customer->email }}">
                            </div>

                            <div class="form-group">
                                <label for="customerPhone">Phone</label>
                                <input type="text" name="phone" class="form-control" id="customerPhone"
                                       value="{{ $customer->phone }}">
                            </div>

                            <div class="form-group">
                                <label for="customerAddress">Address</label>
                                <textarea name="address" class="form-control" id="customerAddress"
                                          rows="3">{{ $customer->address }}</textarea>
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
