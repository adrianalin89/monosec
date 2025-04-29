<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::paginate(15);
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();

        // Generate a unique API key
        $validated['api_key'] = $this->generateUniqueApiKey();

        // Set default value for has_credentials if not provided
        if (!isset($validated['has_credentials'])) {
            $validated['has_credentials'] = false;
        }

        $client = Client::create($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client înregistrat cu succes! Cheia API a fost generată.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients,email,' . $client->id,
            'company_name' => 'required|string|max:255',
            'has_credentials' => 'boolean',
        ]);

        // Set default value for has_credentials if not provided
        if (!isset($validated['has_credentials'])) {
            $validated['has_credentials'] = false;
        }

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Informațiile clientului au fost actualizate cu succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Clientul a fost șters cu succes.');
    }

    /**
     * Regenerate API key for the client.
     */
    public function regenerateApiKey(Client $client)
    {
        $client->api_key = $this->generateUniqueApiKey();
        $client->save();

        return redirect()->route('clients.show', $client)
            ->with('success', 'Cheia API a fost regenerată cu succes.');
    }

    /**
     * Generate a unique API key.
     */
    private function generateUniqueApiKey(): string
    {
        $apiKey = Str::random(64);

        // Ensure the API key is unique
        while (Client::where('api_key', $apiKey)->exists()) {
            $apiKey = Str::random(64);
        }

        return $apiKey;
    }
}
