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
                        <!-- Progress Steps -->
                        <div class="progress-tracker mb-4">
                            <div class="progress">
                                <div class="progress-bar progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="steps d-flex justify-content-between">
                                <div class="step active">
                                    <div class="step-circle">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <span class="step-text">State</span>
                                </div>
                                <div class="step">
                                    <div class="step-circle">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="step-text">Sender</span>
                                </div>
                                <div class="step">
                                    <div class="step-circle">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <span class="step-text">Receiver</span>
                                </div>
                                <div class="step">
                                    <div class="step-circle">
                                        <i class="fas fa-calculator"></i>
                                    </div>
                                    <span class="step-text">Amount</span>
                                </div>
                            </div>
                        </div>

                        <form id="transferForm" method="POST" action="{{ route('transfers.store') }}">
                            @csrf
                            <!-- Multi-step form sections -->
                            <div class="form-sections">
                                <!-- Step 1: State Selection -->
                                <div class="form-section animate__animated animate__fadeIn" id="state-section">
                                    <div class="section-header">
                                        <h4><i class="fas fa-map-marker-alt"></i> Select State</h4>
                                        <p class="text-muted">Choose the state where the transfer will take place</p>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-select form-control custom-select" id="region_id" name="region_id" required>
                                            <option value="">-- Select State --</option>
                                            @foreach($sys_regions as $state)
                                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="text-end mt-4">
                                        <button type="button" class="btn btn-primary btn-lg next-step" disabled>
                                            Continue <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 2: Sender Information -->
                                <div class="form-section d-none animate__animated" id="sender-section">
                                    <div class="section-header">
                                        <h4><i class="fas fa-user"></i> Sender Details</h4>
                                        <p class="text-muted">Search for existing sender or add a new one</p>
                                    </div>
                                    <div class="search-box glass-effect mb-4">
                                        <div class="input-group">
                                            <span class="input-group-text border-0 bg-transparent">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" class="form-control form-control-lg border-0 bg-transparent"
                                                   id="sender_search" placeholder="Search by ID or Phone Number">
                                            <button class="btn btn-primary" type="button" id="searchSender">
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                    <div id="senderResult" class="search-result"></div>
                                    <input type="hidden" id="sender_id" name="sender_id">
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-outline-secondary btn-lg prev-step">
                                            <i class="fas fa-arrow-left me-2"></i> Back
                                        </button>
                                        <button type="button" class="btn btn-primary btn-lg next-step" disabled>
                                            Continue <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 3: Receiver Information -->
                                <div class="form-section d-none animate__animated" id="receiver-section">
                                    <div class="section-header">
                                        <h4><i class="fas fa-user-plus"></i> Receiver Details</h4>
                                        <p class="text-muted">Search for existing receiver or add a new one</p>
                                    </div>
                                    <div class="search-box glass-effect mb-4">
                                        <div class="input-group">
                                            <span class="input-group-text border-0 bg-transparent">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" class="form-control form-control-lg border-0 bg-transparent"
                                                   id="receiver_search" placeholder="Search by ID or Phone Number">
                                            <button class="btn btn-success" type="button" id="searchReceiver">
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                    <div id="receiverResult" class="search-result"></div>
                                    <input type="hidden" id="receiver_id" name="receiver_id">
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-outline-secondary btn-lg prev-step">
                                            <i class="fas fa-arrow-left me-2"></i> Back
                                        </button>
                                        <button type="button" class="btn btn-primary btn-lg next-step" disabled>
                                            Continue <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 4: Amount and Commission -->
                                <div class="form-section d-none animate__animated" id="amount-section">
                                    <div class="section-header">
                                        <h4><i class="fas fa-calculator"></i> Transfer Amount</h4>
                                        <p class="text-muted">Enter the amount and review commission details</p>
                                    </div>
                                    <div class="amount-input-section glass-effect mb-4">
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text border-0 bg-transparent">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                            <input type="number" class="form-control form-control-lg border-0 bg-transparent"
                                                   id="amount" name="amount" required placeholder="Enter amount">
                                        </div>
                                    </div>

                                    <div class="commission-card glass-effect">
                                        <div class="commission-header">
                                            <i class="fas fa-receipt"></i>
                                            <span>Transaction Summary</span>
                                        </div>
                                        <div class="commission-details">
                                            <div class="detail-item">
                                                <span class="detail-label">Base Amount</span>
                                                <span class="detail-value" id="baseAmount">0.00</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Commission Rate</span>
                                                <span class="detail-value" id="commissionRate">
                                                    {{ auth()->user()->commissionRate ?? 0 }}%
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Commission Amount</span>
                                                <span class="detail-value" id="commissionAmount">0.00</span>
                                            </div>
                                            <div class="detail-item total">
                                                <span class="detail-label">Net Amount</span>
                                                <span class="detail-value" id="netAmount">0.00</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-4">
                                        <div class="glass-effect p-3">
                                            <label class="form-label">
                                                <i class="fas fa-sticky-note"></i> Additional Notes
                                            </label>
                                            <textarea class="form-control border-0 bg-transparent"
                                                      id="notes" name="notes" rows="2"
                                                      placeholder="Add any additional notes..."></textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-outline-secondary btn-lg prev-step">
                                            <i class="fas fa-arrow-left me-2"></i> Back
                                        </button>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-paper-plane me-2"></i> Complete Transfer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-effect">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    <span id="modalTitle">Add New Customer</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="customer_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="customer_phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Number</label>
                        <input type="text" class="form-control" id="customer_identity_number" name="identity_number">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCustomer">
                    <i class="fas fa-save me-2"></i>Save Customer
                </button>
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

