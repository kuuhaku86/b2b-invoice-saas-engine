@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>
    <p>Tenant: {{ $tenant->id }}</p>
    <p>Logged in as: {{ $user->name }} ({{ $user->email }})</p>

    <form method="POST" action="{{ route('tenant.logout') }}">
        @csrf
        <button type="submit">Log out</button>
    </form>
@endsection
