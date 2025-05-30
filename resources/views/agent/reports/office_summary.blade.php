@extends('agent.agent_dashboard')
@section('agent')

<div class="page-content">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Office Summary Report</h6>

                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-4 mt-4">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('agent.office.summary') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Total Offices</h5>
                                    <h4 class="mt-2">{{ $summaryReport->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h5 class="card-title">Total Transactions</h5>
                                    <h4 class="mt-2">{{ $summaryReport->sum('total_transactions') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h5 class="card-title">Total Amount</h5>
                                    <h4 class="mt-2">{{ number_format($summaryReport->sum('total_amount'), 2) }} SSP</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Total Commission</h5>
                                    <h4 class="mt-2">{{ number_format($summaryReport->sum('total_commission'), 2) }} SSP</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Office</th>
                                    <th>Transactions</th>
                                    <th>Total Amount</th>
                                    <th>Commission</th>
                                    <th>Net Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summaryReport as $report)
                                <tr>
                                    <td>
                                        {{ $report['region_name'] }}

                                        <br>
                                        <small>ID: {{ $report['sender_region_id'] }}</small>
                                    </td>
                                    <td>{{ number_format($report['total_transactions']) }}</td>
                                    <td>{{ number_format($report['total_amount'], 2) }} SSP</td>
                                    <td>{{ number_format($report['total_commission'], 2) }} SSP</td>
                                    <td>{{ number_format($report['total_net_amount'], 2) }} SSP</td>
                                    <td>
                                        <a href="{{ route('agent.office.detailed', ['sender_region_id' => $report['sender_region_id']]) }}"
                                           class="btn btn-sm btn-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th>Total</th>
                                    <th>{{ number_format($summaryReport->sum('total_transactions')) }}</th>
                                    <th>{{ number_format($summaryReport->sum('total_amount'), 2) }} SSP</th>
                                    <th>{{ number_format($summaryReport->sum('total_commission'), 2) }} SSP</th>
                                    <th>{{ number_format($summaryReport->sum('total_net_amount'), 2) }} SSP</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
