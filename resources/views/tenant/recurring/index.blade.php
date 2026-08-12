@extends('layouts.tenant')

@section('title', 'Recurring Invoices')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Recurring invoices</h1>
        <a href="{{ route('tenant.recurring.create') }}" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">+ New schedule</a>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">Interval</th>
                    <th class="px-4 py-3">Next run</th>
                    <th class="px-4 py-3">Active</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($templates as $template)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $template->client->name }}</td>
                        <td class="px-4 py-3">{{ $template->items[0]['description'] ?? '' }}</td>
                        <td class="px-4 py-3">{{ ucfirst($template->interval) }}</td>
                        <td class="px-4 py-3">{{ $template->next_run_date->toFormattedDateString() }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $template->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $template->active ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('tenant.recurring.destroy', $template) }}" onsubmit="return confirm('Delete this schedule?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
