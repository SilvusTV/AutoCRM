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
        $projects = Project::with('client')->where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(10);
        foreach ($projects as $project) {
            if ($project->client_type === 'company') {
                $project->display_client = Company::find($project->client_id);
            } else {
                $project->display_client = Client::find($project->client_id);
            }
        }

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = Client::where('user_id', auth()->id())->orderBy('name')->get();
        $companies = Company::where('user_id', auth()->id())->orderBy('name')->get();
        $selectedClientId = $request->query('client_id');
        $selectedCompanyId = $request->query('company_id');

        // Create a combined list of recipients (clients and companies)
        $recipients = [];
        foreach ($clients as $client) {
            $recipients[] = [
                'id' => 'client_'.$client->id,
                'name' => $client->name,
                'type' => 'client',
                'type_icon' => '👤',
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

        // Set client_id and client_type based on recipient type
        if ($recipientType === 'client') {
            // Verify client exists and belongs to the authenticated user
            $client = Client::where('id', $recipientId)->where('user_id', auth()->id())->first();
            if (! $client) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Client non trouvé ou non autorisé.'])
                    ->withInput();
            }
            $validated['client_id'] = $recipientId;
            $validated['client_type'] = 'client';
        } elseif ($recipientType === 'company') {
            // Verify company exists and belongs to the authenticated user
            $company = Company::where('id', $recipientId)->where('user_id', auth()->id())->first();
            if (! $company) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Entreprise non trouvée ou non autorisée.'])
                    ->withInput();
            }

            // For companies, we need to find an existing client associated with this company
            $client = Client::where('company_id', $company->id)->first();

            if (! $client) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Aucun client associé à cette entreprise. Veuillez d\'abord créer un client et l\'associer à cette entreprise.'])
                    ->withInput();
            }

            $validated['client_id'] = $client->id;
            $validated['client_type'] = 'company';
        } else {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'Type de destinataire invalide.'])
                ->withInput();
        }

        // Remove recipient_id from validated data
        unset($validated['recipient_id']);

        // Add user_id to validated data
        $validated['user_id'] = auth()->id();

        $project = Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projet créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::with(['client', 'timeEntries.user'])->where('user_id', auth()->id())->findOrFail($id);

        // Récupérer l'utilisateur rattaché à client_id
        $client = null;
        if ($project->client_type === 'client') {
            $client = Client::find($project->client_id);
        } elseif ($project->client_type === 'company') {
            $client = Company::find($project->client_id);
        }

        $totalMinutes = $project->timeEntries->sum('duration_minutes');
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return view('projects.show', compact('project', 'hours', 'minutes', 'client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::where('user_id', auth()->id())->findOrFail($id);
        $clients = Client::where('user_id', auth()->id())->orderBy('name')->get();
        $companies = Company::where('user_id', auth()->id())->orderBy('name')->get();

        // Create a combined list of recipients (clients and companies)
        $recipients = [];
        foreach ($clients as $client) {
            $recipients[] = [
                'id' => 'client_'.$client->id,
                'name' => $client->name,
                'type' => 'client',
                'type_icon' => '👤',
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

        // Set the selected recipient based on client_type
        if ($project->client_type === 'company') {
            // For company type, find the company associated with the client
            $client = Client::find($project->client_id);
            if ($client && $client->company_id) {
                $selectedRecipientId = 'company_'.$client->company_id;
            }
        } else {
            // For client type, select the client directly
            $selectedRecipientId = 'client_'.$project->client_id;
        }

        return view('projects.edit', compact('project', 'clients', 'companies', 'recipients', 'selectedRecipientId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::where('user_id', auth()->id())->findOrFail($id);

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

        // Set client_id and client_type based on recipient type
        if ($recipientType === 'client') {
            // Verify client exists and belongs to the authenticated user
            $client = Client::where('id', $recipientId)->where('user_id', auth()->id())->first();
            if (! $client) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Client non trouvé ou non autorisé.'])
                    ->withInput();
            }
            $validated['client_id'] = $recipientId;
            $validated['client_type'] = 'client';
        } elseif ($recipientType === 'company') {
            // Verify company exists and belongs to the authenticated user
            $company = Company::where('id', $recipientId)->where('user_id', auth()->id())->first();
            if (! $company) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Entreprise non trouvée ou non autorisée.'])
                    ->withInput();
            }

            // For companies, we need to find an existing client associated with this company
            $client = Client::where('company_id', $company->id)->first();

            if (! $client) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Aucun client associé à cette entreprise. Veuillez d\'abord créer un client et l\'associer à cette entreprise.'])
                    ->withInput();
            }

            $validated['client_id'] = $client->id;
            $validated['client_type'] = 'company';
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
        $project = Project::where('user_id', auth()->id())->findOrFail($id);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé avec succès.');
    }
}
