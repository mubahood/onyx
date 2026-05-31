<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with('createdBy')->withCount('cases');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('client_number', 'like', "%$search%");
            });
        }

        $clients = $query->latest()->paginate(20);

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'nullable|email|max:150',
            'phone'      => 'required|string|max:20',
            'phone_alt'  => 'nullable|string|max:20',
            'gender'     => 'nullable|in:male,female,other',
            'dob'        => 'nullable|date',
            'id_type'    => 'nullable|in:national_id,passport,driving_permit,refugee_id,other',
            'id_number'  => 'nullable|string|max:50',
            'address'    => 'required|string',
            'district'   => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'company'    => 'nullable|string|max:150',
            'notes'      => 'nullable|string',
        ]);

        $data['client_number'] = Client::generateNumber();
        $data['created_by']    = Auth::id();

        $client = Client::create($data);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', "Client {$client->full_name} ({$client->client_number}) created successfully.");
    }

    public function show(Client $client)
    {
        $client->load(['cases.mainOfficer', 'transactions.account', 'documents']);

        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'nullable|email|max:150',
            'phone'      => 'required|string|max:20',
            'phone_alt'  => 'nullable|string|max:20',
            'gender'     => 'nullable|in:male,female,other',
            'dob'        => 'nullable|date',
            'id_type'    => 'nullable|in:national_id,passport,driving_permit,refugee_id,other',
            'id_number'  => 'nullable|string|max:50',
            'address'    => 'required|string',
            'district'   => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'company'    => 'nullable|string|max:150',
            'notes'      => 'nullable|string',
        ]);

        $client->update($data);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $name = $client->full_name;
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', "Client {$name} deleted.");
    }
}
