@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <a href="{{ route('add.permission') }}" class="btn btn-inverse-info"> Add Permission </a>
                &nbsp; &nbsp;
                <a href="{{ route('import.permission') }}" class="btn btn-inverse-warning"> Import File </a>
                &nbsp; &nbsp;
                <a href="{{ route('export') }}" class="btn btn-inverse-danger"> Export File </a>
            </ol>
        </nav>

        <div class="row">
            @foreach ($permissions->groupBy('group_name') as $group_name => $grouped_permissions)
                {{-- groupBy() is used to split the permissions into groups based on the group_name field.
                $group_name holds the name of each group (like 'Posts', 'Users', etc.).
                $grouped_permissions is a collection of all permissions within each group. --}}
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Permissions for {{ $group_name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Permission Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($grouped_permissions as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    <a href="{{ route('edit.permission', $item->id) }}"
                                                        class="btn btn-sm btn-outline-warning">
                                                        <i class="mdi mdi-pencil"></i> Edit
                                                    </a>
                                                    <a href="{{ route('delete.permission', $item->id) }}"
                                                        class="btn btn-sm btn-outline-danger" id="delete">
                                                        <i class="mdi mdi-delete"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


    </div>
@endsection
