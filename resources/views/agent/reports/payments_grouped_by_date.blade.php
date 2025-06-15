@extends('agent.agent_dashboard')
@section('agent')

<div class="page-content">
    <h4>Payments Grouped by Date</h4>
    <form method="GET" action="{{ route('agent.payments.grouped') }}" class="row g-3 mb-3">
        <div class="col-md-4">
            <label for="date" class="form-label">Select Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Search</button>
            <button type="submit" name="export_pdf" value="1" class="btn btn-danger">Print PDF</button>
            <a href="{{ route('agent.payments.grouped') }}" class="btn btn-secondary ms-2">Reset</a>
        </div>
    </form>
    @forelse($payments as $date => $group)
        <div class="card mb-3">
            <div class="card-header">
                <strong>{{ $date }}</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Amount</th>
                            <th>Paid Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $index => $payment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ number_format($payment->amount, 2) }} SSP</td>
                                <td>{{ number_format($payment->paid_amount, 2) }} SSP</td>
                                <td>{{ $payment->payment_notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                        {{-- Total row --}}
                        <tr>
                            <td colspan="1"><strong>Total</strong></td>
                            <td><strong>{{ number_format($group->sum('amount'), 2) }} SSP</strong></td>
                            <td><strong>{{ number_format($group->sum('paid_amount'), 2) }} SSP</strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-warning">No paid payments found.</div>
    @endforelse
</div>
@endsection
