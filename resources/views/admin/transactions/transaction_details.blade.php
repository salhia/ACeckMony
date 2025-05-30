@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Transaction Details</h4>
                            <div>
                                <a href="{{ route('transaction.history') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <a href="{{ route('transaction.print', $transaction->id) }}" class="btn btn-primary">
                                    <i class="fas fa-print"></i> Print Receipt
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Transaction Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Transaction ID</th>
                                        <td>{{ $transaction->transaction_code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date & Time</th>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ $transaction->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td>{{ number_format($transaction->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Commission</th>
                                        <td>{{ number_format($transaction->commission, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Amount</th>
                                        <td>{{ number_format($transaction->amount + $transaction->commission, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Customer Information</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Sender Details</h6>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Name</th>
                                                <td>{{ $transaction->senderCustomer->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Phone</th>
                                                <td>{{ $transaction->senderCustomer->phone }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $transaction->senderCustomer->email }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Receiver Details</h6>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Name</th>
                                                <td>{{ $transaction->receiverCustomer->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Phone</th>
                                                <td>{{ $transaction->receiverCustomer->phone }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{{ $transaction->receiverCustomer->email }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($transaction->status == 'pending')
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Transaction Actions</h5>
                                        <form action="{{ route('transaction.update-status', $transaction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" name="status" value="completed" class="btn btn-success">
                                                <i class="fas fa-check"></i> Mark as Completed
                                            </button>
                                            <button type="submit" name="status" value="failed" class="btn btn-danger">
                                                <i class="fas fa-times"></i> Mark as Failed
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
