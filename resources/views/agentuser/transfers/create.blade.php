@extends('agentuser.user_dashboard')
@section('agentuser')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

<div class="page-content">

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="transfer-container">
                <div class="card main-card">
                    <div class="card-header bg-gradient-primary">
                        <div class="header-content">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-money-bill-transfer fa-fw"></i> Quick Money Transfer
                            </h3>
                            <div class="transfer-stats">
                                <div class="stat-item">
                                    <i class="fas fa-clock"></i>
                                    <span id="currentTime"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form id="transferForm" method="POST" action="{{ route('transfers.store') }}">
                            @csrf
                            <!-- Hidden fields for calculated amounts -->
                            <input type="hidden" id="calculated_net_amount" name="amount">
                            <input type="hidden" id="calculated_commission" name="commission">
                            <input type="hidden" id="calculated_total" name="total_amount">

                            <div class="row">
                                <!-- State Selection -->
                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label class="form-label">Select Region <span style="color:red">*</span></label>
                                        <select class="form-select form-control custom-select" id="region_id" name="region_id" required>
                                            <option value="">-- Select Region --</option>
                                            @foreach($sys_regions as $region)
                                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Sender Information -->
                                <div class="col-md-6 mb-4">
                                    <div class="glass-effect p-4">
                                        <h5 class="mb-3"><i class="fas fa-user"></i> Sender Information</h5>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Full Name <span style="color:red">*</span></label>
                                            <input type="text" class="form-control" id="sender_name" name="sender_name" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="sender_phone" name="sender_phone">
                                        </div>
                                        {{-- <div class="form-group">
                                            <label class="form-label">ID Number</label>
                                            <input type="text" class="form-control" id="sender_identity_number" name="sender_identity_number">
                                        </div> --}}
                                    </div>
                                </div>

                                <!-- Receiver Information -->
                                <div class="col-md-6 mb-4">
                                    <div class="glass-effect p-4">
                                        <h5 class="mb-3"><i class="fas fa-user-plus"></i> Receiver Information</h5>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Full Name <span style="color:red">*</span></label>
                                            <input type="text" class="form-control" id="receiver_name" name="receiver_name" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="receiver_phone" name="receiver_phone" >
                                        </div>
                                        {{-- <div class="form-group">
                                            <label class="form-label">ID Number</label>
                                            <input type="text" class="form-control" id="receiver_identity_number" name="receiver_identity_number">
                                        </div> --}}
                                    </div>
                                </div>

                                <!-- Amount Section -->
                                <div class="col-md-12">
                                    <div class="glass-effect p-4">
                                        <h5 class="mb-3"><i class="fas fa-calculator"></i> Transfer Amount</h5>
                                        <div class="form-group mb-4">
                                            <label class="form-label">Net Amount (To be paid) <span style="color:red">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text border-0 bg-transparent">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </span>
                                                <input type="number" class="form-control form-control-lg border-0 bg-transparent"
                                                       id="netAmount" name="amount" required placeholder="Enter net amount to be paid">
                                            </div>
                                        </div>

                                        <div class="commission-details">
                                            <div class="detail-item">
                                                <span class="detail-label">Net Amount (To be paid)</span>
                                                <span class="detail-value" id="netAmountDisplay">0.00</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Commission Rate</span>
                                                <span class="detail-value" id="commission_rate">
                                                    {{ auth()->user()->commission_rate ?? 0 }}%
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Commission Amount</span>
                                                <input type="text" class="form-control form-control-sm" id="commissionAmountInput" readonly value="0.00">
                                            </div>
                                            <div class="detail-item total" style="display: none;">
                                                <span class="detail-label">Total Amount (Including Commission)</span>
                                                <span class="detail-value" id="totalAmount">0.00</span>
                                            </div>
                                        </div>

                                        <div class="form-group mt-4">
                                            <label class="form-label">
                                                <i class="fas fa-sticky-note"></i> Additional Notes
                                            </label>
                                            <textarea class="form-control border-0 bg-transparent"
                                                      id="notes" name="notes" rows="2"
                                                      placeholder="Add any additional notes..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i> Complete Transfer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
/* Main Container Styles */
.transfer-container {
    position: relative;
    z-index: 1;
}

.main-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 0 40px rgba(0,0,0,0.1);
    background: #fff;
    overflow: hidden;
}

