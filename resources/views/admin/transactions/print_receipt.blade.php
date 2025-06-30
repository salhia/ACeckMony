<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - {{ $transaction->transaction_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .transaction-info {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .customer-section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .amount-section {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .receipt {
                border: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="company-name">ACeckMony</div>
            <div class="receipt-title">Transaction Receipt</div>
            <div>Transaction ID: {{ $transaction->transaction_code }}</div>
        </div>

        <div class="transaction-info">
            <div class="info-row">
                <div class="info-label">Date:</div>
                <div class="info-value">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">{{ ucfirst($transaction->status) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Region:</div>
                <div class="info-value">{{ $transaction->region ? $transaction->region->name : 'N/A' }}</div>
            </div>
        </div>

        <div class="customer-section">
            <div class="section-title">Sender Information</div>
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">
                    @if($transaction->senderCustomer)
                        {{ $transaction->senderCustomer->name }}
                    @elseif($transaction->senderAgent)
                        {{ $transaction->senderAgent->name }} (Agent)
                    @elseif($transaction->senderUser)
                        {{ $transaction->senderUser->name }} (User)
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">
                    @if($transaction->senderCustomer)
                        {{ $transaction->senderCustomer->phone }}
                    @elseif($transaction->senderAgent)
                        {{ $transaction->senderAgent->phone }}
                    @elseif($transaction->senderUser)
                        {{ $transaction->senderUser->phone }}
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">
                    @if($transaction->senderCustomer)
                        {{ $transaction->senderCustomer->email }}
                    @elseif($transaction->senderAgent)
                        {{ $transaction->senderAgent->email }}
                    @elseif($transaction->senderUser)
                        {{ $transaction->senderUser->email }}
                    @endif
                </div>
            </div>
        </div>

        <div class="customer-section">
            <div class="section-title">Receiver Information</div>
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">
                    @if($transaction->receiverCustomer)
                        {{ $transaction->receiverCustomer->name }}
                    @elseif($transaction->receiverAgent)
                        {{ $transaction->receiverAgent->name }} (Agent)
                    @elseif($transaction->receiverUser)
                        {{ $transaction->receiverUser->name }} (User)
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">
                    @if($transaction->receiverCustomer)
                        {{ $transaction->receiverCustomer->phone }}
                    @elseif($transaction->receiverAgent)
                        {{ $transaction->receiverAgent->phone }}
                    @elseif($transaction->receiverUser)
                        {{ $transaction->receiverUser->phone }}
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">
                    @if($transaction->receiverCustomer)
                        {{ $transaction->receiverCustomer->email }}
                    @elseif($transaction->receiverAgent)
                        {{ $transaction->receiverAgent->email }}
                    @elseif($transaction->receiverUser)
                        {{ $transaction->receiverUser->email }}
                    @endif
                </div>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount-row">
                <div>Amount:</div>
                <div>{{ number_format($transaction->amount, 2) }}</div>
            </div>
            <div class="amount-row">
                <div>Commission:</div>
                <div>{{ number_format($transaction->commission, 2) }}</div>
            </div>
            <div class="amount-row">
                <div>Vendor Commission:</div>
                <div>{{ number_format($transaction->admin_fee, 2) }}</div>
            </div>
            <div class="amount-row">
                <div>Net Amount:</div>
                <div>{{ number_format($transaction->net_amount, 2) }}</div>
            </div>
            <div class="amount-row" style="font-weight: bold;">
                <div>Final Delivered Amount:</div>
                <div>{{ number_format($transaction->final_delivered_amount, 2) }}</div>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p>Thank you for using ACeckMony!</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Receipt</button>
    </div>
</body>
</html>
