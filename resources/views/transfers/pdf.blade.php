<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Transfer Details</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .title {
            color: #007bff;
            font-size: 24px;
            margin: 0;
        }
        .transfer-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .amount {
            font-size: 28px;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .currency {
            font-size: 16px;
            color: #666;
            margin-left: 5px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px dashed #dee2e6;
            padding-bottom: 10px;
        }
        .detail-label {
            color: #6c757d;
            font-weight: bold;
        }
        .detail-value {
            color: #212529;
        }
        .status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            text-align: center;
            margin: 20px auto;
            width: auto;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(config('app.logo'))
                <img src="{{ config('app.logo') }}" alt="Logo" class="logo">
            @endif
            <h1 class="title">Transfer Details</h1>
        </div>

        <div class="transfer-details">
            <div class="amount">
                {{ $amount }} <span class="currency">SSP</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Transfer ID:</span>
                <span class="detail-value">{{ $transfer->transaction_code }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Transfer Date:</span>
                <span class="detail-value">{{ $date }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Sender:</span>
                <span class="detail-value">{{ $sender->name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Receiver:</span>
                <span class="detail-value">{{ $receiver->name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <div class="status status-{{ strtolower($transfer->status) }}">
                    {{ $transfer->status }}
                </div>
            </div>
        </div>

        <div class="footer">
            <p>This document was automatically generated. Please keep it for your records.</p>
            <p>{{ config('app.name') }} &copy; {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
