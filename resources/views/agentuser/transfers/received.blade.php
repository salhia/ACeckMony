@extends('agentuser.user_dashboard')
@section('agentuser')

<div class="page-content">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h3 class="card-title">
                <i class="fas fa-download"></i> Received Transfers To ({{ $regionName }})
            </h3>
        </div>

        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-3">
                <div class="col-auto">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->toDateString()) }}">
                </div>
                <div class="col-auto">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->toDateString()) }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <!-- Status Filter Buttons -->
            <div class="mb-3">
               <a href="{{ route('agent.received.transfers', ['status' => 'delivered']) }}"
                   class="btn btn-primary btn-sm {{ request('status') == 'delivered' ? 'active' : '' }}">
                    <i data-feather="truck"></i> Delivered
                </a>
                <a href="{{ route('agent.received.transfers', ['status' => 'completed']) }}"
                   class="btn btn-success btn-sm {{ request('status') == 'completed' ? 'active' : '' }}">
                    <i data-feather="check-circle"></i> Completed
                </a>
                <a href="{{ route('agent.received.transfers', ['status' => 'rejected']) }}"
                   class="btn btn-danger btn-sm {{ request('status') == 'rejected' ? 'active' : '' }}">
                    <i data-feather="x-circle"></i> Rejected
                </a>

                <a href="{{ route('agent.received.transfers') }}"
                   class="btn btn-secondary btn-sm {{ !request('status') ? 'active' : '' }}">
                    <i data-feather="list"></i> All
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="transfersTable">
                    <thead>
                        <tr>
                            <th>#</th>

                            <th>Receiver</th>
                            <th>Amount</th>
                            <th>Region From</th>
                            <th>Region To</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Sender User</th>
                            <th>Delivered By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr data-created-by="{{ $transaction->created_by }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->receiverCustomer->name ?? '-' }}</td>
                                <td>{{ number_format($transaction->amount, 2) }} Pound</td>
                                <td>{{ $transaction->senderUser->region->name ?? 'No Region' }}</td>
                                <td>{{ $transaction->region->name ?? 'No Region' }}</td>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' :
                                        ($transaction->status == 'pending' ? 'warning' :
                                        ($transaction->status == 'rejected' ? 'danger' :
                                        ($transaction->status == 'delivered' ? 'primary' : 'secondary'))) }}"
                                          data-current-status="{{ $transaction->status }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                  <td>{{ $transaction->senderUser->name ?? '-' }}</td>

                                <td>
                                    @if($transaction->delivered_by_user_id)
                                        {{ $transaction->deliveredBy->name ?? 'Unknown' }}
                                        <br>
                                        <small class="text-muted">{{ $transaction->delivered_at ? $transaction->delivered_at->format('Y-m-d H:i') : '' }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('transfers.show', $transaction->id) }}"
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i data-feather="eye"></i>
                                        </a>

                                        @if($transaction->status != 'completed')
                                            <button class="btn btn-sm btn-danger status-btn"
                                                    data-id="{{ $transaction->id }}"
                                                    data-status="rejected"
                                                    title="Reject">
                                                <i data-feather="x-circle"></i>
                                            </button>

                                                <button class="btn btn-sm btn-success status-btn"
                                                        data-id="{{ $transaction->id }}"
                                                        data-status="completed"
                                                        title="Mark as Completed">
                                                    <i data-feather="check-circle"></i>
                                                </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Initialize DataTable
    $('#transfersTable').DataTable({
        pageLength: 10,
        order: [[0, "desc"]],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries"
        }
    });

    // Initialize Feather icons
    feather.replace();

    // Status update handling
    $('.status-btn').click(function () {
        const transactionId = $(this).data('id');
        const newStatus = $(this).data('status');
        const currentUserId = {{ auth()->id() }};
        const btn = $(this);
        const currentStatus = btn.closest('tr').find('span.badge').data('current-status');
        const createdBy = btn.closest('tr').data('created-by');

        // Check permissions
        //if (['rejected', 'delivered', 'pending'].includes(currentStatus) && currentUserId !== createdBy) {
        //    toastr.error('Only the creator of this transaction can change its status.');
       //     return;
      //  }

        if (!confirm(`Are you sure you want to mark this as ${newStatus}?`)) return;

        $.ajax({
            url: "{{ route('transfers.updateStatus.ajax') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                transaction_id: transactionId,
                status: newStatus
            },
            beforeSend: function () {
                btn.prop('disabled', true);
            },
            success: function (response) {
                if (response.success) {
                    toastr.success('Status updated successfully.');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to update status.');
                }
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error updating status');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush

@section('styles')
<style>
    .btn-group {
        display: flex;
        gap: 2px;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    .table td {
        vertical-align: middle;
    }
    .status-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection
