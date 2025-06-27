@extends('admin.admin_dashboard')

@section('admin')
<div class="page-content">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Super Agent Payment History</h4>
                            <a href="{{ route('admin.agent.payments.report') }}" class="btn btn-primary">Back to Pending Payments</a>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.agent.payments.history') }}" class="form-inline d-flex align-items-center flex-wrap gap-2 mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Super Agent</label>
                                        <select name="user_id" class="form-control">
                                            <option value="">All Super Agents</option>
                                            @foreach($agents ?? [] as $agent)
                                                <option value="{{ $agent->id }}" {{ request('user_id') == $agent->id ? 'selected' : '' }}>
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
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">Search</button>
                                    </div>
                                </div>
                            </div>                        </form>

                        <!-- Results Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Super Agent</th>
                                        <th>Transfer Amount</th>
                                        <th>Super Agent Commission Amount</th>
                                        <th>Percentage</th>
                                        <th>Payment Date</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments ?? [] as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->user->name ?? 'Unknown' }}</td>
                                        <td>{{ number_format($payment->trnsferamount ?? 0, 2) }}</td>
                                        <td>{{ number_format($payment->amount ?? 0, 2) }}</td>
                                        <td>{{ $payment->percentage ?? 0 }}%</td>
                                        <td>{{ $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : 'Unknown' }}</td>
                                        <td>{{ $payment->notes ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No completed payments found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $payments->links() ?? '' }}
                        </div>

                        @if(request('user_id') && isset($agentPaidTotals[request('user_id')]))
                        <div class="alert alert-success mt-4 text-center">
                            Paid Total for Super Agent <b>{{ $agents->where('id', request('user_id'))->first()->name ?? '' }}</b>:
                            <span style="font-weight:bold;">{{ number_format($agentPaidTotals[request('user_id')], 2) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const agentPaidTotals = @json($agentPaidTotals);
    $(document).ready(function() {
        function updatePaidTotal() {
            const agentId = $('#agentSelect').val();
            if(agentId && agentPaidTotals[agentId]) {
                $('#paidTotalValue').text(parseFloat(agentPaidTotals[agentId]).toFixed(2));
                $('#paidTotalBox').show();
            } else {
                $('#paidTotalValue').text('0.00');
                $('#paidTotalBox').hide();
            }
        }
        $('#agentSelect').change(updatePaidTotal);
        updatePaidTotal();
    });
</script>
@endpush
@endsection


