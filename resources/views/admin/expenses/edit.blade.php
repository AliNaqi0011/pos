@extends('layouts.main')

@section('content')
<div class="container">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="main-breadcrumb mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expenses.') }}">Expenses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    {{-- Main Content --}}
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Expense</h4>

                    {{-- Error Handling --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Edit Form --}}
                    <form action="{{ route('expenses.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $expense->id }}">

                        <div class="form-group">
                            <label for="expenseTitle">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" id="expenseTitle"
                                   value="{{ old('title', $expense->title) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="expenseCategory">Category</label>
                            <select name="expense_category_id" class="form-control" id="expenseCategory">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $expense->expense_category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" id="amount"
                                   value="{{ old('amount', $expense->amount) }}" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="expenseDate">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" id="expenseDate"
                                   value="{{ old('date', $expense->date ? $expense->date->format('Y-m-d') : '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="expenseNote">Note</label>
                            <textarea name="note" class="form-control" id="expenseNote" rows="3">{{ old('note', $expense->note) }}</textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Expense</button>
                            <button type="reset" class="btn btn-light">Reset</button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary float-right">Back to List</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
