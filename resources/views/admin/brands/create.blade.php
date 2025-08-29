@extends('layouts.main')
@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('brands') }}">Brands</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Brand</h4>
                    <form class="forms-sample" action="{{ route('brands.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="brandName">Brand Name</label>
                            <input type="text" name="name" class="form-control" id="brandName"
                                   placeholder="Enter Brand Name" required>
                        </div>
                        <div class="form-group">
                            <label for="brandDesc">Description</label>
                            <textarea name="description" class="form-control" id="brandDesc"
                                      rows="3" placeholder="Enter description (optional)"></textarea>
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
