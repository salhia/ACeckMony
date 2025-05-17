<!-- Report Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Sent Amount</h6>
                <h3>{{ number_format($stats['total_sent'], 2) }} SSP</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Received Amount</h6>
                <h3>{{ number_format($stats['total_received'], 2) }} SSP</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Total Commission</h6>
                <h3>{{ number_format($stats['total_commission'], 2) }} SSP</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Number of Transactions</h6>
                <h3>{{ $stats['count'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Add CSS for table styling -->
<style>
    #transactionsTable {
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    #transactionsTable thead th {
        background-color: #e9ecef;
        border-bottom: 2px solid #dee2e6;
        color: #495057;
        font-weight: 600;
    }

    #transactionsTable tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #transactionsTable tbody tr:hover {
        background-color: #e2e6ea;
    }

    #transactionsTable td {
        vertical-align: middle;
    }
</style>

<!-- Transactions Table -->
<div class="table-responsive">
    <table class="table" id="transactionsTable">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Amount</th>
                <th>Commission</th>
                <th>Received Amount</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Sending Agent</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_code }}</td>
                <td>{{ number_format($transaction->amount, 2) }}</td>
                <td>{{ number_format($transaction->commission, 2) }}</td>
                <td>{{ number_format($transaction->final_delivered_amount, 2) }}</td>
                <td>{{ $transaction->senderCustomer->name ?? 'Not Specified' }}</td>
                <td>{{ $transaction->receiverCustomer->name ?? 'Not Specified' }}</td>
                <td>{{ $transaction->senderAgent->name ?? 'Not Specified' }}</td>
                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                        {{ $transaction->status == 'completed' ? 'Completed' : 'Pending' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No transactions found in the selected period</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('styles')
<style>
    .dt-buttons {
        padding: 1rem;
        background:rgb(4, 35, 67);
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .dt-buttons .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .dt-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    /* Button specific colors */
    .dt-buttons .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }

    .dt-buttons .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .dt-buttons .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }

    .dt-buttons .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .dt-buttons .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }
</style>
@endpush