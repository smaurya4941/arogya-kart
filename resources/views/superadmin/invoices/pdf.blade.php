<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #1f2937; margin: 0; }
        .head { display: flex; justify-content: space-between; border-bottom: 2px solid #059669; padding-bottom: 10px; margin-bottom: 18px; }
        .brand { font-size: 20px; font-weight: bold; color: #065f46; }
        .brand small { display: block; font-size: 10px; color: #6b7280; font-weight: normal; }
        .doc-title { text-align: right; }
        .doc-title h1 { margin: 0; font-size: 22px; color: #111827; letter-spacing: 1px; }
        .doc-title .num { color: #6b7280; font-size: 11px; }
        .parties { width: 100%; margin-bottom: 18px; }
        .parties td { vertical-align: top; padding: 0; width: 50%; }
        .label { text-transform: uppercase; font-size: 9px; color: #9ca3af; letter-spacing: 1px; }
        .val { font-size: 12px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.items th, table.items td { text-align: left; padding: 8px; border-bottom: 1px solid #e5e7eb; }
        table.items th { background: #f3f4f6; text-transform: uppercase; font-size: 9px; color: #374151; }
        .right { text-align: right; }
        .totals { width: 40%; margin-left: 60%; margin-top: 12px; border-collapse: collapse; }
        .totals td { padding: 5px 8px; }
        .totals .grand { border-top: 2px solid #d1d5db; font-size: 15px; font-weight: bold; color: #065f46; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 4px; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-other { background: #f3f4f6; color: #374151; }
        .footer { margin-top: 40px; color: #9ca3af; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="head">
        <div class="brand">
            {{ config('app.name', 'ArogyaKart') }}
            <small>SaaS Subscription Billing</small>
        </div>
        <div class="doc-title">
            <h1>INVOICE</h1>
            <div class="num">{{ $invoice->invoice_number }}</div>
        </div>
    </div>

    <table class="parties">
        <tr>
            <td>
                <div class="label">Billed to</div>
                <div class="val"><strong>{{ $invoice->pharmacy?->name ?? '—' }}</strong></div>
                <div class="val">{{ $invoice->pharmacy?->email }}</div>
                <div class="val">{{ $invoice->pharmacy?->phone }}</div>
                @if($invoice->pharmacy?->gst)<div class="val">GSTIN: {{ $invoice->pharmacy->gst }}</div>@endif
            </td>
            <td class="right">
                <div class="label">Invoice date</div>
                <div class="val">{{ $invoice->created_at->format('d M Y') }}</div>
                <div class="label" style="margin-top:8px;">Status</div>
                <div class="val">
                    <span class="status {{ $invoice->status === \App\Models\Invoice::STATUS_PAID ? 'status-paid' : 'status-other' }}">{{ $invoice->status }}</span>
                </div>
                @if($invoice->paid_at)<div class="val" style="margin-top:6px;">Paid: {{ $invoice->paid_at->format('d M Y') }}</div>@endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $invoice->subscription?->plan?->name ?? 'Subscription' }} plan
                    @if($invoice->subscription)( {{ ucfirst($invoice->subscription->billing_cycle) }} )@endif
                </td>
                <td class="right">₹{{ number_format($invoice->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="right">₹{{ number_format($invoice->amount, 2) }}</td>
        </tr>
        <tr>
            <td>GST</td>
            <td class="right">₹{{ number_format($invoice->tax, 2) }}</td>
        </tr>
        <tr class="grand">
            <td>Total</td>
            <td class="right">₹{{ number_format($invoice->total, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        @if($invoice->transaction_id)Transaction: {{ $invoice->transaction_id }} &middot; @endif
        Payment method: {{ $invoice->payment_method ?? '—' }}<br>
        This is a computer-generated invoice from {{ config('app.name', 'ArogyaKart') }}.
    </div>
</body>
</html>
