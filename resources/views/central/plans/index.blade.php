@extends('layouts.central')

@section('title', 'Plans')

@section('content')
    <h1>Plans</h1>
    <a href="{{ route('central.plans.create') }}">+ New plan</a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price / mo</th>
                <th>Invoice quota</th>
                <th>Tenants</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plans as $plan)
                <tr>
                    <td>{{ $plan->name }}</td>
                    <td>${{ number_format($plan->price, 2) }}</td>
                    <td>{{ $plan->invoice_quota ?? 'Unlimited' }}</td>
                    <td>{{ $plan->tenants_count }}</td>
                    <td>
                        <a href="{{ route('central.plans.edit', $plan) }}">Edit</a>
                        <form class="inline" method="POST" action="{{ route('central.plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
