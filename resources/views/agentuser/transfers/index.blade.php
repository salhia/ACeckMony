@extends('agentuser.user_dashboard')
@section('agentuser')

<div class="page-content">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Balance Summary</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Opening Balance: <strong>{{ number_format($opening, 2) }} SSP</strong></li>
                <li class="list-group-item">Deposits (Total): <strong>{{ number_format($deposits, 2) }} SSP</strong></li>
                <li class="list-group-item">Commission (Total): <strong>{{ number_format($commission, 2) }} SSP</strong></li>
                <li class="list-group-item">Refill (Approved): <strong>{{ number_format($refill, 2) }} SSP</strong></li>
                <li class="list-group-item">Bank: <strong>{{ number_format($bank, 2) }} SSP</strong></li>
                <li class="list-group-item">Deductions: <strong>{{ number_format($deductions, 2) }} SSP</strong></li>
                <li class="list-group-item">Closing Balance: <strong>{{ number_format($closing, 2) }} SSP</strong></li>
            </ul>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Transfer List
            </h3>
        </div>

        <div class="card-body table-responsive">
            <form method="GET" action="" class="mb-3">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <label>From: <input type="date" name="date_from" value="{{ request('date_from', now()->toDateString()) }}"></label>
                    <label>To: <input type="date" name="date_to" value="{{ request('date_to', now()->toDateString()) }}"></label>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </div>
            </form>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Amount</th>
                        <th>Created At</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transaction->senderCustomer->name ?? '-' }}</td>
                            <td>{{ $transaction->receiverCustomer->name ?? '-' }}</td>
                            <td>{{ number_format($transaction->amount, 2) }} SAR</td>
                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('transfers.show', $transaction->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $transactions->links() }}
        </div>
    </div>
</div>

@endsection
