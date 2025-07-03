<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
        $invoices = Invoice::with(['client', 'project'])
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
        $projects = Project::where('status', '!=', 'archive')
            ->orderBy('name')
            ->get();

        $selectedClientId = $request->query('client_id');
        $selectedProjectId = $request->query('project_id');

        return view('invoices.create', compact('clients', 'projects', 'selectedClientId', 'selectedProjectId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
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

        // Calculate total TTC
        $totalTTC = $validated['total_ht'] * (1 + ($validated['tva_rate'] / 100));

        // Create the invoice
        $invoice = new Invoice();
        $invoice->client_id = $validated['client_id'];
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
        $invoice = Invoice::with(['client', 'project', 'invoiceLines'])
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
        $projects = Project::orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'clients', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'required|exists:projects,id',
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $id,
            'status' => 'required|in:brouillon,envoyee,payee',
            'total_ht' => 'required|numeric|min:0',
            'tva_rate' => 'required|numeric|min:0|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
        ]);

        // Calculate total TTC
        $totalTTC = $validated['total_ht'] * (1 + ($validated['tva_rate'] / 100));

        // Update the invoice
        $invoice->client_id = $validated['client_id'];
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
        $invoice = Invoice::with(['client', 'project', 'invoiceLines'])
            ->findOrFail($id);

        $pdf = PDF::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('facture_' . $invoice->invoice_number . '.pdf');
    }
}