.card-header {
    border: none;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

.header-content {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    color: #fff;
    font-size: 1.5rem;
    font-weight: 600;
}

.transfer-stats {
    display: flex;
    gap: 1rem;
}

.stat-item {
    color: #fff;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Glass Effect */
.glass-effect {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
}

/* Form Styles */
.form-label {
    font-weight: 500;
    color: #344767;
    margin-bottom: 0.5rem;
}

.form-control {
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,0.1);
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

/* Commission Details */
.commission-details {
    background: rgba(0,0,0,0.02);
    border-radius: 8px;
    padding: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px dashed rgba(0,0,0,0.1);
}

.detail-label {
    color: #6c757d;
    font-weight: 500;
}

.detail-value {
    font-weight: 600;
    color: #344767;
}

.detail-item.total {
    margin-top: 0.5rem;
    padding-top: 1rem;
    border-top: 2px solid rgba(0,0,0,0.1);
}

.detail-item.total .detail-label,
.detail-item.total .detail-value {
    font-size: 1.1rem;
    font-weight: 700;
    background: linear-gradient(45deg, #007bff, #00bcd4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Buttons */
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-lg {
    padding: 1rem 2rem;
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(45deg, #218838, #1aa179);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40,167,69,0.3);
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .main-card {
        background: #1a1f2c;
    }

    .glass-effect {
        background: rgba(26, 31, 44, 0.9);
    }

    .detail-label {
        color: #a0aec0;
    }

    .detail-value {
        color: #e2e8f0;
    }

    .form-label {
        color: #e2e8f0;
    }
}
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        $('#currentTime').text(timeString);
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Real-time commission calculation with animation
    let calculateTimeout;
    $('#netAmount').on('input', function() {
        clearTimeout(calculateTimeout);
        calculateTimeout = setTimeout(calculateCommission, 300);
    });

    function calculateCommission() {
        const netAmount = parseFloat($('#netAmount').val()) || 0;
        const commission_rate = {{ auth()->user()->commission_rate ?? 0 }};

        // Calculate total amount based on net amount

        // Calculate the commission difference
        const commission = (netAmount * commission_rate) / 100;

          const totalAmount = netAmount + commission;

        // Update displays

        animateNumber('#netAmountDisplay', netAmount);
        $('#commissionAmountInput').val(commission.toFixed(2));
        animateNumber('#totalAmount', totalAmount);

        // Update hidden fields for form submission

        $('#calculated_net_amount').val(netAmount.toFixed(2));
        $('#calculated_commission').val(commission.toFixed(2));
        $('#calculated_total').val(netAmount.toFixed(2));
    }

    function animateNumber(selector, value) {
        const element = $(selector);
        const start = parseFloat(element.text()) || 0;
        const duration = 500;
        const steps = 20;
        const increment = (value - start) / steps;
        let current = start;
        let step = 0;

        const interval = setInterval(() => {
            step++;
            current += increment;
            element.text(current.toFixed(2));

            if (step >= steps) {
                clearInterval(interval);
                element.text(value.toFixed(2));
            }
        }, duration / steps);
    }

    // Form validation
    $('#transferForm').submit(function(e) {
        e.preventDefault();

        // Validate required fields
        const requiredFields = [
            'region_id', 'sender_name',
            'receiver_name',  'netAmount'
        ];

        let isValid = true;
        requiredFields.forEach(field => {
            if (!$(`#${field}`).val()) {
                isValid = false;
                showAlert(`Please fill in ${field.replace(/_/g, ' ')}`, 'warning');
                return false;
            }
        });

        if (!isValid) return;

        // Validate amounts
        const netAmount = parseFloat($('#netAmount').val());
        const commission = parseFloat($('#commissionAmountInput').val());
        const total = parseFloat($('#totalAmount').text());

        if (isNaN(netAmount) || isNaN(commission) || isNaN(total)) {
            showAlert('Invalid amount calculations', 'danger');
            return;
        }

        // Add loading state to submit button
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...')
                .prop('disabled', true);

        // Submit the form
        this.submit();
    });
});

// Enhanced alert system
function showAlert(message, type) {
    const alert = $(`
        <div class="alert alert-${type} glass-effect animate__animated animate__fadeInDown" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' :
                               type === 'warning' ? 'exclamation-triangle' :
                               type === 'danger' ? 'times-circle' : 'info-circle'} fa-2x me-3"></i>
                <div>${message}</div>
                <button type="button" class="btn-close ms-auto" data-dismiss="alert"></button>
            </div>
        </div>
    `);

    $('.page-content').prepend(alert);

    setTimeout(() => {
        alert.removeClass('animate__fadeInDown').addClass('animate__fadeOutUp');
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}
</script>
@endpush


