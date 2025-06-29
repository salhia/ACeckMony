@extends('agent.agent_dashboard')
@section('agent')

<div class="page-content">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-users"></i> Users Balance Report</h4>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="row g-2 mb-3">
                <div class="col-auto">
                    <select name="user_id" class="form-control">
                        <option value="">All Users</option>
                        @foreach($allUsers as $user)
                            <option value="{{ $user->id }}" {{ (isset($selectedUserId) && $selectedUserId == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </div>
            </form>

            <!-- Summary Cards -->
            @if(count($report) > 0)
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Users</h6>
                            <h4 class="text-primary">{{ count($report) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Deposits</h6>
                            <h4 class="text-success">{{ number_format($report->sum('deposits'), 2) }} SSP</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Commission</h6>
                            <h4 class="text-info">{{ number_format($report->sum('commission'), 2) }} SSP</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total Closing Balance</h6>
                            <h4 class="text-warning">{{ number_format($report->sum('closing'), 2) }} SSP</h4>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>User Name</th>
                            <th>Opening Balance</th>
                            <th>Deposits</th>
                            <th>Commission</th>
                            <th>Refill</th>
                            <th>Bank</th>
                            <th>Deductions</th>
                            <th>Closing Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report as $row)
                            <tr>
                                <td><strong>{{ $row['user']->name }}</strong></td>
                                <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                                <td class="text-end text-success">{{ number_format($row['deposits'], 2) }}</td>
                                <td class="text-end text-info">{{ number_format($row['commission'], 2) }}</td>
                                <td class="text-end text-primary">{{ number_format($row['refill'], 2) }}</td>
                                <td class="text-end text-secondary">{{ number_format($row['bank'], 2) }}</td>
                                <td class="text-end text-danger">{{ number_format($row['deductions'], 2) }}</td>
                                <td class="text-end text-warning"><strong>{{ number_format($row['closing'], 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No data available for the selected filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-primary font-weight-bold">
                            <th>Total</th>
                            <th class="text-end">{{ number_format($report->sum('opening'), 2) }}</th>
                            <th class="text-end">{{ number_format($report->sum('deposits'), 2) }}</th>
                            <th class="text-end">{{ number_format($report->sum('commission'), 2) }}</th>
                            <th class="text-end">{{ number_format($report->sum('refill'), 2) }}</th>
                            <th class="text-end">{{ number_format($report->sum('bank'), 2) }}</th>
                            <th class="text-end">{{ number_format($report->sum('deductions'), 2) }}</th>
                            <th class="text-end">{{ number_format($report->sum('closing'), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
