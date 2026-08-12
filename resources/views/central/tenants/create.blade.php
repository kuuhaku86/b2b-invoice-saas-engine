@extends('layouts.central')

@section('title', 'New Tenant')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">New tenant</h1>

    <div class="max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('central.tenants.store') }}" class="space-y-4">
            @csrf
            <label class="block text-sm font-medium text-gray-700">
                Company name
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Subdomain
                <div class="mt-1 flex items-center gap-2">
                    <input type="text" name="subdomain" value="{{ old('subdomain') }}" pattern="[a-z0-9\-]+" required
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <span class="whitespace-nowrap text-sm text-gray-500">.{{ env('CENTRAL_DOMAIN', 'saas.test') }}</span>
                </div>
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Plan
                <select name="plan_id"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">— None —</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Create</button>
        </form>
    </div>
@endsection
