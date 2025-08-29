@extends('layouts.main')

@section('content')
<div class="container">
    {{-- Breadcrumb navigation --}}
    <nav aria-label="breadcrumb" class="main-breadcrumb mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Expense Categories</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Expense Categories Table</h4>

                    {{-- Search box --}}
                    <div class="mb-4" style="max-width: auto;">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="typcn typcn-zoom"></i>
                            </span>
                            <input 
                                type="search" 
                                id="search-box" 
                                class="form-control border-start-0" 
                                placeholder="Search expense categories..."
                                aria-label="Search expense categories"
                                autocomplete="off"
                                value="{{ $search ?? '' }}"
                            >
                            <button 
                                class="btn btn-outline-secondary d-none" 
                                type="button" 
                                id="clear-search" 
                                aria-label="Clear search"
                                title="Clear search"
                            >&times;</button>
                        </div>
                    </div>

                    {{-- Expense Categories table container --}}
                    <div id="expense-categories-table-container">
                        {{-- Initial Server Rendered Table --}}
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    @php
                                        $currentSort = $sortBy ?? 'id';
                                        $currentOrder = $sortOrder ?? 'desc';

                                        function sortIcon($col, $currentSort, $currentOrder) {
                                            if ($col === $currentSort) {
                                                return $currentOrder === 'asc' ? '&#9650;' : '&#9660;';
                                            }
                                            return '';
                                        }
                                    @endphp
                                    <th><a href="#" class="sort" data-sort="id"># {!! sortIcon('id', $currentSort, $currentOrder) !!}</a></th>
                                    <th><a href="#" class="sort" data-sort="name">Name {!! sortIcon('name', $currentSort, $currentOrder) !!}</a></th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenseCategories as $index => $category)
                                    <tr>
                                        <td>{{ $loop->iteration + ($expenseCategories->currentPage() - 1) * $expenseCategories->perPage() }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($category->description, 40) }}</td>
                                        <td>
                                            <a href="{{ route('expense-categories.delete', $category->id) }}" class="text-danger" aria-label="Delete expense category {{ $category->name }}" title="Delete">
                                                <i class="typcn typcn-archive"></i>
                                            </a>
                                            &nbsp;&nbsp;
                                            <a href="{{ route('expense-categories.edit', $category->id) }}" class="text-primary" aria-label="Edit expense category {{ $category->name }}" title="Edit">
                                                <i class="typcn typcn-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">No expense categories found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        {{ $expenseCategories->appends(['search' => $search ?? ''])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- jQuery CDN --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function() {
    let sortBy = "{{ $sortBy ?? 'id' }}";
    let sortOrder = "{{ $sortOrder ?? 'desc' }}";

    function toggleClearButton() {
        $('#clear-search').toggleClass('d-none', $('#search-box').val().length === 0);
    }

    function getSortIcon(column, currentSort, currentOrder) {
        if (column === currentSort) {
            return currentOrder === 'asc' ? '&#9650;' : '&#9660;';
        }
        return '';
    }

    function renderTable(data) {
        let html = `
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><a href="#" class="sort" data-sort="id"># ${getSortIcon('id', data.sort_by, data.sort_order)}</a></th>
                        <th><a href="#" class="sort" data-sort="name">Name ${getSortIcon('name', data.sort_by, data.sort_order)}</a></th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
        `;

        if (data.expenseCategories.length === 0) {
            html += `<tr><td colspan="4" class="text-center py-3">No expense categories found.</td></tr>`;
        } else {
            data.expenseCategories.forEach((category, i) => {
                const rowIndex = i + 1 + (data.current_page - 1) * data.per_page;
                const description = category.description ? category.description.substring(0, 40) : '';

                html += `
                    <tr>
                        <td>${rowIndex}</td>
                        <td>${category.name}</td>
                        <td>${description}</td>
                        <td>
                            <a href="/expense-categories/delete/${category.id}" class="text-danger" aria-label="Delete expense category ${category.name}" title="Delete">
                                <i class="typcn typcn-archive"></i>
                            </a>
                            &nbsp;&nbsp;
                            <a href="/expense-categories/edit/${category.id}" class="text-primary" aria-label="Edit expense category ${category.name}" title="Edit">
                                <i class="typcn typcn-edit"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });
        }

        html += `</tbody></table>`;
        html += data.pagination;

        $('#expense-categories-table-container').html(html);
        toggleClearButton();
    }

    function loadExpenseCategories(page = 1) {
        const search = $('#search-box').val();

        $.ajax({
            url: "{{ route('expense-categories') }}",
            type: "GET",
            data: {
                page: page,
                sort_by: sortBy,
                sort_order: sortOrder,
                search: search,
            },
            beforeSend() {
                $('#expense-categories-table-container').html('<p class="text-center py-4">Loading...</p>');
            },
            success(response) {
                renderTable(response);
            },
            error() {
                $('#expense-categories-table-container').html('<p class="text-danger text-center py-4">Error loading expense categories. Please try again.</p>');
            }
        });
    }

    // Pagination link click
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const urlParams = new URLSearchParams(href.split('?')[1]);
        const page = urlParams.get('page') || 1;
        loadExpenseCategories(page);
    });

    // Sort column click
    $(document).on('click', '.sort', function(e) {
        e.preventDefault();
        const column = $(this).data('sort');
        if (sortBy === column) {
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy = column;
            sortOrder = 'asc';
        }
        loadExpenseCategories();
    });

    // Search input event
    $('#search-box').on('input', function() {
        toggleClearButton();
        loadExpenseCategories();
    });

    // Clear search button click
    $('#clear-search').on('click', function() {
        $('#search-box').val('');
        toggleClearButton();
        loadExpenseCategories();
    });

    // Initialize clear button visibility
    toggleClearButton();
});
</script>
<style>
    /* Reduce pagination text size */
    .relative.inline-flex.items-center.px-4.py-2.text-sm {
        font-size: 0.75rem !important; /* smaller text */
        padding: 0.25rem 0.5rem !important; /* smaller padding */
    }

    /* Reduce size of the SVG arrows */
    .relative.inline-flex.items-center.px-2.py-2 svg {
        width: 1rem !important;  /* 16px */
        height: 1rem !important; /* 16px */
    }

    /* Reduce padding for arrow buttons */
    .relative.inline-flex.items-center.px-2.py-2 {
        padding: 0.25rem 0.5rem !important;
    }
</style>
@endsection
