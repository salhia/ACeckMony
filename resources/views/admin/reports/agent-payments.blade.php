@extends('admin.admin_dashboard')


@section('admin')
<div class="page-content">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Agent Payments Report</h4>
                            <div>
                                <a href="{{ route('admin.agent.payments.history') }}" class="btn btn-info">Payment History</a>
                                <button id="transferPayments" class="btn btn-primary">Transfer Payments from Transactions</button>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.agent.payments.report') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Agent</label>
                                        <select name="user_id" class="form-control" id="agentSelect">
                                            <option value="">All Agents</option>
                                            @foreach($agents ?? [] as $agent)
                                                <option value="{{ $agent->id }}"
                                                        data-pending="{{ $agent->pending_amount ?? 0 }}"
                                                        {{ request('user_id') == $agent->id ? 'selected' : '' }}>
                                                    {{ $agent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Payments</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </form>

                        <!-- Results Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>#</th>
                                        <th>Agent</th>
                                        <th>Transferred Amount</th>
                                        <th>Commission Amount</th>
                                        <th>Percentage</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($payments) && $payments->count() > 0)
                                        @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input payment-checkbox"
                                                       data-id="{{ $payment->id }}"
                                                       data-amount="{{ $payment->amount }}"
                                                       data-agent="{{ $payment->user->name ?? 'Unknown' }}">
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $payment->user->name ?? 'Unknown' }}</td>
                                            <td>{{ number_format($payment->trnsferamount ?? 0, 2) }}</td>
                                            <td>{{ number_format($payment->amount ?? 0, 2) }}</td>
                                            <td>{{ $payment->percentage ?? 0 }}%</td>
                                            <td>{{ $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : 'Unknown' }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No pending payments found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Payment Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Payment Section</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Selected Agent</label>
                                            <input type="text" class="form-control" id="selectedAgent" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Total Selected Amount</label>
                                            <input type="text" class="form-control" id="selectedAmount" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Payment Date</label>
                                            <input type="datetime-local" class="form-control" id="paymentDate">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Payment Notes</label>
                                            <textarea class="form-control" id="paymentNotes" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <button type="button" class="btn btn-success" id="processPayment">Confirm Payment</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $payments->links() ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let selectedPayments = new Set();
    let selectedAmount = 0;
    let selectedAgent = '';

    // Select all
    $('#selectAll').change(function() {
        $('.payment-checkbox').prop('checked', $(this).prop('checked'));
        updateSelectedPayments();
    });

    // Update selected payments
    $('.payment-checkbox').change(function() {
        updateSelectedPayments();
    });

    function updateSelectedPayments() {
        selectedPayments.clear();
        selectedAmount = 0;
        selectedAgent = '';

        $('.payment-checkbox:checked').each(function() {
            const id = $(this).data('id');
            const amount = parseFloat($(this).data('amount'));
            const agent = $(this).data('agent');

            selectedPayments.add(id);
            selectedAmount += amount;
            selectedAgent = agent;
        });

        $('#selectedAmount').val(selectedAmount.toFixed(2));
        $('#selectedAgent').val(selectedAgent);
    }

    // Transfer payments
    $('#transferPayments').click(function() {
        if(confirm('Are you sure you want to transfer payments from transactions?')) {
            $.ajax({
                url: '{{ route("admin.transfer.payments") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while processing the request';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        }
    });

    // Confirm payment
    $('#processPayment').click(function() {
        if (selectedPayments.size === 0) {
            alert('Please select payments to process');
            return;
        }

        const paidAt = $('#paymentDate').val();
        if (!paidAt) {
            alert('Please select the payment date.');
            return;
        }

        if (confirm('Are you sure you want to process the selected amount?')) {
            const paymentData = {
                payment_ids: Array.from(selectedPayments),
                amount: selectedAmount,
                notes: $('#paymentNotes').val(),
                paid_at: paidAt
            };

            $.ajax({
                url: '{{ route("admin.process.payments") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(paymentData),
                success: function(response) {
                    if(response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while processing the request';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    alert(errorMessage);
                }
            });
        }
    });
});
</script>
@endpush
@endsection
