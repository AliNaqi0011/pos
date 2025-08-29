@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Product</h4>
                    <form class="forms-sample" action="{{ route('products.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">

                        <div class="form-group">
                            <label for="productName">Product Name</label>
                            <input type="text" name="name" class="form-control" id="productName"
                                   value="{{ $product->name }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="productCategory">Category</label>
                            <select name="category_id" class="form-control" id="productCategory" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="productBrand">Brand</label>
                            <select name="brand_id" class="form-control" id="productBrand" required>
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="productDescription">Description</label>
                            <textarea name="description" class="form-control" id="productDescription" rows="3">{{ $product->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="productPrice">Price</label>
                            <input type="number" name="price" class="form-control" id="productPrice" value="{{ $product->price }}" required>
                        </div>

                        <div class="form-group">
                            <label for="productQuantity">Quantity</label>
                            <input type="number" name="quantity" class="form-control" id="productQuantity" value="{{ $product->quantity }}" required>
                        </div>

                        <div class="form-group">
                            <label for="productBarcode">Barcode</label>
                            <input type="text" name="barcode" class="form-control" id="productBarcode" value="{{ $product->barcode }}">
                        </div>

                        <div class="form-group">
                            <label for="productImage">Product Image</label>
                            @if ($product->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" width="150" class="img-thumbnail">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control-file" id="productImage">
                            <small class="text-muted">Leave blank to keep the current image.</small>
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
