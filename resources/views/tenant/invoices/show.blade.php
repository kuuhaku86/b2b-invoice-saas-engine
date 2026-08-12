@extends('layouts.tenant')

@section('title', "Invoice {$invoice->invoice_number}")

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
    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold">Invoice {{ $invoice->invoice_number }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $invoice->client->name }} ({{ $invoice->client->email }})</p>
            </div>
            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClasses[$invoice->status] ?? 'bg-gray-100 text-gray-700' }}">
                {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
            </span>
        </div>
        <p class="mt-4 text-sm text-gray-500">
            PDF:
            @if ($invoice->pdf_path)
                ready ({{ $invoice->pdf_path }})
            @else
                generating…
            @endif
        </p>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3">Unit price</th>
                    <th class="px-4 py-3">Tax %</th>
                    <th class="px-4 py-3">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($invoice->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->description }}</td>
                        <td class="px-4 py-3">{{ $item->quantity }}</td>
                        <td class="px-4 py-3">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-3">{{ number_format($item->tax_rate, 2) }}</td>
                        <td class="px-4 py-3">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-end">
        <div class="w-full max-w-xs space-y-1 rounded-lg border border-gray-200 bg-white p-4 text-sm shadow-sm">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span>{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Tax</span>
                <span>{{ $invoice->currency }} {{ number_format($invoice->tax_total, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Discount</span>
                <span>-{{ $invoice->currency }} {{ number_format($invoice->discount_total, 2) }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-200 pt-1 font-semibold text-gray-900">
                <span>Total</span>
                <span>{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>
    </div>
@endsection
