<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transfer Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verify-container {
            max-width: 500px;
            margin: 50px auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(45deg, #007bff, #00bcd4);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .verification-steps .step {
            display: none;
        }
        .verification-steps .step.active {
            display: block;
        }
        .transfer-card {
            background: linear-gradient(145deg, #ffffff, #f5f5f5);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            transition: transform 0.3s ease;
        }
        .transfer-card:hover {
            transform: translateY(-5px);
        }
        .qr-code-container {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code-container img {
            max-width: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .amount-display {
            font-size: 1.5rem;
            font-weight: 700;
            color: #28a745;
            text-align: center;
            margin: 10px 0;
        }
        .transfer-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .status-completed { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .btn-download {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40,167,69,0.3);
            color: white;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .currency {
            font-size: 0.8em;
            color: #666;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verify-container">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode me-2"></i>Transfer Verification
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="verification-steps">
                        <!-- Step 1: Phone Verification -->
                        <div class="step active" id="step-phone">
                            <div class="text-center mb-4">
                                <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                                <h5>Enter Your Phone Number</h5>
                                <p class="text-muted">We'll verify if you have any transfers</p>
                            </div>
                            <div class="form-group">
                                <input type="tel" class="form-control phone-input text-center"
                                       id="phone" placeholder="Enter your phone number"
                                       pattern="[0-9]{10}">
                            </div>
                            <div class="text-center mt-4">
                                <button class="btn btn-primary text-white" onclick="verifyPhone()">
                                    Verify Number
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Transfers List -->
                        <div class="step" id="step-transfers">
                            <div class="text-center mb-4">
                                <i class="fas fa-list-alt fa-3x text-primary mb-3"></i>
                                <h5>Available Transfers</h5>
                                <p class="text-muted">Select a transfer to view details</p>
                            </div>
                            <div id="transfers-container">
                                <!-- Transfers will be loaded here -->
                            </div>
                            <div class="text-center mt-3">
                                <button class="btn btn-outline-secondary" onclick="showPhoneStep()">
                                    <i class="fas fa-arrow-left me-2"></i>Change Number
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Handle direct verification if transfer data is provided
        @if(isset($directVerification) && $directVerification)
            $(document).ready(function() {
                displayTransfers([{!! json_encode($transfer) !!}]);
                showStep('transfers');
            });
        @endif

        function showStep(stepId) {
            $('.step').removeClass('active');
            $(`#step-${stepId}`).addClass('active');
        }

        function showPhoneStep() {
            showStep('phone');
            $('#transfers-container').empty();
        }

        function showLoading() {
            $('#loading-overlay').css('display', 'flex');
        }

        function hideLoading() {
            $('#loading-overlay').css('display', 'none');
        }

        function verifyPhone() {
            const phone = $('#phone').val();
            if (!phone) {
                alert('Please enter your phone number');
                return;
            }

            showLoading();
            const button = $('.btn-verify');
            const originalText = button.html();
            button.html('<i class="fas fa-spinner fa-spin"></i> Verifying...').prop('disabled', true);

            $.ajax({
                url: '/api/transfers/verify-phone',
                method: 'POST',
                data: { phone: phone },
                success: function(response) {
                    if (response.hasTransfers) {
                        displayTransfers(response.transfers);
                        showStep('transfers');
                    } else {
                        alert('No transfers found for this number');
                    }
                },
                error: function(xhr) {
                    console.error('Verification error:', xhr);
                    alert(xhr.responseJSON?.message || 'Verification failed. Please try again.');
                },
                complete: function() {
                    button.html(originalText).prop('disabled', false);
                    hideLoading();
                }
            });
        }

        function displayTransfers(transfers) {
            const container = $('#transfers-container');
            container.empty();

            transfers.forEach(transfer => {
                const card = $(`
                    <div class="transfer-card">
                        <div class="text-center">
                            <span class="transfer-status status-${transfer.status.toLowerCase()}">
                                ${transfer.status}
                            </span>
                        </div>
                        <div class="amount-display">
                            ${transfer.amount} <span class="currency">SSP</span>
                        </div>
                        <div class="qr-code-container">
                            <img src="${transfer.qr_code}" alt="QR Code">
                        </div>
                        <div class="text-center mt-3">
                            <a href="${transfer.pdf_url}" class="btn btn-download" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i>Download Details
                            </a>
                        </div>
                    </div>
                `);
                container.append(card);
            });
        }
    </script>

    @if(session('error'))
    <script>
        $(document).ready(function() {
            alert('{{ session('error') }}');
        });
    </script>
    @endif
</body>
</html>
