<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1e2a37; font-size: 13px; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #c9a14a; padding-bottom: 12px; margin-bottom: 20px; }
        .brand { font-size: 22px; font-weight: bold; }
        .muted { color: #64748b; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { text-align: left; background: #f8fafc; padding: 8px; font-size: 11px; text-transform: uppercase; color: #64748b; }
        td { padding: 8px; border-bottom: 1px solid #f1f5f9; }
        .totals td { border: none; }
        .totals .label { text-align: right; color: #64748b; }
        .totals .grand { font-weight: bold; font-size: 16px; border-top: 2px solid #1e2a37; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">LuxStay Hotels</div>
            <div class="muted">123 Ocean Drive, Negombo, Sri Lanka<br>reservations@luxstay.test</div>
        </div>
        <div style="text-align:right">
            <div style="font-size:18px; font-weight:bold;">INVOICE</div>
            <div class="muted">{{ $booking->invoice->invoice_number }}</div>
            <div class="muted">Issued {{ $booking->invoice->created_at->format('M j, Y') }}</div>
        </div>
    </div>

    <table style="margin-top:0">
        <tr>
            <td style="border:none; width:50%">
                <strong>Billed to</strong><br>
                {{ $booking->guest->name }}<br>
                {{ $booking->guest->email }}<br>
                {{ $booking->guest->phone }}
            </td>
            <td style="border:none; width:50%; text-align:right">
                <strong>Booking Reference</strong> {{ $booking->booking_reference }}<br>
                <strong>Status</strong> {{ ucfirst($booking->status) }}<br>
                <strong>Payment</strong> {{ $booking->payment ? ucfirst($booking->payment->status) : 'N/A' }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>Description</th><th>Check-in</th><th>Check-out</th><th>Nights</th><th style="text-align:right">Rate</th><th style="text-align:right">Amount</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $booking->room->name }} (Room {{ $booking->room->room_number }})</td>
                <td>{{ $booking->check_in->format('M j, Y') }}</td>
                <td>{{ $booking->check_out->format('M j, Y') }}</td>
                <td>{{ $booking->nights }}</td>
                <td style="text-align:right">${{ number_format($booking->nightly_rate, 2) }}</td>
                <td style="text-align:right">${{ number_format($booking->subtotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr><td colspan="5" class="label">Subtotal</td><td style="text-align:right; width:100px">${{ number_format($booking->subtotal, 2) }}</td></tr>
        <tr><td colspan="5" class="label">Tax</td><td style="text-align:right">${{ number_format($booking->tax_amount, 2) }}</td></tr>
        <tr class="grand"><td colspan="5" class="label">Total</td><td style="text-align:right">${{ number_format($booking->total_amount, 2) }}</td></tr>
    </table>

    <p class="muted" style="margin-top:40px">Thank you for staying with LuxStay Hotels. This is a computer-generated invoice.</p>
</body>
</html>
