@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Warehouses</li>
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
                All Warehouses
                <a href="{{ route('warehouses.create') }}" class="btn btn-primary btn-sm">+ Add Warehouse</a>
            </h4>

            @if ($warehouses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ $warehouse->location ?? '-' }}</td>
                                    <td>{{ Str::limit($warehouse->description, 40) ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('warehouses.edit', $warehouse->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                        <form action="{{ route('warehouses.delete', $warehouse->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this warehouse?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $warehouses->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="alert alert-info mt-3">No warehouses found.</div>
            @endif
        </div>
    </div>
</div>
@endsection
