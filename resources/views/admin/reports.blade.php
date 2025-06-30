@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">
    <!-- Date Range Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports') }}" class="row align-items-end">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ $startDate->format('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ $endDate->format('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="filter" class="icon-sm me-2"></i>
                                Filter Data
                            </button>
                            <a href="{{ route('admin.reports') }}" class="btn btn-secondary">
                                <i data-feather="refresh-cw" class="icon-sm me-2"></i>
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Summary Statistics</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h6>Total Transactions</h6>
                                <h3>{{ $transactions->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h6>Total Amount</h6>
                                <h3>{{ number_format($transactions->sum('amount'), 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h6>Total Vendor Commission</h6>
                                <h3>{{ number_format($transactions->sum('admin_fee'), 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h6>Total States</h6>
                                <h3>{{ $stateStats->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- State-wise Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">State-wise Statistics</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>State</th>
                                    <th>Total Amount</th>
                                    <th>Transactions</th>
                                    <th>Vendor Commission</th>
                                    <th>Average Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stateStats as $state)
                                    <tr>
                                        <td>{{ $state['name'] }}</td>
                                        <td>{{ number_format($state['total_amount'], 2) }}</td>
                                        <td>{{ $state['total_transactions'] }}</td>
                                        <td>{{ number_format($state['total_admin_fee'], 2) }}</td>
                                        <td>{{ number_format($state['total_amount'] / $state['total_transactions'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agent-wise Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Agent-wise Statistics</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Agent</th>
                                    <th>Total Amount</th>
                                    <th>Transactions</th>
                                    <th>Vendor Commission</th>
                                    <th>Average Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agentStats as $agent)
                                    <tr>
                                        <td>{{ $agent['name'] }}</td>
                                        <td>{{ number_format($agent['total_amount'], 2) }}</td>
                                        <td>{{ $agent['total_transactions'] }}</td>
                                        <td>{{ number_format($agent['total_admin_fee'], 2) }}</td>
                                        <td>{{ number_format($agent['total_amount'] / $agent['total_transactions'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Detailed Transactions</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction ID</th>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>State</th>
                                    <th>Agent</th>
                                    <th>Amount</th>
                                    <th>Vendor Commission</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $transaction->transaction_id }}</td>
                                        <td>{{ $transaction->senderCustomer->name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->receiverAgent->name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->region->name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->senderAgent->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ number_format($transaction->admin_fee, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
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

@push('scripts')
<script>
$(function() {
    // Date range validation
    $('#start_date, #end_date').on('change', function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        if (startDate && endDate) {
            if (startDate > endDate) {
                alert('Start date cannot be greater than end date');
                $(this).val('');
            }
        }
    });
});
</script>
@endpush
