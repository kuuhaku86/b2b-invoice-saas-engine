@extends('layouts.tenant')

@section('title', 'Clients')

@section('content')
    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    <nav>
        <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
        <a href="{{ route('tenant.clients.index') }}">Clients</a>
        <a href="{{ route('tenant.invoices.index') }}">Invoices</a>
    </nav>

    <h1>Clients</h1>
    <a href="{{ route('tenant.clients.create') }}">+ New client</a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->phone }}</td>
                    <td>
                        <form class="inline" method="POST" action="{{ route('tenant.clients.destroy', $client) }}" onsubmit="return confirm('Delete this client?');">
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
