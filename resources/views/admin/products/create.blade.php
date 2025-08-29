@extends('layouts.main')
@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Product</h4>
                    <form class="forms-sample" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productName">Product Name</label>
                                    <input type="text" name="name" class="form-control" id="productName" placeholder="Enter Product Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productCategory">Category</label>
                                    <select name="category_id" class="form-control" id="productCategory" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productBrand">Brand</label>
                                    <select name="brand_id" class="form-control" id="productBrand" required>
                                        <option value="">Select Brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productDescription">Description</label>
                                    <textarea name="description" class="form-control" id="productDescription" rows="3" placeholder="Enter description (optional)"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="productPrice">Price</label>
                                <input type="number" step="0.01" name="price" class="form-control" id="productPrice" placeholder="Enter Price" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="productCostPrice">Cost Price</label>
                                    <input type="number" step="0.01" name="cost_price" class="form-control" id="productCostPrice" placeholder="Enter Cost Price" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="productSalePrice">Sale Price</label>
                                    <input type="number" step="0.01" name="sale_price" class="form-control" id="productSalePrice" placeholder="Enter Sale Price" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="productDiscountPrice">Discount Price</label>
                                    <input type="number" step="0.01" name="discount_price" class="form-control" id="productDiscountPrice" placeholder="Enter Discount Price (optional)">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productBarcode">Barcode</label>
                                    <input type="text" name="barcode" class="form-control" id="productBarcode" placeholder="Enter Barcode (optional)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productQuantity">Quantity</label>
                                    <input type="number" name="quantity" class="form-control" id="productQuantity" placeholder="Enter Quantity" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productStockAlertLevel">Stock Alert Level</label>
                                    <input type="number" name="stock_alert_level" class="form-control" id="productStockAlertLevel" placeholder="Enter Stock Alert Level" value="10">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productWarehouse">Warehouse</label>
                                    <select name="warehouse_id" class="form-control" id="productWarehouse">
                                        <option value="">Select Warehouse (optional)</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productImage">Product Image</label>
                                    <input type="file" name="image" class="form-control-file" id="productImage" accept="image/*" onchange="previewImage(event)">
                                    <small class="text-muted">Accepted formats: jpeg, png, jpg (Max: 2MB)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Preview</label><br>
                                <img id="imagePreview" src="#" alt="Image Preview" style="max-height: 200px; display: none; border: 1px solid #ccc; padding: 4px;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <button type="reset" class="btn btn-light" onclick="resetPreview()">Reset Form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function resetPreview() {
        const output = document.getElementById('imagePreview');
        output.src = "#";
        output.style.display = "none";
    }
</script>
@endpush
