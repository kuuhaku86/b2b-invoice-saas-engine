@extends('layouts.tenant')

@section('title', 'Recurring Invoices')

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

    <h1>Recurring invoices</h1>
    <a href="{{ route('tenant.recurring.create') }}">+ New schedule</a>

    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Description</th>
                <th>Interval</th>
                <th>Next run</th>
                <th>Active</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($templates as $template)
                <tr>
                    <td>{{ $template->client->name }}</td>
                    <td>{{ $template->items[0]['description'] ?? '' }}</td>
                    <td>{{ ucfirst($template->interval) }}</td>
                    <td>{{ $template->next_run_date->toFormattedDateString() }}</td>
                    <td>{{ $template->active ? 'Yes' : 'No' }}</td>
                    <td>
                        <form class="inline" method="POST" action="{{ route('tenant.recurring.destroy', $template) }}" onsubmit="return confirm('Delete this schedule?');">
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
