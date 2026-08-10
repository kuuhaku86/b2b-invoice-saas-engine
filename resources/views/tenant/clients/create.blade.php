@extends('layouts.tenant')

@section('title', 'New Client')

@section('content')
    <h1>New client</h1>

    <form method="POST" action="{{ route('tenant.clients.store') }}">
        @csrf
        <label>Name <input type="text" name="name" value="{{ old('name') }}" required></label>
        <label>Email <input type="email" name="email" value="{{ old('email') }}"></label>
        <label>Phone <input type="text" name="phone" value="{{ old('phone') }}"></label>
        <label>Address <textarea name="address">{{ old('address') }}</textarea></label>
        <button type="submit">Create</button>
    </form>
@endsection
