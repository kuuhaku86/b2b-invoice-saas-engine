@extends('layouts.tenant')

@section('title', 'Billing')

@section('content')
    <nav>
        <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
        <a href="{{ route('tenant.clients.index') }}">Clients</a>
        <a href="{{ route('tenant.invoices.index') }}">Invoices</a>
        <a href="{{ route('tenant.billing.index') }}">Billing</a>
    </nav>

    <h1>Billing</h1>

    @if (request('checkout') === 'success')
        <div class="flash">Checkout complete — your subscription will activate once Stripe confirms payment.</div>
    @elseif (request('checkout') === 'cancelled')
        <div class="flash">Checkout cancelled.</div>
    @endif

    <p>Current plan: {{ $tenant->plan->name ?? 'None' }}</p>
    @if ($subscription)
        <p>Subscription status: {{ ucfirst($subscription->status) }}</p>
        @if ($subscription->current_period_ends_at)
            <p>Renews: {{ $subscription->current_period_ends_at->toFormattedDateString() }}</p>
        @endif
    @endif

    <h2>Plans</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price / mo</th>
                <th>Invoice quota</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plans as $plan)
                <tr>
                    <td>{{ $plan->name }}</td>
                    <td>${{ number_format($plan->price, 2) }}</td>
                    <td>{{ $plan->invoice_quota ?? 'Unlimited' }}</td>
                    <td>
                        @if ($plan->stripe_price_id)
                            <form method="POST" action="{{ route('tenant.billing.checkout', $plan) }}">
                                @csrf
                                <button type="submit">Subscribe</button>
                            </form>
                        @else
                            <em>Not available for checkout</em>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
