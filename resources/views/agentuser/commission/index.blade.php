@extends('agentuser.user_dashboard')
@section('agentuser')

<div class="page-content">
    <!-- Commission Settings Card -->
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">إعدادات العمولة</h6>
                    <form method="POST" action="{{ route('agent.commission.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">نسبة العمولة (%)</label>
                                    <input type="number" step="0.01" class="form-control" name="commission_rate"
                                           value="{{ old('commission_rate', $commission->commission_rate ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">العمولة الثابتة</label>
                                    <input type="number" step="0.01" class="form-control" name="fixed_commission"
                                           value="{{ old('fixed_commission', $commission->fixed_commission ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">رسوم الإدارة الثابتة</label>
                                    <input type="number" step="0.01" class="form-control" name="admin_fee_fixed"
                                           value="{{ old('admin_fee_fixed', $commission->admin_fee_fixed ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الحد الأدنى للمبلغ</label>
                                    <input type="number" step="0.01" class="form-control" name="min_amount"
                                           value="{{ old('min_amount', $commission->min_amount ?? '') }}" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Table -->
    <div class="row mt-4">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">سجل الأرباح</h6>
                    <div class="table-responsive">
                        <table id="earningsTable" class="table">
                            <thead>
                                <tr>
                                    <th>رقم المعاملة</th>
                                    <th>المبلغ المكتسب</th>
                                    <th>تاريخ المعاملة</th>
                                    <th>تفاصيل المعاملة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($earnings as $earning)
                                <tr>
                                    <td>{{ $earning->transaction->transaction_code }}</td>
                                    <td>{{ number_format($earning->earned_amount, 2) }}</td>
                                    <td>{{ $earning->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info"
                                                onclick="showTransactionDetails('{{ $earning->transaction_id }}')">
                                            عرض التفاصيل
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#earningsTable').DataTable({
            "order": [[ 2, "desc" ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
            }
        });
    });

    function showTransactionDetails(transactionId) {
        // Implement transaction details modal/popup here
    }
</script>
@endpush

@endsection