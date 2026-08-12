<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tenant App')</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
    <nav class="flex items-center gap-6 border-b border-gray-200 bg-white px-8 py-4 text-sm">
        <span class="font-semibold text-gray-400">{{ tenant('id') }}</span>
        @auth
            <a href="{{ route('tenant.dashboard') }}" class="{{ request()->routeIs('tenant.dashboard') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">Dashboard</a>
            <a href="{{ route('tenant.clients.index') }}" class="{{ request()->routeIs('tenant.clients.*') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">Clients</a>
            <a href="{{ route('tenant.invoices.index') }}" class="{{ request()->routeIs('tenant.invoices.*') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">Invoices</a>
            <a href="{{ route('tenant.recurring.index') }}" class="{{ request()->routeIs('tenant.recurring.*') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">Recurring</a>
            <a href="{{ route('tenant.billing.index') }}" class="{{ request()->routeIs('tenant.billing.*') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">Billing</a>
            <form method="POST" action="{{ route('tenant.logout') }}" class="ml-auto">
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
