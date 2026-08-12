@extends('layouts.central')

@section('title', 'Tenants')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Tenants</h1>
        <a href="{{ route('central.tenants.create') }}" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">+ New tenant</a>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Domain</th>
                    <th class="px-4 py-3">Plan</th>
                    <th class="px-4 py-3">Created</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($tenants as $tenant)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $tenant->name }}</td>
                        <td class="px-4 py-3">{{ $tenant->domains->pluck('domain')->join(', ') }}</td>
                        <td class="px-4 py-3">{{ $tenant->plan->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $tenant->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('central.tenants.edit', $tenant) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('central.tenants.destroy', $tenant) }}" onsubmit="return confirm('Delete this tenant and its database? This cannot be undone.');">
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
