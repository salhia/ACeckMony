@extends('agentuser.user_dashboard')
@section('agentuser')

<div class="page-content">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Transfer List
            </h3>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Amount</th>
                        <th>Created At</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transaction->senderCustomer->name ?? '-' }}</td>
                            <td>{{ $transaction->receiverCustomer->name ?? '-' }}</td>
                            <td>{{ number_format($transaction->amount, 2) }} SAR</td>
                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('transfers.show', $transaction->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $transactions->links() }}
        </div>
    </div>
</div>

@endsection
