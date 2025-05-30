@extends('agent.agent_dashboard')
@section('agent')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js
                    /bootstrap-toggle.min.js"></script>

    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
                <a href="{{ route('add.agent') }}" class="btn btn-inverse-info"> Add Agent</a>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Agent All </h6>

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>Sl </th>
                                        <th>Image </th>
                                        <th>Name </th>
                                        <th>Status </th>
                                        <th>Region</th>
                                        <th>Commission %</th>
                                        <th>Limit</th>
                                        <th>Parent Agent</th>
                                        <th>Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allagent as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><img src="{{ !empty($item->photo) ? url('upload/agent_images/' . $item->photo) : url('upload/no_image.jpg') }}"
                                                    style="width:70px; height:40px;"> </td>
                                            <td>{{ $item->name }}</td>

                                            <td>
                                                <span
                                                    class="badge rounded-pill {{ $item->status == 'active' ? 'bg-success' : 'bg-danger' }}">{{ $item->status }}</span>
                                            </td>


                                           <td>{{ $item->region->name ?? 'No Region' }}</td>
                                           <td>{{ $item->commission_rate ?? '-' }}</td>
                                           <td>{{ number_format($item->transfer_limit, 2) ?? '-' }}</td>
                                           <td>{{ $item->parentAgent->name ?? '-' }}</td>
                                            <td>
                                                <a class="btn toggle-class {{ $item->status == 'active' ? 'btn-inverse-success' : 'btn-inverse-danger' }}"
                                                    title="Status" data-id="{{ $item->id }}"
                                                    data-status="{{ $item->status }}">

                                                    <i
                                                        data-feather="{{ $item->status == 'active' ? 'toggle-left' : 'toggle-right' }}"></i>
                                                </a>


                                                <a href="{{ route('edit.agent', $item->id) }}"
                                                    class="btn btn-inverse-warning" title="Edit"> <i
                                                        data-feather="edit"></i> </a>

                                                <a href="{{ route('delete.agent', $item->id) }}"
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


    <script type="text/javascript">
        $(function() {
            $('.toggle-class').click(function() {
                var $this = $(this); // Assign the clicked button to a variable
                var status = $this.attr('data-status'); // Get the current data-status via .attr()
                var user_id = $this.data('id'); // Assuming data-id doesn't need to change dynamically

                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '/changeStatus',
                    data: {
                        'status': status,
                        'user_id': user_id
                    },
                    success: function(data) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        if ($.isEmptyObject(data.error)) {
                            Toast.fire({
                                type: 'success',
                                title: data.success,
                            });

                            // Update button class, icon, and status text based on the returned status
                            var $statusSpan = $this.closest('tr').find(
                            '.status-span'); // Find the status text element

                            if (data.status === 'inactive') {
                                // Update classes for inactive status
                                $this.removeClass('btn-inverse-success').addClass(
                                    'btn-inverse-danger');
                                // Update Feather icon for inactive status
                                $this.find('i').attr('data-feather', 'toggle-right');
                                // Update the status text to 'inactive'
                                $statusSpan.removeClass('bg-success').addClass('bg-danger')
                                    .text('inactive');
                            } else {
                                // Update classes for active status
                                $this.removeClass('btn-inverse-danger').addClass(
                                    'btn-inverse-success');
                                // Update Feather icon for active status
                                $this.find('i').attr('data-feather', 'toggle-left');
                                // Update the status text to 'active'
                                $statusSpan.removeClass('bg-danger').addClass('bg-success')
                                    .text('active');
                            }

                            // Update the button's data-status using .attr()
                            $this.attr('data-status', data.status);

                            // Ensure that Feather icons are re-rendered after the DOM change
                            feather.replace();

                            location.reload();

                        } else {
                            Toast.fire({
                                type: 'error',
                                title: data.error,
                            });
                        }
                    }
                }); // End of Ajax
            });
        });
    </script>

@endsection
