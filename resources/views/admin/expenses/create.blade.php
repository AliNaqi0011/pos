@extends('layouts.main')

@section('content')
<div class="container">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('expenses') }}">Expenses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Expense</h4>

                    {{-- Show Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form class="forms-sample" action="{{ route('expenses.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="expenseTitle">Expense Title</label>
                            <input type="text" name="title" class="form-control" id="expenseTitle"
                                   placeholder="Enter Expense Title" required value="{{ old('title') }}">
                        </div>

                        <div class="form-group">
                            <label for="expenseWarehouse">Warehouse</label>
                            <select class="form-control" name="warehouse_id" id="expenseWarehouse" required>
                                <option value="">-- Select Warehouse --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="expenseCategory">Expense Category</label>
                            <select class="form-control" name="expense_category_id" id="expenseCategory" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" name="amount" class="form-control" id="amount"
                                   placeholder="Enter Amount" step="0.01" required value="{{ old('amount') }}">
                        </div>

                        <div class="form-group">
                            <label for="expenseDate">Date</label>
                            <input type="date" name="date" class="form-control" id="expenseDate" required value="{{ old('date') }}">
                        </div>

                        <div class="form-group">
                            <label for="expenseDetails">Note</label>
                            <textarea name="details" class="form-control" id="expenseDetails"
                                      rows="3" placeholder="Enter note (optional)">{{ old('details') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <button type="reset" class="btn btn-light">Reset Form</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
