@extends('layouts.main')
@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="main-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('quotations') }}">Quotations</a></li>
                <li class="breadcrumb-item active" aria-current="page">Listing</li>
            </ol>
        </nav>
    <div class="container">
        <h2>Quotation Details</h2>

        <p><strong>Customer:</strong> {{ $quotation->customer->name }}</p>
        <p><strong>Date:</strong> {{ $quotation->quotation_date }}</p>
        <p><strong>Status:</strong> {{ ucfirst($quotation->status) }}</p>
        <p><strong>Total:</strong> {{ number_format($quotation->total, 2) }}</p>

        <h4>Items:</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
