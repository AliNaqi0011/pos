@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Quotations</li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title d-flex justify-content-between align-items-center">
                All Quotations
                <a href="{{ route('quotations.create') }}" class="btn btn-sm btn-primary">+ Add Quotation</a>
            </h4>

            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Quotation Number</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quotations as $quotation)
                            <tr>
                                <td>{{ $loop->iteration + ($quotations->currentPage() - 1) * $quotations->perPage() }}</td>
                                <td>{{ $quotation->quotation_number }}</td>
                                <td>{{ $quotation->customer->name ?? 'N/A' }}</td>
                                <td>
                                    @if ($quotation->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif ($quotation->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $quotation->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('quotations', $quotation->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                        <i class="typcn typcn-eye"></i>
                                    </a>

                                    <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="typcn typcn-edit"></i>
                                    </a>

                                    <form action="{{ route('quotations.delete', $quotation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this quotation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="typcn typcn-delete"></i>
                                        </button>
                                    </form>

                                    @if ($quotation->status === 'pending')
                                        <form action="{{ route('quotations', $quotation->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                <i class="typcn typcn-tick"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('quotations', $quotation->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                                <i class="typcn typcn-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3">No quotations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $quotations->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
