@extends('layouts.tenant')

@section('title', 'New Client')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">New client</h1>

    <div class="max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('tenant.clients.store') }}" class="space-y-4">
            @csrf
            <label class="block text-sm font-medium text-gray-700">
                Name
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Email
                <input type="email" name="email" value="{{ old('email') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Phone
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Address
                <textarea name="address" rows="3"
                          class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('address') }}</textarea>
            </label>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Create</button>
        </form>
    </div>
@endsection
