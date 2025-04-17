@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <div class="page-content">
        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <button type="button" class="btn btn-inverse-info" data-bs-toggle="modal" data-bs-target="#addModal">
                    Add Role
                </button>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">All Role</h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Roles Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                <button type="button" class="btn btn-inverse-warning"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    id="{{ $item->id }}" onclick="roleEdit(this.id)">
                                                    Edit
                                                </button>
                                                <a href="{{ route('delete.role', $item->id) }}"
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
                    <h5 class="modal-title" id="addModalLabel">Add Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('store.role') }}" class="forms-sample">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Role Name</label>
                            <input type="text" name="name" class="form-control" id="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Add Role</button>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('update.role') }}" class="forms-sample">
                        @csrf
                        <input type="hidden" name="role_id" id="role_id">
                        <div class="form-group mb-3">
                            <label for="role" class="form-label">Blog Role</label>
                            <input type="text" name="name" class="form-control" id="role">
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function roleEdit(id) {
            $.ajax({
                type: 'GET',
                url: '/edit/role/' + id, // Use the admin route for editing
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $('#role').val(data.name); // Populate the form with category name
                    $('#role_id').val(data.id); // Populate the hidden field with category ID
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
    </script>
@endsection
