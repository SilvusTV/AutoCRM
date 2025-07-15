<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::where('user_id', auth()->id())->orderBy('name')->paginate(10);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();

        return view('clients.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $validated['user_id'] = auth()->id();

        $client = Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::where('user_id', auth()->id())->findOrFail($id);
        $projects = $client->projects()->orderBy('created_at', 'desc')->get();

        // Calculate total time spent across all projects
        $totalMinutes = 0;

        foreach ($projects as $project) {
            $totalMinutes += $project->timeEntries->sum('duration_minutes');
        }

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return view('clients.show', compact('client', 'projects', 'hours', 'minutes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Client::where('user_id', auth()->id())->findOrFail($id);
        $companies = Company::where('user_id', auth()->id())->orderBy('name')->get();

        return view('clients.edit', compact('client', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $client = Client::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        // Ensure company belongs to the authenticated user
        if (! empty($validated['company_id'])) {
            $company = Company::where('id', $validated['company_id'])
                ->where('user_id', auth()->id())
                ->firstOrFail();
        }

        $client->update($validated);

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::where('user_id', auth()->id())->findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
