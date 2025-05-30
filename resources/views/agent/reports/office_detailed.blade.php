@extends('agent.agent_dashboard')
@section('agent')

<div class="page-content">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Office Detailed Report</h6>

                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label">Office</label>
                                <select name="sender_region_id" class="form-select">
                                    <option value="">All Offices</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ request('sender_region_id') == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3 mt-4">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('agent.office.detailed') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Total Users</h5>
                                    <h4 class="mt-2">{{ $detailedReport->unique('user_id')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h5 class="card-title">Total Transactions</h5>
                                    <h4 class="mt-2">{{ $detailedReport->sum('total_transactions') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h5 class="card-title">Total Amount</h5>
                                    <h4 class="mt-2">{{ number_format($detailedReport->sum('total_amount'), 2) }} SSP</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Total Commission</h5>
                                    <h4 class="mt-2">{{ number_format($detailedReport->sum('total_commission'), 2) }} SSP</h2>
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
                                    <th>User</th>
                                    <th>Parent Agent</th>
                                    <th>Region</th>
                                    <th>Transactions</th>
                                    <th>Total Amount</th>
                                    <th>Commission</th>
                                    <th>Net Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailedReport as $report)
                                <tr>
                                    <td>{{ $report->sendRegion->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $report->user->name ?? 'N/A' }}
                                        @if($report->user->role === 'agent')
                                            <span class="badge bg-info">Agent</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $report->user->parentAgent->name ?? 'N/A' }}
                                        @if($report->user->parentAgent)
                                            <br>
                                            <small class="text-muted">ID: {{ $report->user->parent_agent_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $report->user->region->name ?? 'N/A' }}
                                        @if($report->user->region)
                                            <br>
                                            <small class="text-muted">ID: {{ $report->user->region_id }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($report->total_transactions) }}</td>
                                    <td>{{ number_format($report->total_amount, 2) }} SSP</td>
                                    <td>{{ number_format($report->total_commission, 2) }} SSP</td>
                                    <td>{{ number_format($report->total_net_amount, 2) }} SSP</td>
                                    <td>
                                        <a href="{{ route('agent.user.transactions', [
                                            'user_id' => $report->user_id,
                                            'sender_region_id' => $report->sender_region_id
                                        ]) }}" class="btn btn-sm btn-primary">
                                            View Transactions
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="2">Total</th>
                                    <th>{{ number_format($detailedReport->sum('total_transactions')) }}</th>
                                    <th>{{ number_format($detailedReport->sum('total_amount'), 2) }} SSP</th>
                                    <th>{{ number_format($detailedReport->sum('total_commission'), 2) }} SSP</th>
                                    <th>{{ number_format($detailedReport->sum('total_net_amount'), 2) }} SSP</th>
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