/* Progress Tracker */
.progress-tracker {
    position: relative;
    margin-bottom: 3rem;
}

.progress {
    height: 3px;
    background: #e9ecef;
    margin-bottom: 1.5rem;
}

.progress-bar {
    background: linear-gradient(45deg, #007bff, #00bcd4);
    transition: width 0.4s ease;
}

.steps {
    position: relative;
    margin-top: -12px;
}

.step {
    text-align: center;
    position: relative;
    z-index: 1;
}

.step-circle {
    width: 40px;
    height: 40px;
    background: #fff;
    border: 2px solid #dee2e6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    transition: all 0.3s ease;
}

.step-circle i {
    color: #6c757d;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.step.active .step-circle {
    background: linear-gradient(45deg, #007bff, #00bcd4);
    border-color: transparent;
    transform: scale(1.1);
    box-shadow: 0 0 20px rgba(0,123,255,0.3);
}

.step.active .step-circle i {
    color: #fff;
}

.step-text {
    font-size: 0.875rem;
    color: #6c757d;
    transition: all 0.3s ease;
}

.step.active .step-text {
    color: #007bff;
    font-weight: 600;
}

/* Glass Effect */
.glass-effect {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
}

/* Form Sections */
.form-section {
    transition: all 0.4s ease;
}

.section-header {
    margin-bottom: 2rem;
    text-align: center;
}

.section-header h4 {
    color: #344767;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.section-header p {
    font-size: 0.9rem;
}

/* Commission Card */
.commission-card {
    padding: 1.5rem;
    margin-top: 2rem;
}

.commission-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px dashed rgba(0,0,0,0.1);
    color: #344767;
    font-weight: 600;
}

.commission-details .detail-item {
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

/* Search Results */
.search-result {
    min-height: 100px;
    transition: all 0.3s ease;
}

/* Custom Select */
.custom-select {
    height: 50px;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,0.1);
    padding: 0 1rem;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.custom-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
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

.btn-primary {
    background: linear-gradient(45deg, #007bff, #00bcd4);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #0056b3, #008ba3);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
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

/* Animations */
.animate__animated {
    animation-duration: 0.6s;
}

/* Responsive Design */
@media (max-width: 768px) {
    .step-circle {
        width: 35px;
        height: 35px;
    }

    .step-text {
        font-size: 0.75rem;
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
    }
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

    .section-header h4 {
        color: #e2e8f0;
    }
}
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 4;

    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        $('#currentTime').text(timeString);
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Update progress bar with animation
    function updateProgress() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        $('.progress-bar').css('width', `${progress}%`);

        // Update steps
        $('.step').removeClass('active');
        $(`.step:nth-child(-n+${currentStep})`).addClass('active');
    }

    // Navigation between steps with animations
    $('.next-step').click(function() {
        if (currentStep < totalSteps) {
            const currentSection = $(`.form-section:eq(${currentStep - 1})`);
            const nextSection = $(`.form-section:eq(${currentStep})`);

            currentSection.removeClass('animate__fadeIn').addClass('animate__fadeOutLeft');
            setTimeout(() => {
                currentSection.addClass('d-none');
                nextSection.removeClass('d-none animate__fadeOutLeft')
                          .addClass('animate__fadeIn');
                currentStep++;
                updateProgress();
            }, 300);
        }
    });

    $('.prev-step').click(function() {
        if (currentStep > 1) {
            const currentSection = $(`.form-section:eq(${currentStep - 1})`);
            const prevSection = $(`.form-section:eq(${currentStep - 2})`);

            currentSection.removeClass('animate__fadeIn').addClass('animate__fadeOutRight');
            setTimeout(() => {
                currentSection.addClass('d-none');
                prevSection.removeClass('d-none animate__fadeOutRight')
                          .addClass('animate__fadeIn');
                currentStep--;
                updateProgress();
            }, 300);
        }
    });

    // Enable next button when selection is made
    $('#region_id').change(function() {
        const nextButton = $('#state-section .next-step');
        if ($(this).val()) {
            nextButton.prop('disabled', false)
                     .addClass('animate__animated animate__pulse');
        } else {
            nextButton.prop('disabled', true)
                     .removeClass('animate__animated animate__pulse');
        }
    });

    // Search functionality with loading animation
    function searchCustomer(searchTerm, type) {
        if (searchTerm.length < 3) {
            showAlert('Please enter at least 3 characters', 'warning');
            return;
        }

        const searchButton = $(`#search${type}`);
        const resultDiv = $(`#${type.toLowerCase()}Result`);

        $.ajax({
            url: "{{ route('customers.search') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                search: searchTerm
            },
            beforeSend: function() {
                searchButton.html('<i class="fas fa-spinner fa-spin"></i>');
                resultDiv.html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
            },
            success: function(response) {
                if (response.found) {
                    resultDiv.html(`
                        <div class="card glass-effect border-0 animate__animated animate__fadeIn">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="customer-avatar">
                                        <i class="fas fa-user-circle fa-3x text-primary"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="card-title mb-1">${response.customer.name}</h5>
                                        <p class="card-text mb-1">
                                            <i class="fas fa-phone text-muted"></i> ${response.customer.phone}
                                        </p>
                                        <p class="card-text mb-3">
                                            <i class="fas fa-id-card text-muted"></i> ${response.customer.identity_number}
                                        </p>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="selectCustomer(${response.customer.id}, '${response.customer.name}', '${type.toLowerCase()}')">
                                            <i class="fas fa-check"></i> Select ${type}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    resultDiv.html(`
                        <div class="alert glass-effect animate__animated animate__fadeIn">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x text-info me-3"></i>
                                <div>
                                    <p class="mb-2">No ${type.toLowerCase()} found</p>
                                    <button type="button" class="btn btn-sm btn-primary"
                                            onclick="showCustomerModal('${searchTerm}', '${type.toLowerCase()}')">
                                        <i class="fas fa-plus"></i> Add New ${type}
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                showAlert(xhr.responseJSON?.message || 'Search failed', 'danger');
            },
            complete: function() {
                searchButton.html('Search');
            }
        });
    }

    // Real-time commission calculation with animation
    let calculateTimeout;
    $('#amount').on('input', function() {
        clearTimeout(calculateTimeout);
        calculateTimeout = setTimeout(calculateCommission, 300);
    });

    function calculateCommission() {
        const amount = parseFloat($('#amount').val()) || 0;
        const commissionRate = {{ auth()->user()->commissionRate ?? 0 }};

        const commission = (amount * commissionRate) / 100;
        const netAmount = amount - commission;

        // Animate the numbers
        animateNumber('#baseAmount', amount);
        animateNumber('#commissionAmount', commission);
        animateNumber('#netAmount', netAmount);
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

    // Search button handlers
    $('#searchSender').click(() => searchCustomer($('#sender_search').val(), 'Sender'));
    $('#searchReceiver').click(() => searchCustomer($('#receiver_search').val(), 'Receiver'));

    // Form validation
    $('#transferForm').submit(function(e) {
        e.preventDefault();

        if (!$('#sender_id').val() || !$('#receiver_id').val() || !$('#amount').val()) {
            showAlert('Please complete all required fields', 'warning');
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

// Select customer with animation
function selectCustomer(id, name, type) {
    $(`#${type}_id`).val(id);
    const resultDiv = $(`#${type}Result`);

    resultDiv.html(`
        <div class="alert glass-effect animate__animated animate__fadeIn">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x text-success me-3"></i>
                <div>
                    <h6 class="mb-0">Selected ${type}</h6>
                    <p class="mb-0"><strong>${name}</strong></p>
                </div>
            </div>
        </div>
    `);

    // Enable and animate next button
    const nextButton = $(`#${type}-section .next-step`);
    nextButton.prop('disabled', false)
              .addClass('animate__animated animate__pulse');
}

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

// Add these new functions
let currentCustomerType = '';

function showCustomerModal(searchTerm, type) {
    currentCustomerType = type;
    const modal = new bootstrap.Modal(document.getElementById('customerModal'));

    // Clear previous form data
    $('#customerForm')[0].reset();

    // Pre-fill phone if searchTerm looks like a phone number
    if (/^\d+$/.test(searchTerm)) {
        $('#customer_phone').val(searchTerm);
    }

    // Update modal title
    $('#modalTitle').text(`Add New ${type.charAt(0).toUpperCase() + type.slice(1)}`);

    modal.show();
}

// Save customer function
$('#saveCustomer').click(function() {
    const button = $(this);
    const originalText = button.html();

    // Basic validation
    const name = $('#customer_name').val();
    const phone = $('#customer_phone').val();
    const identityNumber = $('#customer_identity_number').val();

    if (!name || !phone) {
        showAlert('Please fill in all required fields', 'warning');
        return;
    }

    // Show loading state
    button.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);

    // Send request to store customer
    $.ajax({
        url: "{{ route('customers.store') }}",
        method: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            name: name,
            phone: phone,
            identity_number: identityNumber
        },
        success: function(response) {
            if (response.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();

                // Show success message
                showAlert('Customer added successfully', 'success');

                // Select the newly created customer
                selectCustomer(response.customer.id, response.customer.name, currentCustomerType);
            } else {
                showAlert(response.message || 'Failed to add customer', 'danger');
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                const errorMessages = Object.values(errors).flat();
                showAlert(errorMessages.join('<br>'), 'danger');
            } else {
                showAlert('Failed to add customer', 'danger');
            }
        },
        complete: function() {
            button.html(originalText).prop('disabled', false);
        }
    });
});
</script>
@endpush


