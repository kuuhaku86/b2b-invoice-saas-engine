@extends('layouts.central')

@section('title', 'New Tenant')

@section('content')
    <h1>New tenant</h1>

    <form method="POST" action="{{ route('central.tenants.store') }}">
        @csrf
        <label>Company name <input type="text" name="name" value="{{ old('name') }}" required></label>
        <label>
            Subdomain
            <input type="text" name="subdomain" value="{{ old('subdomain') }}" pattern="[a-z0-9\-]+" required>
            .{{ env('CENTRAL_DOMAIN', 'saas.test') }}
        </label>
        <label>
            Plan
            <select name="plan_id">
                <option value="">— None —</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>{{ $plan->name }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit">Create</button>
    </form>
@endsection
