@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Brands</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title d-flex justify-content-between align-items-center">
                        All Brands
                        <a href="{{ route('brands.create') }}" class="btn btn-sm btn-primary">+ Add Brand</a>
                    </h4>

                    {{-- Search (commented out) --}}
                    {{--
                    <div class="mb-3" style="max-width: 400px;">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="typcn typcn-zoom"></i>
                            </span>
                            <input 
                                type="search" 
                                id="search" 
                                class="form-control border-start-0" 
                                placeholder="Search brands..." 
                                value="{{ $search ?? '' }}"
                                autocomplete="off"
                            >
                            <button class="btn btn-outline-secondary d-none" type="button" id="clear-search">&times;</button>
                        </div>
                    </div>
                    --}}

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="brand-data">
                                @forelse ($brands as $brand)
                                    <tr>
                                        <td>{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td>{{ Str::limit($brand->description, 40) }}</td>
                                        <td>
                                            <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="typcn typcn-edit"></i>
                                            </a>
                                            &nbsp;
                                            <form action="{{ route('brands.delete', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="typcn typcn-archive"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No brands found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3" id="pagination-links">
                            {{ $brands->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
@push('scripts')
<script>
    let sortBy = "{{ $sortBy ?? 'id' }}";
    let sortOrder = "{{ $sortOrder ?? 'desc' }}";

    // -- Search & Sort features (optional, frontend commented)
    /*
    function fetchBrands(page = 1) {
        const search = $('#search').val();
        $.ajax({
            url: "{{ route('brands') }}",
            type: 'GET',
            data: {
                search: search,
                sort_by: sortBy,
                sort_order: sortOrder,
                page: page
            },
            success: function (response) {
                $('#brand-data').html(response.tableRows);
                $('#pagination-links').html(response.pagination);
            }
        });
    }

    $('#search').on('input', function () {
        fetchBrands();
    });

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        fetchBrands(page);
    });

    $(document).on('click', '.sort-link', function (e) {
        e.preventDefault();
        sortBy = $(this).data('sort');
        sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        fetchBrands();
    });
    */
</script>
<style>
    .pagination .page-link {
        font-size: 0.8rem !important;
        padding: 0.25rem 0.5rem !important;
    }
</style>
@endpush
@endsection
