@extends('layouts.tenant')

@section('title', 'Billing')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Billing</h1>

    @if (request('checkout') === 'success')
        <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">Checkout complete — your subscription will activate once Stripe confirms payment.</div>
    @elseif (request('checkout') === 'cancelled')
        <div class="mb-6 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Checkout cancelled.</div>
    @endif

    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-600">Current plan: <span class="font-medium text-gray-900">{{ $tenant->plan->name ?? 'None' }}</span></p>
        @if ($subscription)
            <p class="mt-1 text-sm text-gray-600">Subscription status: <span class="font-medium text-gray-900">{{ ucfirst($subscription->status) }}</span></p>
            @if ($subscription->current_period_ends_at)
                <p class="mt-1 text-sm text-gray-600">Renews: {{ $subscription->current_period_ends_at->toFormattedDateString() }}</p>
            @endif
        @endif
    </div>

    <h2 class="mb-3 text-lg font-semibold">Plans</h2>
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Price / mo</th>
                    <th class="px-4 py-3">Invoice quota</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($plans as $plan)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $plan->name }}</td>
                        <td class="px-4 py-3">${{ number_format($plan->price, 2) }}</td>
                        <td class="px-4 py-3">{{ $plan->invoice_quota ?? 'Unlimited' }}</td>
                        <td class="px-4 py-3">
                            @if ($plan->stripe_price_id)
                                <form method="POST" action="{{ route('tenant.billing.checkout', $plan) }}">
                                    @csrf
                                    <button type="submit" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">Subscribe</button>
                                </form>
                            @else
                                <em class="text-gray-400">Not available for checkout</em>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
