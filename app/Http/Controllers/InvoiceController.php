<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with(['client', 'company', 'project'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = Client::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $projects = Project::where('status', '!=', 'archive')
            ->orderBy('name')
            ->get();

        // Create a combined list of recipients (clients and companies)
        $recipients = [];
        foreach ($clients as $client) {
            $recipients[] = [
                'id' => 'client_'.$client->id,
                'name' => $client->name,
                'type' => 'client',
                'type_icon' => '👤',
                'details' => $client->company ? '('.$client->company->name.')' : '',
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

        $selectedClientId = $request->query('client_id');
        $selectedProjectId = $request->query('project_id');
        $selectedCompanyId = $request->query('company_id');

        // Determine the selected recipient
        $selectedRecipientId = null;
        if ($selectedClientId) {
            $selectedRecipientId = 'client_'.$selectedClientId;
        } elseif ($selectedCompanyId) {
            $selectedRecipientId = 'company_'.$selectedCompanyId;
        }

        return view('invoices.create', compact('clients', 'companies', 'projects', 'recipients', 'selectedRecipientId', 'selectedProjectId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'invoice_number' => 'required|string|max:255|unique:invoices',
            'status' => 'required|in:brouillon,envoyee,payee',
            'total_ht' => 'required|numeric|min:0',
            'tva_rate' => 'required|numeric|min:0|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
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

        // Set client_id or company_id based on recipient type
        $client_id = null;
        $company_id = null;

        if ($recipientType === 'client') {
            // Verify client exists
            $client = Client::find($recipientId);
            if (! $client) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Client non trouvé.'])
                    ->withInput();
            }
            $client_id = $recipientId;
        } elseif ($recipientType === 'company') {
            // Verify company exists
            $company = Company::find($recipientId);
            if (! $company) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Entreprise non trouvée.'])
                    ->withInput();
            }
            $company_id = $recipientId;
        } else {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'Type de destinataire invalide.'])
                ->withInput();
        }

        // Calculate total TTC
        $totalTTC = $validated['total_ht'] * (1 + ($validated['tva_rate'] / 100));

        // Create the invoice
        $invoice = new Invoice;
        $invoice->client_id = $client_id;
        $invoice->company_id = $company_id;
        $invoice->project_id = $validated['project_id'];
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->status = $validated['status'];
        $invoice->total_ht = $validated['total_ht'];
        $invoice->tva_rate = $validated['tva_rate'];
        $invoice->total_ttc = $totalTTC;
        $invoice->issue_date = $validated['issue_date'];
        $invoice->due_date = $validated['due_date'];
        $invoice->payment_date = $validated['payment_date'] ?? null;
        $invoice->notes = $validated['notes'] ?? null;
        $invoice->save();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Facture créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with(['client', 'company', 'project', 'invoiceLines'])
            ->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $clients = Client::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

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
        if ($invoice->client_id) {
            $selectedRecipientId = 'client_'.$invoice->client_id;
        } elseif ($invoice->company_id) {
            $selectedRecipientId = 'company_'.$invoice->company_id;
        }

        return view('invoices.edit', compact('invoice', 'clients', 'companies', 'projects', 'recipients', 'selectedRecipientId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'recipient_id' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,'.$id,
            'status' => 'required|in:brouillon,envoyee,payee',
            'total_ht' => 'required|numeric|min:0',
            'tva_rate' => 'required|numeric|min:0|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
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

        // Set client_id or company_id based on recipient type
        $client_id = null;
        $company_id = null;

        if ($recipientType === 'client') {
            // Verify client exists
            $client = Client::find($recipientId);
            if (! $client) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Client non trouvé.'])
                    ->withInput();
            }
            $client_id = $recipientId;
        } elseif ($recipientType === 'company') {
            // Verify company exists
            $company = Company::find($recipientId);
            if (! $company) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Entreprise non trouvée.'])
                    ->withInput();
            }
            $company_id = $recipientId;
        } else {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'Type de destinataire invalide.'])
                ->withInput();
        }

        // Calculate total TTC
        $totalTTC = $validated['total_ht'] * (1 + ($validated['tva_rate'] / 100));

        // Update the invoice
        $invoice->client_id = $client_id;
        $invoice->company_id = $company_id;
        $invoice->project_id = $validated['project_id'];
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->status = $validated['status'];
        $invoice->total_ht = $validated['total_ht'];
        $invoice->tva_rate = $validated['tva_rate'];
        $invoice->total_ttc = $totalTTC;
        $invoice->issue_date = $validated['issue_date'];
        $invoice->due_date = $validated['due_date'];
        $invoice->payment_date = $validated['payment_date'] ?? null;
        $invoice->notes = $validated['notes'] ?? null;
        $invoice->save();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Facture mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Facture supprimée avec succès.');
    }

    /**
     * Generate a PDF for the invoice
     */
    public function generatePdf(string $id)
    {
        $invoice = Invoice::with(['client', 'company', 'project', 'invoiceLines'])
            ->findOrFail($id);

        $pdf = PDF::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('facture_'.$invoice->invoice_number.'.pdf');
    }
}
