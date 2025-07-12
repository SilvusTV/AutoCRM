<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use Illuminate\Http\Request;

class InvoiceLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get the invoice ID from the request
        $invoiceId = $request->query('invoice_id');

        if (! $invoiceId) {
            return redirect()->route('invoices.index')
                ->with('error', 'ID de facture manquant.');
        }

        // Get the invoice
        $invoice = Invoice::with(['client', 'company', 'project', 'invoiceLines'])
            ->findOrFail($invoiceId);

        // Check if the invoice is validated
        if ($invoice->isValidated()) {
            return redirect()->route('invoices.preview', $invoice->id)
                ->with('error', 'Impossible d\'ajouter des lignes à une facture validée.');
        }

        return view('invoice-lines.create', compact('invoice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'description' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'item_type' => 'nullable|in:service,product',
            'is_expense' => 'nullable|boolean',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'tva_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        // Get the invoice
        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // Check if the invoice is validated
        if ($invoice->isValidated()) {
            return redirect()->route('invoices.preview', $invoice->id)
                ->with('error', 'Impossible d\'ajouter des lignes à une facture validée.');
        }

        // Calculate total HT
        $totalHT = $validated['quantity'] * $validated['unit_price'];

        // Apply discount if any
        if (isset($validated['discount_percent']) && $validated['discount_percent'] > 0) {
            $totalHT = $totalHT * (1 - ($validated['discount_percent'] / 100));
        }

        // Create the invoice line
        $invoiceLine = new InvoiceLine;
        $invoiceLine->invoice_id = $validated['invoice_id'];
        $invoiceLine->description = $validated['description'];
        $invoiceLine->quantity = $validated['quantity'];
        $invoiceLine->unit_price = $validated['unit_price'];
        $invoiceLine->item_type = $validated['item_type'] ?? 'service';
        $invoiceLine->is_expense = $validated['is_expense'] ?? false;
        $invoiceLine->discount_percent = $validated['discount_percent'] ?? 0;
        $invoiceLine->tva_rate = $validated['tva_rate'] ?? null;
        $invoiceLine->total_ht = $totalHT;
        $invoiceLine->save();

        // Update the invoice total
        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $newTotalHT = $invoice->invoiceLines->sum('total_ht');
        $invoice->total_ht = $newTotalHT;
        $invoice->total_ttc = $newTotalHT * (1 + ($invoice->tva_rate / 100));
        $invoice->save();

        return redirect()->route('invoice-lines.create', ['invoice_id' => $invoice->id])
            ->with('success', 'Ligne de facture ajoutée avec succès. Vous pouvez continuer à ajouter d\'autres lignes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoiceLine = InvoiceLine::findOrFail($id);
        $invoice = $invoiceLine->invoice;

        // Check if the invoice is validated
        if ($invoice->isValidated()) {
            return redirect()->route('invoices.preview', $invoice->id)
                ->with('error', 'Impossible de modifier les lignes d\'une facture validée.');
        }

        $validated = $request->validate([
            'description' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'item_type' => 'nullable|in:service,product',
            'is_expense' => 'nullable|boolean',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'tva_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        // Calculate total HT
        $totalHT = $validated['quantity'] * $validated['unit_price'];

        // Apply discount if any
        if (isset($validated['discount_percent']) && $validated['discount_percent'] > 0) {
            $totalHT = $totalHT * (1 - ($validated['discount_percent'] / 100));
        }

        // Update the invoice line
        $invoiceLine->description = $validated['description'];
        $invoiceLine->quantity = $validated['quantity'];
        $invoiceLine->unit_price = $validated['unit_price'];
        $invoiceLine->item_type = $validated['item_type'] ?? $invoiceLine->item_type;
        $invoiceLine->is_expense = $validated['is_expense'] ?? $invoiceLine->is_expense;
        $invoiceLine->discount_percent = $validated['discount_percent'] ?? $invoiceLine->discount_percent;
        $invoiceLine->tva_rate = $validated['tva_rate'] ?? $invoiceLine->tva_rate;
        $invoiceLine->total_ht = $totalHT;
        $invoiceLine->save();

        // Update the invoice total
        $invoice = $invoiceLine->invoice;
        $newTotalHT = $invoice->invoiceLines->sum('total_ht');
        $invoice->total_ht = $newTotalHT;
        $invoice->total_ttc = $newTotalHT * (1 + ($invoice->tva_rate / 100));
        $invoice->save();

        return redirect()->route('invoices.preview', $invoice->id)
            ->with('success', 'Ligne de facture mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoiceLine = InvoiceLine::findOrFail($id);
        $invoice = $invoiceLine->invoice;

        // Check if the invoice is validated
        if ($invoice->isValidated()) {
            return redirect()->route('invoices.preview', $invoice->id)
                ->with('error', 'Impossible de supprimer les lignes d\'une facture validée.');
        }

        // Delete the invoice line
        $invoiceLine->delete();

        // Update the invoice total
        $newTotalHT = $invoice->invoiceLines->sum('total_ht');
        $invoice->total_ht = $newTotalHT;
        $invoice->total_ttc = $newTotalHT * (1 + ($invoice->tva_rate / 100));
        $invoice->save();

        return redirect()->route('invoices.preview', $invoice->id)
            ->with('success', 'Ligne de facture supprimée avec succès.');
    }
}
