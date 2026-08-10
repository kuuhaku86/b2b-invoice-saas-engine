<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Central Admin')</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; color: #222; }
        nav a { margin-right: 1rem; }
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { border: 1px solid #ccc; padding: 0.5rem; text-align: left; }
        form.inline { display: inline; }
        .flash { background: #e6ffe6; border: 1px solid #4caf50; padding: 0.5rem; margin-bottom: 1rem; }
        .errors { background: #ffe6e6; border: 1px solid #f44336; padding: 0.5rem; margin-bottom: 1rem; }
        label { display: block; margin-top: 0.75rem; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('central.tenants.index') }}">Tenants</a>
        <a href="{{ route('central.plans.index') }}">Plans</a>
    </nav>
    <hr>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</body>
</html>
