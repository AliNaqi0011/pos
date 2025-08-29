@extends('layouts.main')

@section('content')
<div class="container">
    {{-- Breadcrumb navigation --}}
    <nav aria-label="breadcrumb" class="main-breadcrumb mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Expenses</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Expenses Table</h4>

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
                                placeholder="Search expenses..."
                                autocomplete="off"
                                value="{{ $search ?? '' }}"
                            >
                            <button 
                                class="btn btn-outline-secondary d-none" 
                                type="button" 
                                id="clear-search" 
                                title="Clear search"
                            >&times;</button>
                        </div>
                    </div>

                    {{-- Expenses table --}}
                    <div id="expenses-table-container">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="#" class="sort" data-sort="id">
                                            # {!! ($currentSort ?? 'id') === 'id' ? (($currentOrder ?? 'desc') === 'asc' ? '&#9650;' : '&#9660;') : '' !!}
                                        </a>
                                    </th>
                                    <th>
                                        <a href="#" class="sort" data-sort="title">
                                            Title {!! ($currentSort ?? '') === 'title' ? (($currentOrder ?? 'desc') === 'asc' ? '&#9650;' : '&#9660;') : '' !!}
                                        </a>
                                    </th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenses as $index => $expense)
                                    <tr>
                                        <td>{{ $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage() }}</td>
                                        <td>{{ $expense->title }}</td>
                                        <td>{{ number_format($expense->amount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('d M, Y') }}</td>
                                        <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($expense->description, 40) }}</td>
                                        <td>
                                            <a href="{{ route('expenses.delete', $expense->id) }}" class="text-danger" title="Delete">
                                                <i class="typcn typcn-archive"></i>
                                            </a>
                                            &nbsp;&nbsp;
                                            <a href="{{ route('expenses.edit', $expense->id) }}" class="text-primary" title="Edit">
                                                <i class="typcn typcn-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center py-3">No expenses found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        {{ $expenses->appends(['search' => $search ?? ''])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- jQuery --}}
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
                        <th><a href="#" class="sort" data-sort="title">Title ${getSortIcon('title', data.sort_by, data.sort_order)}</a></th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
        `;

        if (data.expenses.length === 0) {
            html += `<tr><td colspan="7" class="text-center py-3">No expenses found.</td></tr>`;
        } else {
            data.expenses.forEach((expense, i) => {
                const rowIndex = i + 1 + (data.current_page - 1) * data.per_page;
                const description = expense.description ? expense.description.substring(0, 40) : '';
                const category = expense.category?.name ?? 'N/A';
                const formattedDate = new Date(expense.date).toLocaleDateString('en-GB', {
                    day: '2-digit', month: 'short', year: 'numeric'
                });

                html += `
                    <tr>
                        <td>${rowIndex}</td>
                        <td>${expense.title}</td>
                        <td>${parseFloat(expense.amount).toFixed(2)}</td>
                        <td>${formattedDate}</td>
                        <td>${category}</td>
                        <td>${description}</td>
                        <td>
                            <a href="/expenses/delete/${expense.id}" class="text-danger" title="Delete">
                                <i class="typcn typcn-archive"></i>
                            </a>
                            &nbsp;&nbsp;
                            <a href="/expenses/edit/${expense.id}" class="text-primary" title="Edit">
                                <i class="typcn typcn-edit"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });
        }

        html += `</tbody></table>`;
        html += data.pagination;

        $('#expenses-table-container').html(html);
        toggleClearButton();
    }

    function loadExpenses(page = 1) {
        const search = $('#search-box').val();

        $.ajax({
            url: "{{ route('expenses') }}",
            type: "GET",
            data: {
                page: page,
                sort_by: sortBy,
                sort_order: sortOrder,
                search: search,
            },
            beforeSend() {
                $('#expenses-table-container').html('<p class="text-center py-4">Loading...</p>');
            },
            success(response) {
                renderTable(response);
            },
            error() {
                $('#expenses-table-container').html('<p class="text-danger text-center py-4">Error loading expenses.</p>');
            }
        });
    }

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'));
        const page = url.searchParams.get('page');
        if (page) loadExpenses(page);
    });

    $(document).on('click', '.sort', function(e) {
        e.preventDefault();
        const newSort = $(this).data('sort');
        if (sortBy === newSort) {
            sortOrder = (sortOrder === 'asc') ? 'desc' : 'asc';
        } else {
            sortBy = newSort;
            sortOrder = 'asc';
        }
        loadExpenses(1);
    });

    let typingTimer;
    const typingDelay = 500;

    $('#search-box').on('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => loadExpenses(1), typingDelay);
        toggleClearButton();
    });

    $('#search-box').on('keydown', function() {
        clearTimeout(typingTimer);
    });

    $('#clear-search').on('click', function() {
        $('#search-box').val('');
        toggleClearButton();
        loadExpenses(1);
        $('#search-box').focus();
    });

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
