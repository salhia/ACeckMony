@extends('agentuser.user_dashboard')
@section('agentuser')

<!-- Add these CSS links in the head section or with your other CSS -->
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

@endpush

<div class="page-content">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">
                        @switch($type)
                            @case('sent')
                                Sent Transfers Report
                                @break
                            @case('received')
                                Received Transfers Report
                                @break
                            @case('commission')
                                Commission Report
                                @break
                            @case('summary')
                                Summary Report
                                @break
                            @default
                                Daily Transactions Report
                        @endswitch
                    </h6>

                    <form id="reportForm" class="mb-4">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" class="form-control" name="start_date" required
                                           value="{{ $type == 'daily' ? date('Y-m-d') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" name="end_date" required
                                           value="{{ $type == 'daily' ? date('Y-m-d') : '' }}">
                                </div>
                            </div>

                            @if($type != 'summary')
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Agent</label>
                                    <select class="form-select" name="agent_id">
                                        <option value="all">All Agents</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            @if($type == 'commission')
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Commission Type</label>
                                    <select class="form-select" name="commission_type">
                                        <option value="all">All Types</option>
                                        <option value="sent">Sending Commission</option>
                                        <option value="received">Receiving Commission</option>
                                    </select>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-{{ $type == 'summary' ? '6' : '3' }}">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block w-100">
                                        <i data-feather="search" class="icon-sm me-2"></i>
                                        Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div id="reportResults">
                        <!-- Results will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Add these script tags after jQuery -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

<script>
$(function() {
    $('#reportForm').on('submit', function(e) {
        e.preventDefault();

        // Show loading indicator
        $('#reportResults').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');

        $.ajax({
            url: '{{ route("agent.reports.data") }}',
            type: 'GET',
            data: $(this).serialize(),
            success: function(response) {
                $('#reportResults').html(response);
                // Reinitialize DataTables
                if ($.fn.DataTable.isDataTable('#transactionsTable')) {
                    $('#transactionsTable').DataTable().destroy();
                }
                initializeDataTable();
            },
            error: function(xhr) {
                let errorMessage = 'Error while fetching data';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.details;
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                $('#reportResults').html(`
                    <div class="alert alert-danger">
                        <i data-feather="alert-circle" class="icon-sm me-2"></i>
                        ${errorMessage}
                    </div>
                `);
                feather.replace();
            }
        });
    });

    function initializeDataTable() {
        $('#transactionsTable').DataTable({
            "order": [[ 7, "desc" ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/English.json"
            },
            dom: '<"dt-buttons"B><"clear">lfrtip',
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn btn-excel',
                    text: '<i class="fas fa-file-excel"></i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-print',
                    text: '<i class="fas fa-print"></i> Print'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-csv',
                    text: '<i class="fas fa-file-csv"></i> CSV'
                }
            ],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            pageLength: 10
        });
    }

    // Auto-submit for daily reports
    if ('{{ $type }}' === 'daily') {
        $('#reportForm').submit();
    }
});
</script>
@endpush
@endsection
