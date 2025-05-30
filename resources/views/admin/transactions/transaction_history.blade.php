@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Transaction History</h4>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="start_date" placeholder="Start Date">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="end_date" placeholder="End Date">
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="status">
                                    <option value="">All Status</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="delivered">Delivered</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary" id="filter">Filter</button>
                                <button class="btn btn-secondary" id="reset">Reset</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Date</th>
                                        <th>Sender</th>
                                        <th>Receiver</th>
                                        <th>Amount</th>
                                        <th>Commission</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->transaction_code }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($transaction->senderCustomer)
                                                {{ $transaction->senderCustomer->name }}
                                            @elseif($transaction->senderAgent)
                                                {{ $transaction->senderAgent->name }} (Agent)
                                            @elseif($transaction->senderUser)
                                                {{ $transaction->senderUser->name }} (User)
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->receiverCustomer)
                                                {{ $transaction->receiverCustomer->name }}
                                            @elseif($transaction->receiverAgent)
                                                {{ $transaction->receiverAgent->name }} (Agent)
                                            @elseif($transaction->receiverUser)
                                                {{ $transaction->receiverUser->name }} (User)
                                            @endif
                                        </td>
                                        <td>{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ number_format($transaction->commission, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{
                                                $transaction->status == 'completed' ? 'success' :
                                                ($transaction->status == 'pending' ? 'warning' :
                                                ($transaction->status == 'delivered' ? 'info' : 'danger'))
                                            }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('transaction.details', $transaction->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('transaction.print', $transaction->id) }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-print"></i> Print
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#filter').click(function() {
            let url = new URL(window.location.href);
            url.searchParams.set('start_date', $('#start_date').val());
            url.searchParams.set('end_date', $('#end_date').val());
            url.searchParams.set('status', $('#status').val());
            window.location.href = url.toString();
        });

        $('#reset').click(function() {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#status').val('');
            let url = new URL(window.location.href);
            url.search = '';
            window.location.href = url.toString();
        });
    });
</script>
@endpush
