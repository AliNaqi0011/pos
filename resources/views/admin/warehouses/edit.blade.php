@extends('layouts.main')
@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="main-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('warehouses') }}">Warehouses</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
        <div class="main-body">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Warehouse</h4>
                        <form class="forms-sample" action="{{ route('warehouses.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $warehouse->id }}">
                            <div class="form-group">
                                <label for="warehouseName">Warehouse Name</label>
                                <input type="text" name="name" class="form-control" id="warehouseName"
                                       value="{{ $warehouse->name }}" required>
                            </div>

                            <div class="form-group">
                                <label for="warehouseLocation">Location</label>
                                <input type="text" name="location" class="form-control" id="warehouseLocation"
                                       value="{{ $warehouse->location }}">
                            </div>

                            <div class="form-group">
                                <label for="warehouseNote">Note</label>
                                <textarea name="note" class="form-control" id="warehouseNote"
                                          rows="3">{{ $warehouse->note }}</textarea>
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
