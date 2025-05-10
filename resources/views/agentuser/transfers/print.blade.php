<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transfer Receipt</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .section { margin-bottom: 20px; }
        .line { margin: 4px 0; }
    </style>
</head>
<body>
    <div class="title">Transfer Receipt</div>

    <div class="section">
        <div class="line"><strong>Transaction Code:</strong> {{ $transaction->transaction_code }}</div>
        <div class="line"><strong>Date:</strong> {{ $transaction->created_at->format('Y-m-d H:i') }}</div>
        <div class="line"><strong>State:</strong> {{ $states[$transaction->state_code] ?? $transaction->state_code }}</div>
    </div>

    <div class="section">
        <strong>Sender:</strong>
        <div class="line">Name: {{ $transaction->senderCustomer->name }}</div>
        <div class="line">Phone: {{ $transaction->senderCustomer->phone }}</div>
    </div>

    <div class="section">
        <strong>Receiver:</strong>
        <div class="line">Name: {{ $transaction->receiverCustomer->name }}</div>
        <div class="line">Phone: {{ $transaction->receiverCustomer->phone }}</div>
    </div>

    <div class="section">
        <strong>Amount Details:</strong>
        <div class="line">Amount: {{ number_format($transaction->amount, 2) }} Pound</div>
        <div class="line">Commission: {{ number_format($transaction->commission, 2) }} Pound</div>
        <div class="line">Final Delivered: {{ number_format($transaction->final_delivered_amount, 2) }} Pound</div>
    </div>

    @if($transaction->notes)
        <div class="section">
            <strong>Notes:</strong>
            <div class="line">{{ $transaction->notes }}</div>
        </div>
    @endif
</body>
</html>
