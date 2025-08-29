@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sales</li>
        </ol>
    </nav>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->has('error'))
        <div class="alert alert-danger">{{ $errors->first('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <h4 class="card-title d-flex justify-content-between align-items-center">
                All Sales
                <a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm">+ Create Sale</a>
            </h4>

            @if ($sales->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Salesperson</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>#{{ $sale->id }}</td>
                                    <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                    <td>Rs. {{ number_format($sale->final_total, 2) }}</td>
                                    <td><span class="badge badge-info">{{ ucfirst($sale->status) }}</span></td>
                                    <td><span class="badge badge-{{ $sale->payment_status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($sale->payment_status) }}</span></td>
                                    <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn btn-sm btn-primary">View</a>
                                        <a href="{{ route('admin.sales.returnForm', $sale->id) }}" class="btn btn-sm btn-danger">Return</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $sales->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="alert alert-info mt-3">No sales found.</div>
            @endif
        </div>
    </div>
</div>
@endsection
