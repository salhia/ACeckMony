@extends('agentuser.user_dashboard')
@section('agentuser')
 <div class="page-content">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">
                <i class="fas fa-money-bill-transfer"></i> تحويل أموال
            </h3>
        </div>

        <div class="card-body">
            <form id="transferForm" method="POST" action="{{ route('transfers.store') }}">
                @csrf

                <!-- معلومات المرسل -->
                <div class="sender-info mb-4">
                    <h4 class="section-title">
                        <i class="fas fa-user"></i> معلومات المرسل
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sender_id">اختر المرسل</label>
                                <select class="form-control select2" id="sender_id" name="sender_id" required>
                                    <option value="">-- اختر المرسل --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            @if(old('sender_id') == $customer->id) selected @endif>
                                            {{ $customer->name }} - {{ $customer->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sender_name">اسم المرسل</label>
                                <input type="text" class="form-control" id="sender_name" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات المستلم -->
                <div class="receiver-info mb-4">
                    <h4 class="section-title">
                        <i class="fas fa-user-plus"></i> معلومات المستلم
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receiver_search">ابحث عن المستلم</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="receiver_search"
                                           placeholder="رقم الهوية أو الهاتف">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button"
                                                id="searchCustomer">
                                            <i class="fas fa-search"></i> بحث
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="customerResult" class="mt-3">
                                <!-- ستظهر نتائج البحث هنا -->
                            </div>
                        </div>
                    </div>

                    <!-- نموذج إضافة زبون جديد (مخفي بشكل افتراضي) -->
                    <div id="newCustomerForm" style="display: none;">
                        <hr>
                        <h5 class="mb-3">إضافة زبون جديد</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="new_name">الاسم الكامل</label>
                                    <input type="text" class="form-control" id="new_name" name="new_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="new_phone">رقم الهاتف</label>
                                    <input type="text" class="form-control" id="new_phone" name="new_phone">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="new_identity">رقم الهوية</label>
                                    <input type="text" class="form-control" id="new_identity" name="new_identity">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="receiver_id" name="receiver_id">
                    </div>
                </div>

                <!-- باقي النموذج (تفاصيل التحويل، الملخص، الأزرار) -->
                <!-- ... (نفس الكود السابق) ... -->

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
$(document).ready(function() {
    // تهيئة Select2
    $('.select2').select2({
        placeholder: "اختر المرسل",
        allowClear: true
    });

    // البحث عن الزبون
    $('#searchCustomer').click(function() {

        alert("mmmmm");
        const searchTerm = $('#receiver_search').val().trim();

        if(searchTerm.length < 3) {
            alert('الرجاء إدخال رقم الهوية أو الهاتف (3 أحرف على الأقل)');
            return;
        }

        $.ajax({
            url: "{{ route('customers.search') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                search: searchTerm
            },
            success: function(response) {
                if(response.found) {
                    // عرض بيانات الزبون الموجود
                    $('#customerResult').html(`
                        <div class="alert alert-success">
                            <h5>${response.customer.name}</h5>
                            <p>الهاتف: ${response.customer.phone}</p>
                            <p>الهوية: ${response.customer.identity_number}</p>
                            <button type="button" class="btn btn-sm btn-primary mt-2"
                                    onclick="selectCustomer(${response.customer.id}, '${response.customer.name}')">
                                اختيار هذا الزبون
                            </button>
                        </div>
                    `);
                    $('#newCustomerForm').hide();
                } else {
                    // عرض خيار إضافة زبون جديد
                    $('#customerResult').html(`
                        <div class="alert alert-warning">
                            <p>لا يوجد زبون مسجل بهذه البيانات</p>
                            <button type="button" class="btn btn-sm btn-success"
                                    onclick="showNewCustomerForm('${searchTerm}')">
                                إضافة زبون جديد
                            </button>
                        </div>
                    `);
                }
            }
        });
    });
});

// اختيار زبون موجود
function selectCustomer(customerId, customerName) {
    $('#receiver_id').val(customerId);
    $('#customerResult').html(`
        <div class="alert alert-info">
            <p>الزبون المحدد: <strong>${customerName}</strong></p>
        </div>
    `);
    $('#newCustomerForm').hide();
}

// عرض نموذج إضافة زبون جديد
function showNewCustomerForm(searchTerm) {
    $('#newCustomerForm').show();
    $('#new_phone').val(searchTerm);
    $('#customerResult').html('');
}

// عند إدخال بيانات الزبون الجديد
$('#newCustomerForm').on('submit', function(e) {
    e.preventDefault();

    const formData = {
        name: $('#new_name').val(),
        phone: $('#new_phone').val(),
        identity_number: $('#new_identity').val(),
        _token: "{{ csrf_token() }}"
    };

    $.ajax({
        url: "{{ route('customers.store') }}",
        method: 'POST',
        data: formData,
        success: function(response) {
            if(response.success) {
                selectCustomer(response.customer.id, response.customer.name);
                alert('تم إضافة الزبون بنجاح');
            }
        }
    });
});
</script>
@endpush


@section('styles')
<style>
/* ... (نفس الأنماط السابقة) ... */
.select2-container--default .select2-selection--single {
    height: 38px;
    padding-top: 4px;
}
</style>
@endsection
