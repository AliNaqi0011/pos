@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Customers</li>
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
                All Customers
                <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">+ Add Customer</a>
            </h4>

            @if ($customers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email ?? '—' }}</td>
                                    <td>{{ $customer->phone ?? '—' }}</td>
                                    <td>{{ Str::limit($customer->address, 40) }}</td>
                                    <td>
                                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="{{ route('customers.delete', $customer->id) }}" class="btn btn-sm btn-danger">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $customers->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="alert alert-info mt-3">No customers found.</div>
            @endif
        </div>
    </div>
</div>

<style>
    .relative.inline-flex.items-center.px-4.py-2.text-sm {
        font-size: 0.75rem !important;
        padding: 0.25rem 0.5rem !important;
    }

    .relative.inline-flex.items-center.px-2.py-2 svg {
        width: 1rem !important;
        height: 1rem !important;
    }

    .relative.inline-flex.items-center.px-2.py-2 {
        padding: 0.25rem 0.5rem !important;
    }
</style>
@endsection
