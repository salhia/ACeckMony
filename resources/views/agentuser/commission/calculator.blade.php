@extends('agentuser.user_dashboard')
@section('agentuser')

<div class="page-content">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">حاسبة العمولة</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">المبلغ</label>
                                <input type="number" id="amount" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h6>تفاصيل العملية:</h6>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p>المبلغ الأساسي: <span id="baseAmount">0</span></p>
                                    <p>نسبة العمولة: <span id="commission_rate">{{ auth()->user()->commission_rate ?? 0 }}%</span></p>
                                    <p>قيمة العمولة: <span id="commissionAmount">0</span></p>
                                    <p>صافي المبلغ: <span id="netAmount">0</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('amount').addEventListener('input', function() {
    calculateCommission();
});

function calculateCommission() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const commission_rate = {{ auth()->user()->commission_rate ?? 0 }};

    // حساب العمولة
    const commission = (amount * commission_rate) / 100;

    // حساب صافي المبلغ
    const netAmount = amount - commission;

    // تحديث العرض
    document.getElementById('baseAmount').textContent = amount.toFixed(2);
    document.getElementById('commissionAmount').textContent = commission.toFixed(2);
    document.getElementById('netAmount').textContent = netAmount.toFixed(2);
}
</script>
@endpush

@endsection
