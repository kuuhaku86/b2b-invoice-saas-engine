@extends('layouts.tenant')

@section('title', 'Invoices')

@php
    $statusClasses = [
        'draft' => 'bg-gray-100 text-gray-700',
        'sent' => 'bg-blue-100 text-blue-700',
        'paid' => 'bg-green-100 text-green-700',
        'past_due' => 'bg-amber-100 text-amber-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
@endphp

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Invoices</h1>
        <a href="{{ route('tenant.invoices.create') }}" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">+ New invoice</a>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Number</th>
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Due</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-3">{{ $invoice->client->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClasses[$invoice->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                        <td class="px-4 py-3">{{ $invoice->due_date->toFormattedDateString() }}</td>
                        <td class="px-4 py-3"><a href="{{ route('tenant.invoices.show', $invoice) }}" class="text-blue-600 hover:underline">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
