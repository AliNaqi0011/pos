@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Products Report</h4>
                    <div>
                        <a href="{{ route('reports.products', ['export' => 'csv']) }}" class="btn btn-success btn-sm">
                            <i class="typcn typcn-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3>{{ $products->count() }}</h3>
                                <p>Total Products</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3>{{ $low_stock->count() }}</h3>
                                <p>Low Stock</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3>{{ $out_of_stock->count() }}</h3>
                                <p>Out of Stock</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>${{ number_format($product->sale_price, 2) }}</td>
                                <td>
                                    @if($product->quantity == 0)
                                        <span class="badge badge-danger">Out of Stock</span>
                                    @elseif($product->quantity < 10)
                                        <span class="badge badge-warning">Low Stock</span>
                                    @else
                                        <span class="badge badge-success">In Stock</span>
                                    @endif
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
@endsection