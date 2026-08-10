@extends('layouts.central')

@section('title', 'Admin Login')

@section('content')
    <h1>Admin login</h1>

    <form method="POST" action="{{ route('central.login.attempt') }}">
        @csrf
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password <input type="password" name="password" required></label>
        <button type="submit">Log in</button>
    </form>
@endsection
