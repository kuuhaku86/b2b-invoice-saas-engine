@extends('layouts.tenant')

@section('title', 'Log in')

@section('content')
    <div class="mx-auto max-w-sm rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
        <h1 class="mb-6 text-2xl font-bold">Log in</h1>

        <form method="POST" action="{{ route('tenant.login.attempt') }}" class="space-y-4">
            @csrf
            <label class="block text-sm font-medium text-gray-700">
                Email
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <label class="block text-sm font-medium text-gray-700">
                Password
                <input type="password" name="password" required
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            </label>
            <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Log in</button>
        </form>

        <p class="mt-4 text-sm text-gray-600">Need an account? <a href="{{ route('tenant.register') }}" class="text-blue-600 hover:underline">Register</a></p>
    </div>
@endsection
