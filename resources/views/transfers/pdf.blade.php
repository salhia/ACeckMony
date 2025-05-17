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
            position: relative;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 15px;
            display: block;
            margin: 0 auto;
        }
        .title {
            color: #007bff;
            font-size: 24px;
            margin: 10px 0;
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
        .financial-summary {
            background-color: #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .financial-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #dee2e6;
        }
        .financial-row:last-child {
            border-bottom: none;
            padding-top: 15px;
            margin-top: 5px;
            border-top: 2px solid #dee2e6;
        }
        .financial-label {
            color: #495057;
            font-weight: bold;
        }
        .financial-value {
            color: #28a745;
            font-weight: bold;
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
            <img src="{{ public_path('image/icon.png') }}" alt="AKEC Money Logo" class="logo">
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
                <span class="detail-label">Status:</span>
                <div class="status status-{{ strtolower($transfer->status) }}">
                    {{ $transfer->status }}
                </div>
            </div>
        </div>

        <div class="financial-summary">
            <h3 style="margin-top: 0; color: #007bff;">Financial Summary</h3>
            <div class="financial-row">
                <span class="financial-label">Base Amount:</span>
                <span class="financial-value">{{ $amount }} SSP</span>
            </div>
            <div class="financial-row">
                <span class="financial-label">Commission:</span>
                <span class="financial-value">{{ $commission }} SSP</span>
            </div>
            <div class="financial-row">
                <span class="financial-label">Net Amount:</span>
                <span class="financial-value">{{ $net_amount }} SSP</span>
            </div>
        </div>

        <div class="transfer-details">
            <h3 style="margin-top: 0; color: #007bff;">Sender Details</h3>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $sender->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $sender->phone }}</span>
            </div>
        </div>

        <div class="transfer-details">
            <h3 style="margin-top: 0; color: #007bff;">Receiver Details</h3>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $receiver->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $receiver->phone }}</span>
            </div>
        </div>

        <div class="footer">
            <p>This is an official transfer receipt from AKEC Money Transfer Services.</p>
            <p>For any inquiries, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
