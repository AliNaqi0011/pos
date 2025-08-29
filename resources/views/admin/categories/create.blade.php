@extends('layouts.main')
@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories') }}">Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
    <div class="main-body">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Category</h4>
                    <form class="forms-sample" action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="categoryName">Category Name</label>
                            <input type="text" name="name" class="form-control" id="categoryName"
                                   placeholder="Enter Category Name" required>
                        </div>
                        <div class="form-group">
                            <label for="parentCategory">Parent Category (Optional)</label>
                            <select class="form-control" name="parent_id" id="parentCategory">
                                <option value="">-- None (Main Category) --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="categoryDesc">Description</label>
                            <textarea name="description" class="form-control" id="categoryDesc"
                                      rows="3" placeholder="Enter description (optional)"></textarea>
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
