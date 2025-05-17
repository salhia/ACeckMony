@extends('agentuser.user_dashboard')
@section('agentuser')

<div class="page-content">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="success-animation mb-4">
                        <i class="fas fa-check-circle text-success fa-5x"></i>
                    </div>

                    <h3 class="mb-4">Transfer Successful!</h3>

                    <div class="transfer-details mb-4">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="detail-item d-flex justify-content-between py-2">
                                    <span class="label">Amount:</span>
                                    <span class="value">{{ number_format($transfer->amount, 2) }}</span>
                                </div>
                                <div class="detail-item d-flex justify-content-between py-2">
                                    <span class="label">Receiver:</span>
                                    <span class="value">{{ $transfer->receiver->name }}</span>
                                </div>
                                <div class="detail-item d-flex justify-content-between py-2">
                                    <span class="label">Date:</span>
                                    <span class="value">{{ $transfer->created_at->format('Y-m-d H:i:s') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="qr-section mb-4">
                        <h5 class="mb-3">Verification QR Code</h5>
                        <div class="qr-container mx-auto" style="max-width: 200px;">
                            <img src="{{ route('transfers.qr-code', $transfer->id) }}"
                                 alt="Transfer QR Code"
                                 class="img-fluid">
                        </div>
                        <p class="text-muted mt-3">
                            The receiver can scan this QR code to verify the transfer
                        </p>
                    </div>

                    <div class="verification-link mb-4">
                        <h5 class="mb-3">Verification Link</h5>
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   value="{{ route('transfers.verify') }}"
                                   id="verificationLink" readonly>
                            <button class="btn btn-outline-primary" type="button"
                                    onclick="copyLink()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p class="text-muted mt-2">
                            Share this link with the receiver to verify the transfer
                        </p>
                    </div>

                    <div class="actions">
                        <a href="{{ route('transfers.create') }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus"></i> New Transfer
                        </a>
                        <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> View All Transfers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-animation {
    animation: scaleIn 0.5s ease-in-out;
}

.detail-item {
    border-bottom: 1px dashed #dee2e6;
}

.detail-item:last-child {
    border-bottom: none;
}

.label {
    color: #6c757d;
    font-weight: 500;
}

.value {
    font-weight: 600;
    color: #344767;
}

.qr-container {
    padding: 1rem;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

<script>
function copyLink() {
    const linkInput = document.getElementById('verificationLink');
    linkInput.select();
    document.execCommand('copy');

    // Show feedback
    const button = event.currentTarget;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(() => {
        button.innerHTML = originalText;
    }, 2000);
}
</script>

@endsection
