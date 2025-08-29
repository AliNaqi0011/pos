@extends('layouts.main')
@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">Listing</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Products Table</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($product->image && file_exists(public_path('storage/' . $product->image)))
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" width="50" height="50" style="object-fit: cover;">
                                            @else
                                            <img src="{{ asset('images/no-image.png') }}" alt="No Image" width="50" height="50">
                                        @endif
                                        </td>
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>${{ $product->price }}</td>
                                        <td>{{ $product->quantity }}</td>
                                        <td>
                                            <a href="{{ route('products.delete', $product->id) }}" title="Delete">
                                                <i class="typcn typcn-archive text-danger"></i>
                                            </a>
                                            &nbsp;&nbsp;
                                            <a href="{{ route('products.edit', $product->id) }}" title="Edit">
                                                <i class="typcn typcn-edit text-primary"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    /* Reduce pagination text size */
    .relative.inline-flex.items-center.px-4.py-2.text-sm {
        font-size: 0.75rem !important; /* smaller text */
        padding: 0.25rem 0.5rem !important; /* smaller padding */
    }

    /* Reduce size of the SVG arrows */
    .relative.inline-flex.items-center.px-2.py-2 svg {
        width: 1rem !important;  /* 16px */
        height: 1rem !important; /* 16px */
    }

    /* Reduce padding for arrow buttons */
    .relative.inline-flex.items-center.px-2.py-2 {
        padding: 0.25rem 0.5rem !important;
    }
</style>
@endsection
