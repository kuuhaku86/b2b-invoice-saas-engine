@extends('layouts.tenant')

@section('title', 'New Invoice')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">New invoice</h1>

    <div class="max-w-3xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('tenant.invoices.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
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
                    Issue date
                    <input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" required
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </label>
                <label class="block text-sm font-medium text-gray-700">
                    Due date
                    <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(14)->toDateString()) }}" required
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </label>
            </div>

            <div>
                <h3 class="mb-2 text-sm font-semibold text-gray-700">Line items</h3>
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-3 py-2">Description</th>
                                <th class="px-3 py-2">Qty</th>
                                <th class="px-3 py-2">Unit price</th>
                                <th class="px-3 py-2">Tax %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="text" name="items[{{ $i }}][description]"
                                               class="block w-full rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][quantity]" value="1"
                                               class="block w-20 rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" value="0"
                                               class="block w-24 rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" min="0" max="100" name="items[{{ $i }}][tax_rate]" value="0"
                                               class="block w-20 rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <p class="mt-2 text-xs text-gray-500">Blank rows (no description) are ignored.</p>
            </div>

            <label class="block max-w-xs text-sm font-medium text-gray-700">
                Discount (flat amount)
                <input type="number" step="0.01" min="0" name="discount_total" value="{{ old('discount_total', 0) }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>

            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Create invoice</button>
        </form>
    </div>
@endsection
