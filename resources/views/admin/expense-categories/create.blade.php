@extends('layouts.main')

@section('content')
<div class="container">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>

    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Expense Category</h4>

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

                    {{-- Create Form --}}
                    <form class="forms-sample" action="{{ route('expense-categories.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="categoryName">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" id="categoryName"
                                   placeholder="Enter Category Name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="categoryDescription">Description</label>
                            <textarea name="description" class="form-control" id="categoryDescription"
                                      rows="3" placeholder="Enter description (optional)">{{ old('description') }}</textarea>
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
