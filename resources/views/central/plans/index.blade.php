@extends('layouts.central')

@section('title', 'Plans')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Plans</h1>
        <a href="{{ route('central.plans.create') }}" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">+ New plan</a>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Price / mo</th>
                    <th class="px-4 py-3">Invoice quota</th>
                    <th class="px-4 py-3">Tenants</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($plans as $plan)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $plan->name }}</td>
                        <td class="px-4 py-3">${{ number_format($plan->price, 2) }}</td>
                        <td class="px-4 py-3">{{ $plan->invoice_quota ?? 'Unlimited' }}</td>
                        <td class="px-4 py-3">{{ $plan->tenants_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('central.plans.edit', $plan) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('central.plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
