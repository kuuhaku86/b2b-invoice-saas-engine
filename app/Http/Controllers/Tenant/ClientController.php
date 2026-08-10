<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        return view('tenant.clients.index', [
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('tenant.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        Client::create($validated);

        return redirect()->route('tenant.clients.index')->with('status', 'Client created.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        try {
            $client->delete();
        } catch (QueryException) {
            return redirect()->route('tenant.clients.index')
                ->with('status', "Can't delete \"{$client->name}\" — it has invoices on file.");
        }

        return redirect()->route('tenant.clients.index')->with('status', 'Client deleted.');
    }
}
