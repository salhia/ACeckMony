@extends('agent.agent_dashboard')
@section('agent')

<div class="page-content">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">User Transactions Report</h6>

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
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-select">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 mt-4">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('agent.user.transactions') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Total Transactions</h5>
                                    <h4 class="mt-2">{{ $transactions->total() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h5 class="card-title">Total Amount</h5>
                                    <h4 class="mt-2">{{ number_format($transactions->sum('amount'), 2) }} SSP</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h5 class="card-title">Total Commission</h5>
                                    <h4 class="mt-2">{{ number_format($transactions->sum('commission'), 2) }} SSP</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Net Amount</h5>
                                    <h4 class="mt-2">{{ number_format($transactions->sum('net_amount'), 2) }} SSP</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction ID</th>
                                    <th>Office</th>
                                    <th>User</th>
                                    <th>Parent Agent</th>
                                    <th>Region</th>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>Amount</th>
                                    <th>Commission</th>
                                    <th>Net Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $transaction->transaction_code }}</td>
                                    <td>{{ $transaction->sendRegion->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $transaction->user->name ?? 'N/A' }}
                                        @if($transaction->user->role === 'agent')
                                            <span class="badge bg-info">Agent</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction->user->parentAgent->name ?? 'N/A' }}
                                        @if($transaction->user->parentAgent)
                                            <br>
                                            <small class="text-muted">ID: {{ $transaction->user->parent_agent_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction->user->region->name ?? 'N/A' }}
                                        @if($transaction->user->region)
                                            <br>
                                            <small class="text-muted">ID: {{ $transaction->user->region_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction->sender->name ?? 'N/A' }}<br>
                                        <small class="text-muted">{{ $transaction->sender->phone ?? '' }}</small>
                                    </td>
                                    <td>
                                        {{ $transaction->receiver->name ?? 'N/A' }}<br>
                                        <small class="text-muted">{{ $transaction->receiver->phone ?? '' }}</small>
                                    </td>
                                    <td>{{ number_format($transaction->amount, 2) }} SSP</td>
                                    <td>{{ number_format($transaction->commission, 2) }} SSP</td>
                                    <td>{{ number_format($transaction->net_amount, 2) }} SSP</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->status == 'Completed' ? 'success' : 'warning' }}">
                                            {{ $transaction->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $transactions->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
