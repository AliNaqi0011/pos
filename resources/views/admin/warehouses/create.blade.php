@extends('layouts.main')
@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('warehouses') }}">Warehouses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Warehouse</h4>
                    <form class="forms-sample" action="{{ route('warehouses.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="warehouseName">Warehouse Name</label>
                            <input type="text" name="name" class="form-control" id="warehouseName"
                                   placeholder="Enter Warehouse Name" required>
                        </div>
                        <div class="form-group">
                            <label for="warehouseLocation">Location</label>
                            <input type="text" name="location" class="form-control" id="warehouseLocation"
                                   placeholder="Enter Location (optional)">
                        </div>
                        <div class="form-group">
                            <label for="warehouseNote">Note</label>
                            <textarea name="note" class="form-control" id="warehouseNote"
                                      rows="3" placeholder="Enter note (optional)"></textarea>
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
