@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Categories</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title d-flex justify-content-between align-items-center">
                        All Categories
                        <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary">+ Add Category</a>
                    </h4>

                    {{-- Search box (commented) --}}
                    {{--
                    <div class="mb-3" style="max-width: 400px;">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="typcn typcn-zoom"></i>
                            </span>
                            <input 
                                type="search" 
                                id="search-box" 
                                class="form-control border-start-0" 
                                placeholder="Search..." 
                                value="{{ $search ?? '' }}"
                                autocomplete="off"
                            >
                            <button class="btn btn-outline-secondary d-none" type="button" id="clear-search">&times;</button>
                        </div>
                    </div>
                    --}}

                    <div id="categories-table-container">
                        @php
                            $currentSort = $sortBy ?? 'id';
                            $currentOrder = $sortOrder ?? 'desc';

                            $sortIcon = function ($col) use ($currentSort, $currentOrder) {
                                return $col === $currentSort ? ($currentOrder === 'asc' ? '↑' : '↓') : '';
                            };
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th><a href="#" class="sort text-white" data-sort="id"># {!! $sortIcon('id') !!}</a></th>
                                        <th><a href="#" class="sort text-white" data-sort="name">Name {!! $sortIcon('name') !!}</a></th>
                                        <th class="text-white">Parent</th>
                                        <th class="text-white">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $index => $category)
                                        <tr>
                                            <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $category->parent->name ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="typcn typcn-edit"></i>
                                                </a>
                                                &nbsp;
                                                <form action="{{ route('categories.delete', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
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
                                            <td colspan="4" class="text-center">No categories found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $categories->appends(['search' => $search ?? '', 'sort_by' => $sortBy ?? 'id', 'sort_order' => $sortOrder ?? 'desc'])->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function () {
    let sortBy = "{{ $sortBy ?? 'id' }}";
    let sortOrder = "{{ $sortOrder ?? 'desc' }}";

    function loadCategories(page = 1) {
        $.ajax({
            url: "{{ route('categories') }}",
            method: 'GET',
            data: {
                page: page,
                sort_by: sortBy,
                sort_order: sortOrder
            },
            beforeSend: function () {
                $('#categories-table-container').html('<div class="text-center py-4">Loading...</div>');
            },
            success: function (res) {
                const newHtml = $(res).find('#categories-table-container').html();
                $('#categories-table-container').html(newHtml);
            },
            error: function () {
                $('#categories-table-container').html('<div class="text-danger text-center py-4">Error loading categories</div>');
            }
        });
    }

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        const page = new URL($(this).attr('href')).searchParams.get('page');
        loadCategories(page);
    });

    $(document).on('click', '.sort', function (e) {
        e.preventDefault();
        const newSort = $(this).data('sort');
        if (sortBy === newSort) {
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy = newSort;
            sortOrder = 'asc';
        }
        loadCategories(1);
    });
});
</script>

<style>
    .pagination .page-link {
        font-size: 0.8rem !important;
        padding: 0.25rem 0.5rem !important;
    }
</style>
@endsection
