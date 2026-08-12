@extends('layouts.central')

@section('title', 'Edit Tenant')

@section('content')
    <h1 class="mb-1 text-2xl font-bold">Edit tenant: {{ $tenant->name }}</h1>
    <p class="mb-6 text-sm text-gray-500">Subdomain: {{ $tenant->domains->pluck('domain')->join(', ') }} (not editable here)</p>

    <div class="max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('central.tenants.update', $tenant) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <label class="block text-sm font-medium text-gray-700">
                Company name
                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Plan
                <select name="plan_id"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">— None —</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected(old('plan_id', $tenant->plan_id) == $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save</button>
        </form>
    </div>
@endsection
