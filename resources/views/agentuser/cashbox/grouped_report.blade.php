@extends('agentuser.user_dashboard')

<style>
.custom-header {
    background-color: #6c757d !important;
    color: white !important;
}
.custom-header th,
.custom-header h5 {
    color: white !important;
}
</style>

@section('agentuser')
<div class="page-content">
    <h3>Grouped Cashbox Report</h3>

    <form method="GET" action="{{ route('cashbox.grouped.report') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block">Show Report</button>
            </div>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Deposits</h6>
                    <h4>{{ number_format($totalDeposits, 2) }} SSP</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Total Refills</h6>
                    <h4>{{ number_format($grandTotals['refill'], 2) }} SSP</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Total Deductions</h6>
                    <h4>{{ number_format($totalDeductions, 2) }} SSP</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Net Amount</h6>
                    <h4>{{ number_format($netAmount, 2) }} SSP</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Summary Table -->
    <div class="card">
        <div class="card-header">
            <h5>Daily Summary Report ({{ $startDate }} to {{ $endDate }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="custom-header">
                        <tr>
                            <th>Date</th>
                            <th>Opening Balance</th>
                            <th>Deposits</th>
                            <th>Commission</th>
                            <th>Refills</th>
                            <th>Bank</th>
                            <th>Withdrawals</th>
                            <th>Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyTotals as $date => $totals)
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong></td>
                                <td class="text-end {{ $totals['opening'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($totals['opening'], 2) }} SSP
                                </td>
                                <td class="text-end text-success">
                                    {{ number_format($totals['deposit'], 2) }} SSP
                                </td>
                                <td class="text-end text-success">
                                    {{ number_format($totals['commission'], 2) }} SSP
                                </td>
                                <td class="text-end text-success">
                                    {{ number_format($totals['refill'], 2) }} SSP
                                </td>
                                <td class="text-end {{ $totals['bank'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($totals['bank'], 2) }} SSP
                                </td>
                                <td class="text-end text-danger">
                                    {{ number_format($totals['withdraw'], 2) }} SSP
                                </td>
                                <td class="text-end fw-bold {{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($totals['net'], 2) }} SSP
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No transactions found for the selected date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background-color:rgb(77, 85, 90); color: white !important;" >
                        <tr class="fw-bold">
                            <td><strong>GRAND TOTALS</strong></td>
                            <td class="text-end fw-bold {{ $grandTotals['opening'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($grandTotals['opening'], 2) }} SSP
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ number_format($grandTotals['deposit'], 2) }} SSP
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ number_format($grandTotals['commission'], 2) }} SSP
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ number_format($grandTotals['refill'], 2) }} SSP
                            </td>
                            <td class="text-end fw-bold {{ $grandTotals['bank'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($grandTotals['bank'], 2) }} SSP
                            </td>
                            <td class="text-end fw-bold text-danger">
                                {{ number_format($grandTotals['withdraw'], 2) }} SSP
                            </td>
                            <td class="text-end fw-bold fs-5 {{ $netAmount >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($netAmount, 2) }} SSP
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed Summary -->
    <div class="card mt-4">
        <div class="card-header custom-header">
            <h5>Detailed Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Income Items:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Opening Balance:</strong> {{ number_format($grandTotals['opening'], 2) }} SSP</li>
                        <li><strong>Total Deposits:</strong> {{ number_format($grandTotals['deposit'], 2) }} SSP</li>
                        <li><strong>Total Commission:</strong> {{ number_format($grandTotals['commission'], 2) }} SSP</li>
                        <li><strong>Total Refills:</strong> {{ number_format($grandTotals['refill'], 2) }} SSP</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Expense Items:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Bank Deposits:</strong> {{ number_format(abs($grandTotals['bank']), 2) }} SSP</li>
                        <li><strong>Withdrawals:</strong> {{ number_format(abs($grandTotals['withdraw']), 2) }} SSP</li>
                        <li class="border-top pt-2"><strong>Net Amount:</strong>
                            <span class="fw-bold {{ $netAmount >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($netAmount, 2) }} SSP
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Refills -->
    @if($pendingRefills->count() > 0)
        <div class="card mt-4">
            <div class="card-header bg-warning">
                <h5>Pending Refills</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRefills as $i => $trx)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $trx->date }}</td>
                                    <td>{{ number_format($trx->amount, 2) }} SSP</td>
                                    <td>{{ $trx->description }}</td>
                                    <td>{{ $trx->created_at->format('H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
