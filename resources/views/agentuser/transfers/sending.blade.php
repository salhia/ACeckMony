@extends('agentuser.user_dashboard')
@section('agentuser')

<div class="page-content">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h3 class="card-title">

                    <i class="fas fa-download"></i> Sending Transfers from ({{ $regionName }})

            </h3>
        </div>

        <div class="card-body table-responsive">
        <!-- Status Filter Buttons -->
    <div class="mb-3">
        <a href="{{ route('agent.sending.transfers', ['status' => 'pending']) }}" class="btn btn-warning btn-sm">
            <i data-feather="clock"></i> Pending
        </a>
        <a href="{{ route('agent.sending.transfers', ['status' => 'completed']) }}" class="btn btn-success btn-sm">
            <i data-feather="check-circle"></i> Completed
        </a>
        <a href="{{ route('agent.sending.transfers', ['status' => 'rejected']) }}" class="btn btn-danger btn-sm">
            <i data-feather="x-circle"></i> Rejected
        </a>
        <a href="{{ route('agent.sending.transfers', ['status' => 'delivered']) }}" class="btn btn-primary btn-sm">
            <i data-feather="truck"></i> Delivered
        </a>
    </div>
            <div class="table-responsive">
    <table class="table table-bordered table-striped" id="transfersTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Amount</th>
                <th>Region From</th>
                <th>Region To</th>
                <th>Date</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr data-created-by="{{ $transaction->created_by }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $transaction->senderCustomer->name ?? '-' }}</td>
                    <td>{{ $transaction->receiverCustomer->name ?? '-' }}</td>
                    <td>{{ number_format($transaction->amount, 2) }} Pound</td>
                    <td>{{ $transaction->senderUser->region->name ?? 'No Region' }}</td>
                    <td>{{ $transaction->region->name ?? 'No Region' }}</td>
                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                   <span class="badge bg-secondary" data-current-status="{{ $transaction->status }}">
                   {{ $transaction->status }}
                   </span>
                    </td>

                    <td>
                    <a href="{{ route('transfers.show', $transaction->id) }}" class="btn btn-sm btn-xs btn-info">
                            <i data-feather="eye"></i>
                        </a>


    @if($transaction->created_by === auth()->id())
    <button class="btn btn-success btn-xs status-btn" data-id="{{ $transaction->id }}" data-status="completed" title="Mark as Completed">
        <i data-feather="check-circle"></i>
    </button>
@endif
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
<!-- Feather Icons -->
<script>
$(document).ready(function () {
    $('.status-btn').click(function () {
        const transactionId = $(this).data('id');
        const newStatus = $(this).data('status');
         const currentUserId = {{ auth()->id() }};
        const btn = $(this);
        const currentStatus = btn.closest('tr').find('span.badge').data('current-status');
        const createdBy = btn.closest('tr').data('created-by'); // تأكد من إضافته في الـ <tr>

    // منع التغيير إلا إذا كان المستخدم هو من أنشأ العملية
    if (['rejected', 'delivered', 'pending'].includes(currentStatus) && currentUserId !== createdBy) {
       alert(
  "Only the creator of this transaction can return it to 'completed'.\n" );
  return;
    }


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
         }, 1000); // إعادة تحميل الصفحة بعد ثانية واحدة
            }
                else {
                    toastr.error('Failed to update status.');
                }
            },
            error: function () {
                alert('Error updating status');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
});

</script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#transfersTable').DataTable({
            pageLength: 10,
            order: [[ 0, "desc" ]]
        });

        feather.replace(); // Initialize Feather icons
    });
</script>
@endpush
@section('styles')
<style>
    .btn-xs {
        padding: 2px 6px;
        font-size: 11px;
        line-height: 1;
    }
    .btn-xs svg {
        vertical-align: middle;
    }
</style>
@endsection
