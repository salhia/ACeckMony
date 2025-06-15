<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment History PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h3>Payment History</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Paid Amount</th>
                <th>Payment Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allPayments as $index => $payment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ number_format($payment->amount, 2) }} SSP</td>
                    <td>{{ $payment->status }}</td>
                    <td>{{ number_format($payment->paid_amount, 2) ?? '-' }} SSP</td>
                    <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $payment->payment_notes ?? '-' }}</td>
                </tr>
            @endforeach

            @php
                $totalAmount = $allPayments->sum('amount');
                $totalPaid = $allPayments->sum('paid_amount');
            @endphp
            <tr>
                <td colspan="1"><strong>Total</strong></td>
                <td><strong>{{ number_format($totalAmount, 2) }} SSP</strong></td>
                <td></td>
                <td><strong>{{ number_format($totalPaid, 2) }} SSP</strong></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
