@extends('agentuser.user_dashboard')
@section('agentuser')

<!-- Add these CSS links in the head section or with your other CSS -->
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<style>
    /* Main container styling */
    .page-content {
        background-color: #1a2234;
        padding: 20px;
        min-height: 100vh;
    }

    /* Card styling with enhanced shadow */
    .card {
        background-color: #2a3447;
        border: none;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        margin-bottom: 1rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .card h6.card-title {
        color: #8a94a6;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .card h3 {
        color: #ffffff;
        font-size: 1.5rem;
        margin: 0;
    }

    /* Table styling */
    .table-responsive {
        background-color: #2a3447;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
    }

    #transactionsTable {
        color: #ffffff;
        border: none;
        margin: 0 !important;
    }

    #transactionsTable thead th {
        background-color: #2a3447;
        color: #8a94a6;
        border: none;
        padding: 15px;
        font-weight: 600;
        border-bottom: 2px solid #3a4459;
    }

    #transactionsTable tbody tr {
        background-color: #2a3447 !important;
        border-bottom: 1px solid #3a4459;
    }

    /* Remove white background from even/odd rows */
    #transactionsTable tbody tr:nth-of-type(odd),
    #transactionsTable tbody tr:nth-of-type(even) {
        background-color: #2a3447 !important;
    }

    #transactionsTable tbody tr:hover {
        background-color: #323c52 !important;
    }

    #transactionsTable td {
        padding: 12px 15px;
        border: none;
        color: #ffffff;
    }

    /* DataTable controls styling */
    .dataTables_wrapper {
        background-color: #2a3447;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
    }

    .dataTables_length,
    .dataTables_filter {
        color: #8a94a6;
        margin-bottom: 1rem;
    }

    .dataTables_length select,
    .dataTables_filter input {
        background-color: #323c52;
        border: 1px solid #3a4459;
        color: #ffffff;
        border-radius: 6px;
        padding: 6px 10px;
    }

    /* Pagination styling */
    .dataTables_paginate {
        padding-top: 1rem;
    }

    .paginate_button {
        padding: 5px 10px;
        margin: 0 3px;
        color: #ffffff !important;
        background-color: #323c52 !important;
        border: none !important;
        border-radius: 6px;
    }

    .paginate_button.current {
        background-color: #0d6efd !important;
        color: #ffffff !important;
    }

    /* Button container styling */
    .dt-buttons {
        background-color: #2a3447 !important;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
    }

    /* Override any white backgrounds */
    .stripe,
    .odd,
    .even {
        background-color: #2a3447 !important;
    }

    /* Force dark background on all table elements */
    table.dataTable tbody tr {
        background-color: #2a3447 !important;
    }

    /* Remove any default white backgrounds */
    .table > :not(caption) > * > * {
        background-color: #2a3447 !important;
    }

    /* Button styling */
    .dt-buttons .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 14px;
        border-radius: 8px;
        border: none;
        transition: all 0.3s ease;
    }

    /* Specific button colors */
    .dt-buttons .btn-excel {
        background-color: #217346;
        color: white;
    }

    .dt-buttons .btn-pdf {
        background-color: #ff3e3e;
        color: white;
    }

    .dt-buttons .btn-print {
        background-color: #0d6efd;
        color: white;
    }

    .dt-buttons .btn-csv {
        background-color: #ff9800;
        color: white;
    }

    /* Button hover effects */
    .dt-buttons .btn:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }

    /* Status badge styling */
    .badge {
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 500;
    }

    /* Search input styling */
    .dataTables_filter {
        margin-bottom: 1rem;
    }

    .dataTables_filter label {
        color: #8a94a6;
        font-weight: 500;
    }

    .dataTables_filter input {
        background-color: #ffffff !important;
        border: 1px solid #3a4459;
        color: #1a2234 !important;
        border-radius: 6px;
        padding: 8px 12px;
        margin-left: 8px;
        width: 250px;
        font-size: 14px;
    }

    .dataTables_filter input::placeholder {
        color: #6c757d;
    }

    .dataTables_filter input:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
    }

    /* Show entries dropdown remains dark */
    .dataTables_length select {
        background-color: #323c52;
        border: 1px solid #3a4459;
        color: #ffffff;
        border-radius: 6px;
        padding: 6px 10px;
    }
</style>
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
                                        <option value="">All Agents</option>
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