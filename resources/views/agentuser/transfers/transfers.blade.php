@extends('agentuser.user_dashboard')
@section('agentuser')
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<div class="page-content">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">
                <i class="fas fa-receipt"></i> Transfer Details
            </h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sender Information</h5>
                    <p><strong>Name:</strong> {{ $transaction->senderCustomer->name }}</p>
                    <p><strong>Phone:</strong> {{ $transaction->senderCustomer->phone }}</p>
                </div>

                <div class="col-md-6">
                    <h5>Receiver Information</h5>
                    <p><strong>Name:</strong> {{ $transaction->receiverCustomer->name }}</p>
                    <p><strong>Phone:</strong> {{ $transaction->receiverCustomer->phone }}</p>
                </div>
            </div>

            <hr>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Transfer Details</h5>
                    <p><strong>Transaction Code:</strong> {{ $transaction->transaction_code }}</p>
                    <p><strong>Date:</strong> {{ $transaction->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>State:</strong> {{ $states[$transaction->state_code] ?? $transaction->state_code }}</p>
                </div>

                <div class="col-md-6">
                    <h5>Financial Details</h5>
                    <p><strong>Sent Amount:</strong> {{ number_format($transaction->amount, 2) }} Pound</p>

                    @if($transaction->discount_amount > 0)
                    <p><strong>Discount:</strong>
                        {{ $transaction->discount_type == 'percentage' ? $transaction->discount_value.'%' : $transaction->discount_value.' Pound' }}
                        ({{ number_format($transaction->discount_amount, 2) }} Pound)
                    </p>
                    @endif

                    <p><strong>Commission:</strong> {{ number_format($transaction->commission, 2) }} Pound</p>
                    <p><strong>Final Received Amount:</strong> {{ number_format($transaction->final_delivered_amount, 2) }} Pound</p>
                </div>
            </div>

            @if($transaction->notes)
            <div class="mt-4">
                <h5>Notes</h5>
                <p>{{ $transaction->notes }}</p>
            </div>
            @endif
        </div>

        <div class="card-footer text-center">
            <a href="{{ route('transfers.print', $transaction->id) }}" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Receipt
            </a>
            <a href="{{ route('transfers.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Back to List
            </a>
        </div>
    </div>
</div>
@endsection
