<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .totals td { border: none; text-align: right; }
        .totals tr td:first-child { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Invoice {{ $invoice->invoice_number }}</h1>

    <p>
        <strong>Bill to:</strong> {{ $invoice->client->name }}<br>
        {{ $invoice->client->email }}<br>
        {{ $invoice->client->address }}
    </p>

    <p>
        <strong>Issue date:</strong> {{ $invoice->issue_date->toFormattedDateString() }}<br>
        <strong>Due date:</strong> {{ $invoice->due_date->toFormattedDateString() }}<br>
        <strong>Status:</strong> {{ ucfirst($invoice->status) }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit price</th>
                <th>Tax %</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->tax_rate, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td>{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td></tr>
        <tr><td>Tax</td><td>{{ $invoice->currency }} {{ number_format($invoice->tax_total, 2) }}</td></tr>
        <tr><td>Discount</td><td>-{{ $invoice->currency }} {{ number_format($invoice->discount_total, 2) }}</td></tr>
        <tr><td>Total</td><td>{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td></tr>
    </table>
</body>
</html>
