@extends('layouts.central')

@section('title', 'Edit Plan')

@section('content')
    <h1>Edit plan: {{ $plan->name }}</h1>

    <form method="POST" action="{{ route('central.plans.update', $plan) }}">
        @csrf
        @method('PUT')
        <label>Name <input type="text" name="name" value="{{ old('name', $plan->name) }}" required></label>
        <label>Price / month <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $plan->price) }}" required></label>
        <label>Invoice quota (blank = unlimited) <input type="number" min="1" name="invoice_quota" value="{{ old('invoice_quota', $plan->invoice_quota) }}"></label>
        <button type="submit">Save</button>
    </form>
@endsection
