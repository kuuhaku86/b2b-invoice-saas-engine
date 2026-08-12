@extends('layouts.central')

@section('title', 'Edit Plan')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Edit plan: {{ $plan->name }}</h1>

    <div class="max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('central.plans.update', $plan) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <label class="block text-sm font-medium text-gray-700">
                Name
                <input type="text" name="name" value="{{ old('name', $plan->name) }}" required
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Price / month
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $plan->price) }}" required
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Invoice quota (blank = unlimited)
                <input type="number" min="1" name="invoice_quota" value="{{ old('invoice_quota', $plan->invoice_quota) }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save</button>
        </form>
    </div>
@endsection
