@extends('layouts.tenant')

@section('title', 'New Recurring Invoice')

@section('content')
    <h1>New recurring invoice schedule</h1>

    <form method="POST" action="{{ route('tenant.recurring.store') }}">
        @csrf
        <label>
            Client
            <select name="client_id" required>
                <option value="">— Select —</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Description <input type="text" name="description" value="{{ old('description') }}" required></label>
        <label>Quantity <input type="number" step="0.01" min="0.01" name="quantity" value="{{ old('quantity', 1) }}" required></label>
        <label>Unit price <input type="number" step="0.01" min="0" name="unit_price" value="{{ old('unit_price', 0) }}" required></label>
        <label>Tax % <input type="number" step="0.01" min="0" max="100" name="tax_rate" value="{{ old('tax_rate', 0) }}"></label>
        <label>Discount (flat) <input type="number" step="0.01" min="0" name="discount_total" value="{{ old('discount_total', 0) }}"></label>
        <label>
            Interval
            <select name="interval" required>
                <option value="monthly">Monthly</option>
                <option value="weekly">Weekly</option>
            </select>
        </label>
        <label>First run date <input type="date" name="next_run_date" value="{{ old('next_run_date', now()->toDateString()) }}" required></label>
        <button type="submit">Create schedule</button>
    </form>
@endsection
