@extends('layouts.tenant')

@section('title', 'Invoices')

@section('content')
    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    <nav>
        <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
        <a href="{{ route('tenant.clients.index') }}">Clients</a>
        <a href="{{ route('tenant.invoices.index') }}">Invoices</a>
        <a href="{{ route('tenant.recurring.index') }}">Recurring</a>
        <a href="{{ route('tenant.billing.index') }}">Billing</a>
    </nav>

    <h1>Invoices</h1>
    <a href="{{ route('tenant.invoices.create') }}">+ New invoice</a>

    <table>
        <thead>
            <tr>
                <th>Number</th>
                <th>Client</th>
                <th>Status</th>
                <th>Total</th>
                <th>Due</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->client->name }}</td>
                    <td>{{ ucfirst($invoice->status) }}</td>
                    <td>{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                    <td>{{ $invoice->due_date->toFormattedDateString() }}</td>
                    <td><a href="{{ route('tenant.invoices.show', $invoice) }}">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
