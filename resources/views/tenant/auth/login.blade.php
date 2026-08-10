@extends('layouts.tenant')

@section('title', 'Log in')

@section('content')
    <h1>Log in</h1>

    <form method="POST" action="{{ route('tenant.login.attempt') }}">
        @csrf
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password <input type="password" name="password" required></label>
        <button type="submit">Log in</button>
    </form>

    <p><a href="{{ route('tenant.register') }}">Need an account? Register</a></p>
@endsection
