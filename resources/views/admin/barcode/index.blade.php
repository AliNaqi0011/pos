@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Barcode Generator</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title d-flex justify-content-between align-items-center">
                        Barcode Generator
                        <span class="text-muted small">Generate & print barcodes for your products</span>
                    </h4>

                    {{-- Barcode generation form --}}
                    <form action="{{ route('barcodes.print') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label for="product_id" class="form-label">Select Product</label>
                                <select name="product_id" id="product_id" class="form-control" required>
                                    <option value="">-- Choose --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} ({{ $product->barcode }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" min="1" max="100" value="1" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="typcn typcn-printer"></i> Print
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Table preview --}}
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Barcode</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->barcode }}</td>
                                        <td>{!! DNS1D::getBarcodeHTML($product->barcode, 'C128', 1.4, 40) !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
