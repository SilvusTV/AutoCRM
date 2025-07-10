<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('client')->orderBy('created_at', 'desc')->paginate(10);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = Client::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $selectedClientId = $request->query('client_id');
        $selectedCompanyId = $request->query('company_id');

        // Create a combined list of recipients (clients and companies)
        $recipients = [];
        foreach ($clients as $client) {
            $recipients[] = [
                'id' => 'client_'.$client->id,
                'name' => $client->name,
                'type' => 'client',
                'type_icon' => '🧑‍🦱',
                'details' => $client->company_name ? '('.$client->company_name.')' : '',
                'model' => $client,
            ];
        }

        foreach ($companies as $company) {
            $recipients[] = [
                'id' => 'company_'.$company->id,
                'name' => $company->name,
                'type' => 'company',
                'type_icon' => '🏢',
                'details' => '',
                'model' => $company,
            ];
        }

        // Sort recipients by name
        usort($recipients, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        // Determine the selected recipient
        $selectedRecipientId = null;
        if ($selectedClientId) {
            $selectedRecipientId = 'client_'.$selectedClientId;
        } elseif ($selectedCompanyId) {
            $selectedRecipientId = 'company_'.$selectedCompanyId;
        }

        return view('projects.create', compact('clients', 'companies', 'recipients', 'selectedRecipientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:en_cours,termine,archive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Parse the recipient_id to determine type and ID
        $recipientParts = explode('_', $validated['recipient_id']);
        if (count($recipientParts) !== 2) {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'Format de destinataire invalide.'])
                ->withInput();
        }

        $recipientType = $recipientParts[0];
        $recipientId = $recipientParts[1];

        // Set client_id based on recipient type
        if ($recipientType === 'client') {
            // Verify client exists
            $client = Client::find($recipientId);
            if (! $client) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Client non trouvé.'])
                    ->withInput();
            }
            $validated['client_id'] = $recipientId;
        } elseif ($recipientType === 'company') {
            // Verify company exists
            $company = Company::find($recipientId);
            if (! $company) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Entreprise non trouvée.'])
                    ->withInput();
            }

            // For companies, we need to create a new client record or use an existing one
            $client = Client::firstOrCreate(
                ['company_id' => $company->id, 'name' => $company->name],
                [
                    'email' => $company->email ?? 'contact@'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $company->name)).'.com',
                    'company_name' => $company->name,
                    'phone' => $company->phone,
                    'address' => $company->address,
                    'siret' => $company->siret,
                ]
            );

            $validated['client_id'] = $client->id;
        } else {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'Type de destinataire invalide.'])
                ->withInput();
        }

        // Remove recipient_id from validated data
        unset($validated['recipient_id']);

        $project = Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projet créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::with(['client', 'timeEntries.user'])->findOrFail($id);
        $totalMinutes = $project->timeEntries->sum('duration_minutes');

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return view('projects.show', compact('project', 'hours', 'minutes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::findOrFail($id);
        $clients = Client::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        // Create a combined list of recipients (clients and companies)
        $recipients = [];
        foreach ($clients as $client) {
            $recipients[] = [
                'id' => 'client_'.$client->id,
                'name' => $client->name,
                'type' => 'client',
                'type_icon' => '🧑‍🦱',
                'details' => $client->company_name ? '('.$client->company_name.')' : '',
                'model' => $client,
            ];
        }

        foreach ($companies as $company) {
            $recipients[] = [
                'id' => 'company_'.$company->id,
                'name' => $company->name,
                'type' => 'company',
                'type_icon' => '🏢',
                'details' => '',
                'model' => $company,
            ];
        }

        // Sort recipients by name
        usort($recipients, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        // Determine the selected recipient
        $selectedRecipientId = null;

        // If the client has a company_id, select the company
        if ($project->client && $project->client->company_id) {
            $selectedRecipientId = 'company_'.$project->client->company_id;
        } else {
            // Otherwise select the client
            $selectedRecipientId = 'client_'.$project->client_id;
        }

        return view('projects.edit', compact('project', 'clients', 'companies', 'recipients', 'selectedRecipientId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'recipient_id' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:en_cours,termine,archive',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Parse the recipient_id to determine type and ID
        $recipientParts = explode('_', $validated['recipient_id']);
        if (count($recipientParts) !== 2) {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'Format de destinataire invalide.'])
                ->withInput();
        }

        $recipientType = $recipientParts[0];
        $recipientId = $recipientParts[1];

        // Set client_id based on recipient type
        if ($recipientType === 'client') {
            // Verify client exists
            $client = Client::find($recipientId);
            if (! $client) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Client non trouvé.'])
                    ->withInput();
            }
            $validated['client_id'] = $recipientId;
        } elseif ($recipientType === 'company') {
            // Verify company exists
            $company = Company::find($recipientId);
            if (! $company) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Entreprise non trouvée.'])
                    ->withInput();
            }

            // For companies, we need to create a new client record or use an existing one
            $client = Client::firstOrCreate(
                ['company_id' => $company->id, 'name' => $company->name],
                [
                    'email' => $company->email ?? 'contact@'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $company->name)).'.com',
                    'company_name' => $company->name,
                    'phone' => $company->phone,
                    'address' => $company->address,
                    'siret' => $company->siret,
                ]
            );

            $validated['client_id'] = $client->id;
        } else {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'Type de destinataire invalide.'])
                ->withInput();
        }

        // Remove recipient_id from validated data
        unset($validated['recipient_id']);

        $project->update($validated);

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé avec succès.');
    }
}
