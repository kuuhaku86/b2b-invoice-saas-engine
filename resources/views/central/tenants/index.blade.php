@extends('layouts.central')

@section('title', 'Tenants')

@section('content')
    <h1>Tenants</h1>
    <a href="{{ route('central.tenants.create') }}">+ New tenant</a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Domain</th>
                <th>Plan</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tenants as $tenant)
                <tr>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->domains->pluck('domain')->join(', ') }}</td>
                    <td>{{ $tenant->plan->name ?? '—' }}</td>
                    <td>{{ $tenant->created_at->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('central.tenants.edit', $tenant) }}">Edit</a>
                        <form class="inline" method="POST" action="{{ route('central.tenants.destroy', $tenant) }}" onsubmit="return confirm('Delete this tenant and its database? This cannot be undone.');">
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
