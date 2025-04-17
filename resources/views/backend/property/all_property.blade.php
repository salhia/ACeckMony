@extends('admin.admin_dashboard')

@section('admin')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <a href="{{ route('add.property') }}" class="btn btn-inverse-info"> Add Property </a>
            </ol>
        </nav>

        @php
            $id = Auth::user()->id;
            $profileData = App\Models\User::find($id);
        @endphp

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Property All </h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Code</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Agent</th>
                                        <th>P Type</th>
                                        <th>S Type</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Status</th>
                                        {{-- <th>Update Details</th> --}}
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($property as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->property_code }}</td>
                                            <td><img src="{{ asset($item->property_thambnail) }}"
                                                    style="width:70px; height:40px;"> </td>
                                            <td>{{ $item->property_name }}</td>
                                            <td>{{ $item['user']['name'] ?? 'N/A' }}</td>

                                            <td>{{ $item['type']['type_name'] }}</td>
                                            {{-- ['type is a function of Property MODEL']['type_name is property_types column name'] --}}

                                            <td>{{ $item->property_status }}</td>
                                            <td>{{ $item->city ?? 'N/A' }}</td>
                                            <td>{{ $item['pstate']['state_name'] ?? 'N/A' }}</td>
                                            <td>
                                                @if ($item->status == 1)
                                                    <span class="badge rounded-pill bg-success">Active</span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger">InActive</span>
                                                @endif
                                            </td>
                                            {{-- <td>
                                                @if ($item->updated_at != null)
                                                    {{ $item->updated_at }} ({{ $profileData->name }})
                                                @else
                                                    Not Updated Yet
                                                @endif
                                            </td> --}}
                                            {{-- <td>{{ $item->updated_at }} (BY {{ $profileData->name }}) </td> --}}
                                            <td>
                                                <a href="{{ route('details.property', $item->id) }}"
                                                    class="btn btn-inverse-info" title="Details"> <i data-feather="eye"></i>
                                                </a>

                                                <a href="{{ route('edit.property', $item->id) }}"
                                                    class="btn btn-inverse-warning" title="Edit"> <i
                                                        data-feather="edit"></i> </a>

                                                <a href="{{ route('delete.property', $item->id) }}"
                                                    class="btn btn-inverse-danger" id="delete" title="Delete"> <i
                                                        data-feather="trash-2"></i> </a>
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
@endsection
