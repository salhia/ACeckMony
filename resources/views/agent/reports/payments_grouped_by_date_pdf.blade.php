<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payments Grouped by Date</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h3>Payments Grouped by Date</h3>
    @foreach($payments as $date => $group)
        <h4>{{ $date }}</h4>
        @php
            $totalAmount = $group->sum('amount');
            $totalPaid = $group->sum('paid_amount');
        @endphp
        <p>
            <strong>Total Amount:</strong> {{ number_format($totalAmount, 2) }} SSP |
            <strong>Total Paid:</strong> {{ number_format($totalPaid, 2) }} SSP
        </p>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Amount</th>
                    <th>Paid Amount</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group as $index => $payment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ number_format($payment->amount, 2) }} SSP</td>
                        <td>{{ number_format($payment->paid_amount, 2) }} SSP</td>
                        <td>{{ $payment->payment_notes ?? '-' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="1"><strong>Total</strong></td>
                    <td><strong>{{ number_format($group->sum('amount'), 2) }} SSP</strong></td>
                    <td><strong>{{ number_format($group->sum('paid_amount'), 2) }} SSP</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br>
    @endforeach
</body>
</html>
