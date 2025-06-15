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

            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead>
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
                                <td>{{ $row['user']->name }}</td>
                                <td>{{ number_format($row['opening'], 2) }}</td>
                                <td>{{ number_format($row['deposits'], 2) }}</td>
                                <td>{{ number_format($row['commission'], 2) }}</td>
                                <td>{{ number_format($row['refill'], 2) }}</td>
                                <td>{{ number_format($row['bank'], 2) }}</td>
                                <td>{{ number_format($row['deductions'], 2) }}</td>
                                <td>{{ number_format($row['closing'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No data available for the selected filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
