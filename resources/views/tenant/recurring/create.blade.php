@extends('layouts.tenant')

@section('title', 'New Recurring Invoice')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">New recurring invoice schedule</h1>

    <div class="max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('tenant.recurring.store') }}" class="space-y-4">
            @csrf
            <label class="block text-sm font-medium text-gray-700">
                Client
                <select name="client_id" required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Description
                <input type="text" name="description" value="{{ old('description') }}" required
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <div class="grid grid-cols-2 gap-4">
                <label class="block text-sm font-medium text-gray-700">
                    Quantity
                    <input type="number" step="0.01" min="0.01" name="quantity" value="{{ old('quantity', 1) }}" required
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </label>
                <label class="block text-sm font-medium text-gray-700">
                    Unit price
                    <input type="number" step="0.01" min="0" name="unit_price" value="{{ old('unit_price', 0) }}" required
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </label>
                <label class="block text-sm font-medium text-gray-700">
                    Tax %
                    <input type="number" step="0.01" min="0" max="100" name="tax_rate" value="{{ old('tax_rate', 0) }}"
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </label>
                <label class="block text-sm font-medium text-gray-700">
                    Discount (flat)
                    <input type="number" step="0.01" min="0" name="discount_total" value="{{ old('discount_total', 0) }}"
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </label>
            </div>
            <label class="block text-sm font-medium text-gray-700">
                Interval
                <select name="interval" required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="monthly">Monthly</option>
                    <option value="weekly">Weekly</option>
                </select>
            </label>
            <label class="block text-sm font-medium text-gray-700">
                First run date
                <input type="date" name="next_run_date" value="{{ old('next_run_date', now()->toDateString()) }}" required
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Create schedule</button>
        </form>
    </div>
@endsection
