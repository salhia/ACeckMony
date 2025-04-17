@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <button type="button" class="btn btn-inverse-info" data-bs-toggle="modal" data-bs-target="#addModal">
                    Add Category
                </button>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Blog Category All</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Blog Category Name</th>
                                        <th>Blog Category Slug</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($category as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->category_name }}</td>
                                            <td>{{ $item->category_slug }}</td>
                                            <td>
                                                <button type="button" class="btn btn-inverse-warning"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    id="{{ $item->id }}" onclick="categoryEdit(this.id)">
                                                    Edit
                                                </button>
                                                <a href="{{ route('delete.blog.category', $item->id) }}"
                                                    class="btn btn-inverse-danger" id="delete"> Delete </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('store.blog.category') }}" class="forms-sample">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="category_name" class="form-label">Blog Category Name</label>
                            <input type="text" name="category_name" class="form-control" id="category_name">
                            @error('category_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('update.blog.category') }}" class="forms-sample">
                        @csrf
                        <input type="hidden" name="cat_id" id="cat_id">
                        <div class="form-group mb-3">
                            <label for="cat" class="form-label">Blog Category Name</label>
                            <input type="text" name="category_name" class="form-control" id="cat">
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function categoryEdit(id) {
            $.ajax({
                type: 'GET',
                url: '/admin/blog/category/' + id, // Use the admin route for editing
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $('#cat').val(data.category_name); // Populate the form with category name
                    $('#cat_id').val(data.id); // Populate the hidden field with category ID
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
    </script>
@endsection
