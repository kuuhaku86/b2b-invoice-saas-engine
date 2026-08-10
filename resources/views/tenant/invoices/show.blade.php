@extends('layouts.tenant')

@section('title', "Invoice {$invoice->invoice_number}")

@section('content')
    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    <nav>
        <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
        <a href="{{ route('tenant.clients.index') }}">Clients</a>
        <a href="{{ route('tenant.invoices.index') }}">Invoices</a>
    </nav>

    <h1>Invoice {{ $invoice->invoice_number }}</h1>
    <p>Client: {{ $invoice->client->name }} ({{ $invoice->client->email }})</p>
    <p>Status: {{ ucfirst($invoice->status) }}</p>
    <p>
        PDF:
        @if ($invoice->pdf_path)
            ready ({{ $invoice->pdf_path }})
        @else
            generating…
        @endif
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

    <p>
        Subtotal: {{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}<br>
        Tax: {{ $invoice->currency }} {{ number_format($invoice->tax_total, 2) }}<br>
        Discount: -{{ $invoice->currency }} {{ number_format($invoice->discount_total, 2) }}<br>
        <strong>Total: {{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</strong>
    </p>
@endsection
