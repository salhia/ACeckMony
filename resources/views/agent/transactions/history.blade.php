@extends('agent.agent_dashboard')
@section('agent')

<div class="page-content">
    <h4>My Transaction History</h4>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ $tx->id }}</td>
                <td>{{ $tx->created_at }}</td>
                <td>{{ number_format($tx->amount, 2) }} SSP</td>
                <td>{{ ucfirst($tx->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $transactions->links() }}
</div>
@endsection