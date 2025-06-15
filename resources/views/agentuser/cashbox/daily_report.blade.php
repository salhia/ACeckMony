@extends('agentuser.user_dashboard')
@section('agentuser')
<div class="page-content">
    <h3>Daily Cashbox Report</h3>
    <form method="GET" action="{{ route('cashbox.daily.report') }}" class="mb-3">
        <label for="date" class="form-label">Select Date</label>
        <input type="date" name="date" id="date" value="{{ $date }}" class="form-control" style="max-width:200px;display:inline-block;">
        <button type="submit" class="btn btn-primary">Show</button>
    </form>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Summary</h5>
            <ul>
                <li><strong>Opening Balance:</strong> {{ number_format($opening, 2) }} SSP</li>
                <li><strong>Deposits (Total):</strong> {{ number_format($deposits, 2) }} SSP</li>
                <li><strong>Commission (Total):</strong> {{ number_format($commission, 2) }} SSP</li>
                <li><strong>Refill (Approved):</strong> {{ number_format($refill, 2) }} SSP</li>
                <li><strong>Bank:</strong> {{ number_format($bank, 2) }} SSP</li>
                <li><strong>Deductions:</strong> {{ number_format($deductions, 2) }} SSP</li>
                <li><strong>Closing Balance:</strong> {{ number_format($closing, 2) }} SSP</li>
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>All Transactions for {{ $date }}</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $i => $trx)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>
                                <span class="badge bg-{{ $trx->type == 'deposit' ? 'success' : ($trx->type == 'withdrawal' ? 'danger' : 'info') }}">
                                    {{ ucfirst($trx->type) }}
                                </span>
                            </td>
                            <td>{{ number_format($trx->amount, 2) }} SSP</td>
                            <td>{{ $trx->description }}</td>
                            <td>
                                @if($trx->reference_type && $trx->reference_id)
                                    {{ $trx->reference_type }} #{{ $trx->reference_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $trx->status == 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                            <td>{{ $trx->created_at->format('H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No transactions for this day.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
