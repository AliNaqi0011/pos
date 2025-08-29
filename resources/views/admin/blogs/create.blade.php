@extends('layouts.main')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="main-breadcrumb mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Blog</li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title d-flex justify-content-between align-items-center">
                Blog Create
                <a href="{{ route('user.settings') }}" class="btn btn-sm btn-secondary">Back to Blog List</a>
            </h4>

            <form action="{{ route('blog.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Enter blog title" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="slug" class="form-control" placeholder="Enter slug" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tags" class="form-label">Meta Tags</label>
                        <input type="text" name="tags" id="tags" class="form-control" placeholder="Enter meta tags (comma separated)">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="editor" class="form-label">Meta Description</label>
                        <div id="editor" class="border rounded" style="min-height: 150px;"></div>
                        <input type="hidden" name="description" id="editorContent">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">Create</button>
                    <button type="reset" class="btn btn-light">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .then(editor => {
            editor.model.document.on('change:data', () => {
                document.getElementById('editorContent').value = editor.getData();
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endsection
