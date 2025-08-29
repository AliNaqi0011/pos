@extends('layouts.main')

@section('content')
<div class="container">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="main-breadcrumb mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    {{-- Main Content --}}
    <div class="main-body">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Expense Category</h4>

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
                    <form action="{{ route('expense-categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="categoryName">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   id="categoryName"
                                   value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="categoryDescription">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      id="categoryDescription" rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Category</button>
                            <button type="reset" class="btn btn-light">Reset</button>
                            <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary float-right">Back to List</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
