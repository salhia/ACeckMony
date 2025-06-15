@extends('agent.agent_dashboard')
@section('agent')

<div class="page-content">
    <div class="card-body">
        <form method="GET" action="{{ route('agent.payments.history') }}" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control"
                       value="{{ request('start_date') }}">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control"
                       value="{{ request('end_date') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <button type="submit" name="export_pdf" value="1" class="btn btn-danger">Export PDF</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4>Payment History</h4>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Paid Amount</th>
                    <th>Payment Date</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $index => $payment)
                    <tr>
                        <td>{{ $payments->firstItem() + $index }}</td>
                        <td>{{ number_format($payment->amount, 2) }} SSP</td>
                        <td>
                            @if($payment->status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>{{ number_format($payment->paid_amount, 2) ?? '-' }} SSP</td>
                        <td>
                            {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') : '-' }}
                        </td>
                        <td>{{ $payment->payment_notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No payment records found.</td>
                    </tr>
                @endforelse

                @php
                    $totalAmount = $payments->sum('amount');
                    $totalPaid = $payments->sum('paid_amount');
                @endphp
                <tr>
                    <td colspan="1"><strong>Total</strong></td>
                    <td><strong>{{ number_format($totalAmount, 2) }} SSP</strong></td>
                    <td></td>
                    <td><strong>{{ number_format($totalPaid, 2) }} SSP</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
        <div>
            {{ $payments->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
