@extends('layouts.central')

@section('title', 'New Plan')

@section('content')
    <h1>New plan</h1>

    <form method="POST" action="{{ route('central.plans.store') }}">
        @csrf
        <label>Name <input type="text" name="name" value="{{ old('name') }}" required></label>
        <label>Price / month <input type="number" step="0.01" min="0" name="price" value="{{ old('price', 0) }}" required></label>
        <label>Invoice quota (blank = unlimited) <input type="number" min="1" name="invoice_quota" value="{{ old('invoice_quota') }}"></label>
        <button type="submit">Create</button>
    </form>
@endsection
