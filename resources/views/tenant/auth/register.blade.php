@extends('layouts.tenant')

@section('title', 'Register')

@section('content')
    <h1>Register</h1>

    <form method="POST" action="{{ route('tenant.register') }}">
        @csrf
        <label>Name <input type="text" name="name" value="{{ old('name') }}" required></label>
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password <input type="password" name="password" required></label>
        <label>Confirm password <input type="password" name="password_confirmation" required></label>
        <button type="submit">Register</button>
    </form>

    <p><a href="{{ route('tenant.login') }}">Already have an account? Log in</a></p>
@endsection
