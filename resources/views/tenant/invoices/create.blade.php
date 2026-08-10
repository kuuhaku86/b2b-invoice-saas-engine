@extends('layouts.tenant')

@section('title', 'New Invoice')

@section('content')
    <h1>New invoice</h1>

    <form method="POST" action="{{ route('tenant.invoices.store') }}">
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
        <label>Issue date <input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" required></label>
        <label>Due date <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(14)->toDateString()) }}" required></label>

        <h3>Line items</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit price</th>
                    <th>Tax %</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 5; $i++)
                    <tr>
                        <td><input type="text" name="items[{{ $i }}][description]"></td>
                        <td><input type="number" step="0.01" min="0" name="items[{{ $i }}][quantity]" value="1"></td>
                        <td><input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" value="0"></td>
                        <td><input type="number" step="0.01" min="0" max="100" name="items[{{ $i }}][tax_rate]" value="0"></td>
                    </tr>
                @endfor
            </tbody>
        </table>
        <p>Blank rows (no description) are ignored.</p>

        <label>Discount (flat amount) <input type="number" step="0.01" min="0" name="discount_total" value="{{ old('discount_total', 0) }}"></label>

        <button type="submit">Create invoice</button>
    </form>
@endsection
