@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchases</li>
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
                All Purchases
                <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm">+ Create Purchase</a>
            </h4>

            @if ($purchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Reference</th>
                                <th>Warehouse</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->id }}</td>
                                    <td>{{ $purchase->reference_code }}</td>
                                    <td>{{ $purchase->warehouse->name ?? 'N/A' }}</td>
                                    <td>Rs. {{ number_format($purchase->grand_total, 2) }}</td>
                                    <td><span class="badge badge-info">{{ ucfirst($purchase->status) }}</span></td>
                                    <td><span class="badge badge-{{ $purchase->payment_status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($purchase->payment_status) }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.purchases.show', $purchase->id) }}" class="btn btn-sm btn-primary">View</a>
                                        <a href="{{ route('admin.purchases.returnForm', $purchase->id) }}" class="btn btn-sm btn-danger">Return</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $purchases->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="alert alert-info mt-3">No purchases found.</div>
            @endif
        </div>
    </div>
</div>
@endsection
