@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Return</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Process Sale Return for Sale #{{ $sale->id }}</h4>

                    <form action="{{ route('admin.sales.processReturn', $sale->id) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="return_date">Return Date</label>
                            <input type="date" class="form-control" name="return_date" id="return_date" value="{{ old('return_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="reason">Reason for Return</label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                        </div>

                        <h5>Return Items</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sold Quantity</th>
                                    <th>Return Quantity</th>
                                    <th>Price</th>
                                    <th>Total Return Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->saleItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            <input 
                                                type="number" 
                                                name="return_items[{{ $item->id }}][quantity]" 
                                                class="form-control" 
                                                min="0" 
                                                max="{{ $item->quantity }}" 
                                                value="0"
                                            >
                                        </td>
                                        <td>${{ number_format($item->product_price, 2) }}</td>
                                        <td>
                                            <input 
                                                type="number" 
                                                name="return_items[{{ $item->id }}][total]" 
                                                class="form-control" 
                                                min="0" 
                                                step="0.01" 
                                                value="0"
                                            >
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-danger">Submit Return</button>
                        <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
