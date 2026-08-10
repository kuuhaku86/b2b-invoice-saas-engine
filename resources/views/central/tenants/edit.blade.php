@extends('layouts.central')

@section('title', 'Edit Tenant')

@section('content')
    <h1>Edit tenant: {{ $tenant->name }}</h1>
    <p>Subdomain: {{ $tenant->domains->pluck('domain')->join(', ') }} (not editable here)</p>

    <form method="POST" action="{{ route('central.tenants.update', $tenant) }}">
        @csrf
        @method('PUT')
        <label>Company name <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required></label>
        <label>
            Plan
            <select name="plan_id">
                <option value="">— None —</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}" @selected(old('plan_id', $tenant->plan_id) == $plan->id)>{{ $plan->name }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit">Save</button>
    </form>
@endsection
