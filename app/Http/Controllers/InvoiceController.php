<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with(['client', 'company', 'project'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = Client::where('user_id', auth()->id())->orderBy('name')->get();
        $companies = Company::where('user_id', auth()->id())->orderBy('name')->get();
        $projects = Project::where('status', '!=', 'archive')
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
        $bankAccounts = auth()->user()->bankAccounts;

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
        $invoiceType = $request->query('type', 'invoice');

        // Determine the selected recipient
        $selectedRecipientId = null;
        if ($selectedClientId) {
            $selectedRecipientId = 'client_'.$selectedClientId;
        } elseif ($selectedCompanyId) {
            $selectedRecipientId = 'company_'.$selectedCompanyId;
        }

        // Generate a new invoice number
        $invoiceNumber = Invoice::generateInvoiceNumber();
        $quoteNumber = Invoice::generateQuoteNumber();

        return view('invoices.create', compact('clients', 'companies', 'projects', 'recipients', 'selectedRecipientId', 'selectedProjectId', 'invoiceType', 'invoiceNumber', 'quoteNumber', 'bankAccounts'));
    }

    /**
     * Show the form for creating a new quote.
     */
    public function createQuote(Request $request)
    {
        $request->merge(['type' => 'quote']);

        return $this->create($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'invoice_number' => 'required|string|max:255|unique:invoices',
            'type' => 'required|in:invoice,quote',
            'status' => 'required|in:draft,sent,paid,cancelled,overdue',
            'tva_rate' => 'required|numeric|min:0|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_date' => 'nullable|date|after_or_equal:issue_date',
            'payment_terms' => 'required|in:immediate,15_days,30_days,45_days,60_days,end_of_month',
            'payment_method' => 'required|in:bank_transfer,check,cash,credit_card,paypal',
            'late_fees' => 'required|in:none,legal_rate,fixed_percent',
            'bank_account' => 'nullable',
            'intro_text' => 'nullable|string',
            'conclusion_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'notes' => 'nullable|string',
            'project_name' => 'required_if:project_id,null,type,quote|nullable|string|max:255',
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
        $client = null;

        if ($recipientType === 'client') {
            // Verify client exists and belongs to the authenticated user
            $clientFind = Client::where('id', $recipientId)->where('user_id', auth()->id())->first();
            if (! $clientFind) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Client non trouvé ou non autorisé.'])
                    ->withInput();
            }
            $client = $clientFind;
        } elseif ($recipientType === 'company') {
            // Verify company exists and belongs to the authenticated user
            $companyFind = Company::where('id', $recipientId)->where('user_id', auth()->id())->first();
            if (! $companyFind) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Entreprise non trouvée ou non autorisée.'])
                    ->withInput();
            }
            $client = $companyFind;
        } else {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'Type de destinataire invalide.'])
                ->withInput();
        }

        // If this is a quote and no project is selected, create a new project
        $project_id = $validated['project_id'];
        if ($project_id) {
            // Verify project exists and belongs to the authenticated user
            $project = Project::where('id', $project_id)->where('user_id', auth()->id())->first();
            if (! $project) {
                return redirect()->back()
                    ->withErrors(['project_id' => 'Projet non trouvé ou non autorisé.'])
                    ->withInput();
            }
        } elseif ($validated['type'] === 'quote' && isset($validated['project_name'])) {
            // Create a new project
            $project = new Project;
            $project->name = $validated['project_name'];
            $project->client_id = $client->id;
            $project->client_type = $recipientType;
            $project->status = 'en_cours'; // Set status to in progress
            $project->user_id = auth()->id(); // Associate with authenticated user
            $project->save();

            $project_id = $project->id;
        }

        // Initialize totals to 0 since we're not adding lines yet
        $totalHT = 0;
        $totalTTC = 0;

        // Create the invoice/quote
        $invoice = new Invoice;
        if ($recipientType === 'client') {
            $invoice->client_id = $client->id;
            if ($client->company_id) {
                $invoice->company_id = $client->company_id;
            }
        } elseif ($recipientType === 'company') {
            $invoice->company_id = $client->id;
        }
        $invoice->project_id = $project_id;
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->type = $validated['type'];
        $invoice->status = $validated['status'];
        $invoice->is_validated = false; // Always start as not validated
        $invoice->total_ht = $totalHT;
        $invoice->tva_rate = $validated['tva_rate'];
        $invoice->total_ttc = $totalTTC;
        $invoice->issue_date = $validated['issue_date'];
        $invoice->due_date = $validated['due_date'];
        $invoice->payment_date = $validated['payment_date'] ?? null;

        // Payment terms fields
        $invoice->payment_terms = $validated['payment_terms'];
        $invoice->payment_method = $validated['payment_method'];
        $invoice->late_fees = $validated['late_fees'];
        $invoice->bank_account = $validated['bank_account'] ?? null;

        // Document text fields
        $invoice->intro_text = $validated['intro_text'] ?? null;
        $invoice->conclusion_text = $validated['conclusion_text'] ?? null;
        $invoice->footer_text = $validated['footer_text'] ?? null;

        $invoice->notes = $validated['notes'] ?? null;
        $invoice->user_id = auth()->id(); // Associate with authenticated user
        $invoice->save();

        $successMessage = $validated['type'] === 'invoice' ? 'Facture créée avec succès. Vous pouvez maintenant ajouter des lignes.' : 'Devis créé avec succès. Vous pouvez maintenant ajouter des lignes.';

        // Redirect to add invoice lines
        return redirect()->route('invoice-lines.create', ['invoice_id' => $invoice->id])
            ->with('success', $successMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Redirect to preview instead of show
        return redirect()->route('invoices.preview', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::with(['client', 'company', 'project', 'invoiceLines'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // Prevent editing of validated invoices
        if ($invoice->isValidated()) {
            return redirect()->route('invoices.preview', $invoice->id)
                ->with('error', 'Les factures validées ne peuvent pas être modifiées.');
        }

        $clients = Client::where('user_id', auth()->id())->orderBy('name')->get();
        $companies = Company::where('user_id', auth()->id())->orderBy('name')->get();
        $projects = Project::where('status', '!=', 'archive')
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
        $bankAccounts = auth()->user()->bankAccounts;

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

        // Determine the selected recipient
        $selectedRecipientId = null;
        if ($invoice->client_id) {
            $selectedRecipientId = 'client_'.$invoice->client_id;
        } elseif ($invoice->company_id) {
            $selectedRecipientId = 'company_'.$invoice->company_id;
        }

        $selectedProjectId = $invoice->project_id;
        $invoiceType = $invoice->type;
        $invoiceNumber = $invoice->invoice_number;

        // Use the create view but with the invoice data
        return view('invoices.create', compact('invoice', 'clients', 'companies', 'projects', 'recipients', 'selectedRecipientId', 'selectedProjectId', 'invoiceType', 'invoiceNumber', 'bankAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);

        // Prevent updating of validated invoices
        if ($invoice->isValidated()) {
            return redirect()->route('invoices.preview', $invoice->id)
                ->with('error', 'Les factures validées ne peuvent pas être modifiées.');
        }

        $validated = $request->validate([
            'recipient_id' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,'.$id,
            'type' => 'required|in:invoice,quote',
            'status' => 'required|in:draft,sent,paid,cancelled,overdue',
            'tva_rate' => 'required|numeric|min:0|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
            'payment_terms' => 'required|in:immediate,15_days,30_days,45_days,60_days,end_of_month',
            'payment_method' => 'required|in:bank_transfer,check,cash,credit_card,paypal',
            'late_fees' => 'required|in:none,legal_rate,fixed_percent',
            'bank_account' => 'nullable',
            'intro_text' => 'nullable|string',
            'conclusion_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
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
            // Verify client exists and belongs to the authenticated user
            $client = Client::where('id', $recipientId)->where('user_id', auth()->id())->first();
            if (! $client) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Client non trouvé ou non autorisé.'])
                    ->withInput();
            }
            $client_id = $recipientId;
        } elseif ($recipientType === 'company') {
            // Verify company exists and belongs to the authenticated user
            $company = Company::where('id', $recipientId)->where('user_id', auth()->id())->first();
            if (! $company) {
                return redirect()->back()
                    ->withErrors(['recipient_id' => 'Entreprise non trouvée ou non autorisée.'])
                    ->withInput();
            }
            $company_id = $recipientId;
        } else {
            return redirect()->back()
                ->withErrors(['recipient_id' => 'Type de destinataire invalide.'])
                ->withInput();
        }

        // Verify project exists and belongs to the authenticated user
        $project = Project::where('id', $validated['project_id'])->where('user_id', auth()->id())->first();
        if (! $project) {
            return redirect()->back()
                ->withErrors(['project_id' => 'Projet non trouvé ou non autorisé.'])
                ->withInput();
        }

        // Update the invoice
        $invoice->client_id = $client_id;
        $invoice->company_id = $company_id;
        $invoice->project_id = $validated['project_id'];
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->type = $validated['type'];
        $invoice->status = $validated['status'];
        $invoice->tva_rate = $validated['tva_rate'];
        $invoice->issue_date = $validated['issue_date'];
        $invoice->due_date = $validated['due_date'];
        $invoice->payment_date = $validated['payment_date'] ?? null;
        $invoice->notes = $validated['notes'] ?? null;

        // Payment terms fields
        $invoice->payment_terms = $validated['payment_terms'];
        $invoice->payment_method = $validated['payment_method'];
        $invoice->late_fees = $validated['late_fees'];
        $invoice->bank_account = $validated['bank_account'] ?? null;

        // Document text fields
        $invoice->intro_text = $validated['intro_text'] ?? null;
        $invoice->conclusion_text = $validated['conclusion_text'] ?? null;
        $invoice->footer_text = $validated['footer_text'] ?? null;

        $invoice->save();

        $successMessage = $invoice->isQuote() ? 'Devis mis à jour avec succès. Vous pouvez maintenant gérer les lignes.' : 'Facture mise à jour avec succès. Vous pouvez maintenant gérer les lignes.';

        // Redirect to manage invoice lines
        return redirect()->route('invoice-lines.create', ['invoice_id' => $invoice->id])
            ->with('success', $successMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);

        // Prevent deletion of validated invoices
        if ($invoice->isValidated()) {
            return redirect()->route('invoices.preview', $invoice->id)
                ->with('error', 'Les factures validées ne peuvent pas être supprimées.');
        }

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
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // Get the user's own company information
        $ownCompany = auth()->user()->company;

        // Handle logo path
        $logoPath = null;
        if ($ownCompany->logo_path) {
            try {
                // First approach: Try to get the image content and create a data URI
                $imageContent = Storage::disk('s3')->get($ownCompany->logo_path);
                if ($imageContent) {
                    // Determine the MIME type based on file extension
                    $extension = pathinfo($ownCompany->logo_path, PATHINFO_EXTENSION);
                    $mimeType = 'image/jpeg'; // Default

                    if ($extension === 'png') {
                        $mimeType = 'image/png';
                    } elseif ($extension === 'gif') {
                        $mimeType = 'image/gif';
                    } elseif ($extension === 'svg') {
                        $mimeType = 'image/svg+xml';
                    } elseif ($extension === 'webp') {
                        $mimeType = 'image/webp';
                    }

                    // Create a data URI
                    $logoPath = 'data:'.$mimeType.';base64,'.base64_encode($imageContent);
                }
            } catch (Exception $e) {
                // If the first approach fails, try the second approach
                try {
                    // Second approach: Try to generate a temporary URL
                    $logoPath = Storage::disk('s3')->temporaryUrl(
                        $ownCompany->logo_path,
                        now()->addMinutes(5)
                    );
                } catch (Exception $e) {
                    // If both approaches fail, fall back to the regular URL
                    $logoPath = env('AWS_URL').$ownCompany->logo_path;
                }
            }
        }

        // Set DomPDF options to improve image handling
        $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'ownCompany', 'logoPath'));
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isFontSubsettingEnabled', true);

        $prefix = $invoice->isQuote() ? 'devis_' : 'facture_';

        return $pdf->download($prefix.$invoice->invoice_number.'.pdf');
    }

    /**
     * Preview the invoice before finalizing
     */
    public function preview(string $id)
    {
        $invoice = Invoice::with(['client', 'company', 'project', 'invoiceLines'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('invoices.preview', compact('invoice'));
    }

    /**
     * Validate the invoice, making it non-modifiable
     */
    public function validateInvoice(string $id)
    {
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);

        // Check if the invoice already has lines
        if ($invoice->invoiceLines->count() === 0) {
            return redirect()->route('invoices.preview', $invoice->id)
                ->with('error', 'Impossible de valider une facture sans lignes.');
        }

        // Mark the invoice as validated
        $invoice->is_validated = true;
        $invoice->save();

        $successMessage = $invoice->isQuote() ? 'Devis validé avec succès.' : 'Facture validée avec succès.';

        return redirect()->route('invoices.preview', $invoice->id)
            ->with('success', $successMessage);
    }

    /**
     * Update just the status of an invoice, even if it's validated
     */
    public function updateStatus(Request $request, string $id)
    {
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid,cancelled,overdue',
        ]);

        // Update only the status
        $invoice->status = $validated['status'];
        $invoice->save();

        // If status is set to paid and payment_date is not set, update it
        if ($validated['status'] === 'paid' && ! $invoice->payment_date) {
            $invoice->payment_date = now();
            $invoice->save();
        }

        $successMessage = $invoice->isQuote() ? 'Statut du devis mis à jour avec succès.' : 'Statut de la facture mis à jour avec succès.';

        return redirect()->route('invoices.preview', $invoice->id)
            ->with('success', $successMessage);
    }
}
