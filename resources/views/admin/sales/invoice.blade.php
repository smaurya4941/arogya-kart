<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $sale->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; margin: 0; padding: 24px; background: #f1f5f9; }
        .sheet { max-width: 760px; margin: 0 auto; background: #fff; padding: 32px; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #059669; padding-bottom: 16px; }
        .brand { font-size: 22px; font-weight: 700; color: #059669; }
        .muted { color: #64748b; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
        th, td { padding: 8px; text-align: left; }
        thead th { background: #f1f5f9; border-bottom: 1px solid #cbd5e1; }
        tbody td { border-bottom: 1px solid #e2e8f0; }
        .r { text-align: right; }
        .totals { margin-top: 16px; margin-left: auto; width: 300px; font-size: 13px; }
        .totals div { display: flex; justify-content: space-between; padding: 4px 0; }
        .totals .grand { font-size: 16px; font-weight: 700; border-top: 2px solid #cbd5e1; padding-top: 8px; margin-top: 4px; }
        .foot { margin-top: 32px; text-align: center; color: #64748b; font-size: 12px; }
        .actions { max-width: 760px; margin: 0 auto 16px; text-align: right; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; cursor: pointer; border: 1px solid #cbd5e1; background: #fff; color: #1e293b; }
        .btn-primary { background: #059669; color: #fff; border-color: #059669; }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; border-radius: 0; max-width: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ route('admin.sales.show', $sale) }}" class="btn">← Back</a>
        <a href="#" onclick="window.print(); return false;" class="btn btn-primary">Print</a>
    </div>

    <div class="sheet">
        <div class="head">
            <div>
                <div class="brand">{{ $pharmacy?->name ?? config('app.name', 'ArogyaKart') }}</div>
                <div class="muted">
                    @if($pharmacy?->phone) Ph: {{ $pharmacy->phone }}<br>@endif
                    @if($pharmacy?->email) {{ $pharmacy->email }}<br>@endif
                    @if($pharmacy?->license) DL: {{ $pharmacy->license }}@endif
                    @if($pharmacy?->gst) · GSTIN: {{ $pharmacy->gst }}@endif
                </div>
            </div>
            <div style="text-align:right">
                <h1>TAX INVOICE</h1>
                <div class="muted">
                    <strong>{{ $sale->invoice_number }}</strong><br>
                    {{ $sale->sale_date->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; margin-top:16px; font-size:13px;">
            <div>
                <div class="muted">Billed To</div>
                <strong>{{ $sale->customer?->name ?? 'Walk-in Customer' }}</strong>
                @if($sale->customer?->phone)<br><span class="muted">{{ $sale->customer->phone }}</span>@endif
            </div>
            <div style="text-align:right">
                <div class="muted">Payment</div>
                <strong style="text-transform:capitalize">{{ $sale->payment_method }}</strong> ·
                <span style="text-transform:capitalize">{{ $sale->payment_status }}</span><br>
                <span class="muted">Cashier: {{ $sale->cashier?->name ?? '—' }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine</th>
                    <th>Batch</th>
                    <th>Expiry</th>
                    <th class="r">MRP</th>
                    <th class="r">Qty</th>
                    <th class="r">Disc%</th>
                    <th class="r">GST%</th>
                    <th class="r">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product?->name ?? '—' }}</td>
                        <td>{{ $item->batch?->batch_number ?? '—' }}</td>
                        <td>{{ optional($item->batch?->expiry_date)->format('m/Y') ?? '—' }}</td>
                        <td class="r">{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="r">{{ $item->quantity }}</td>
                        <td class="r">{{ rtrim(rtrim(number_format((float) $item->discount_percentage, 2), '0'), '.') }}</td>
                        <td class="r">{{ rtrim(rtrim(number_format((float) $item->tax_percentage, 2), '0'), '.') }}</td>
                        <td class="r">{{ number_format((float) $item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div><span>Subtotal</span><span>₹{{ number_format((float) $sale->subtotal, 2) }}</span></div>
            <div><span>GST</span><span>₹{{ number_format((float) $sale->tax_amount, 2) }}</span></div>
            <div><span>Discount</span><span>− ₹{{ number_format((float) $sale->discount_amount, 2) }}</span></div>
            <div class="grand"><span>Grand Total</span><span>₹{{ number_format((float) $sale->total_amount, 2) }}</span></div>
            <div><span>Paid ({{ ucfirst($sale->payment_method) }})</span><span>₹{{ number_format((float) $sale->paid_amount, 2) }}</span></div>
            @if($sale->due_amount > 0)
                <div style="color:#e11d48"><span>Balance Due</span><span>₹{{ number_format((float) $sale->due_amount, 2) }}</span></div>
            @endif
        </div>

        <div class="foot">
            @if($sale->notes)<p>{{ $sale->notes }}</p>@endif
            <p>This is a computer-generated invoice. Get well soon! · Thank you for shopping with us.</p>
        </div>
    </div>

    @if(request()->boolean('autoprint'))
        <script>window.addEventListener('load', () => window.print());</script>
    @endif
</body>
</html>
