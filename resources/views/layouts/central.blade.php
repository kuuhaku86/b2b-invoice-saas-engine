<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Central Admin')</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
    <nav class="flex items-center gap-6 border-b border-gray-200 bg-white px-8 py-4 text-sm">
        <span class="font-semibold text-gray-400">Central Admin</span>
        @auth
            <a href="{{ route('central.dashboard') }}" class="{{ request()->routeIs('central.dashboard') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">Dashboard</a>
            <a href="{{ route('central.tenants.index') }}" class="{{ request()->routeIs('central.tenants.*') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">Tenants</a>
            <a href="{{ route('central.plans.index') }}" class="{{ request()->routeIs('central.plans.*') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">Plans</a>
            <a href="{{ url('/horizon') }}" class="text-gray-600 hover:text-gray-900">Horizon</a>
            <form method="POST" action="{{ route('central.logout') }}" class="ml-auto">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-gray-900">Log out</button>
            </form>
        @endauth
    </nav>

    <main class="mx-auto max-w-5xl px-8 py-8">
        @if (session('status'))
            <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
