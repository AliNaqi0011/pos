@extends('layouts.main')
@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="main-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories') }}">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
        <div class="main-body">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Category</h4>
                        <form class="forms-sample" action="{{ route('categories.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $category->id }}">
                            <div class="form-group">
                                <label for="categoryName">Category Name</label>
                                <input type="text" name="name" class="form-control" id="categoryName"
                                       value="{{ $category->name }}" required>
                            </div>

                            <div class="form-group">
                                <label for="parentCategory">Parent Category (Optional)</label>
                                <select class="form-control" name="parent_id" id="parentCategory">
                                    <option value="">-- None (Main Category) --</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="categoryDesc">Description</label>
                                <textarea name="description" class="form-control" id="categoryDesc"
                                          rows="3">{{ $category->description }}</textarea>
                            </div>

                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input">
                                    Remember me
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                            <button type="reset" class="btn btn-light">Reset Form</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
