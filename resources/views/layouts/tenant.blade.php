<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Tenant App')</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; color: #222; }
        nav a { margin-right: 1rem; }
        .flash { background: #e6ffe6; border: 1px solid #4caf50; padding: 0.5rem; margin-bottom: 1rem; }
        .errors { background: #ffe6e6; border: 1px solid #f44336; padding: 0.5rem; margin-bottom: 1rem; }
        label { display: block; margin-top: 0.75rem; }
        form.inline { display: inline; }
    </style>
</head>
<body>
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
