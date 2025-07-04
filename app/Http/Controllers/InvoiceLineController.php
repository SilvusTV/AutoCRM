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
    public function create()
    {
        //
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
        ]);

        // Calculate total HT
        $totalHT = $validated['quantity'] * $validated['unit_price'];

        // Create the invoice line
        $invoiceLine = new InvoiceLine;
        $invoiceLine->invoice_id = $validated['invoice_id'];
        $invoiceLine->description = $validated['description'];
        $invoiceLine->quantity = $validated['quantity'];
        $invoiceLine->unit_price = $validated['unit_price'];
        $invoiceLine->total_ht = $totalHT;
        $invoiceLine->save();

        // Update the invoice total
        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $newTotalHT = $invoice->invoiceLines->sum('total_ht');
        $invoice->total_ht = $newTotalHT;
        $invoice->total_ttc = $newTotalHT * (1 + ($invoice->tva_rate / 100));
        $invoice->save();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Ligne de facture ajoutée avec succès.');
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

        $validated = $request->validate([
            'description' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate total HT
        $totalHT = $validated['quantity'] * $validated['unit_price'];

        // Update the invoice line
        $invoiceLine->description = $validated['description'];
        $invoiceLine->quantity = $validated['quantity'];
        $invoiceLine->unit_price = $validated['unit_price'];
        $invoiceLine->total_ht = $totalHT;
        $invoiceLine->save();

        // Update the invoice total
        $invoice = $invoiceLine->invoice;
        $newTotalHT = $invoice->invoiceLines->sum('total_ht');
        $invoice->total_ht = $newTotalHT;
        $invoice->total_ttc = $newTotalHT * (1 + ($invoice->tva_rate / 100));
        $invoice->save();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Ligne de facture mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoiceLine = InvoiceLine::findOrFail($id);
        $invoice = $invoiceLine->invoice;

        // Delete the invoice line
        $invoiceLine->delete();

        // Update the invoice total
        $newTotalHT = $invoice->invoiceLines->sum('total_ht');
        $invoice->total_ht = $newTotalHT;
        $invoice->total_ttc = $newTotalHT * (1 + ($invoice->tva_rate / 100));
        $invoice->save();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Ligne de facture supprimée avec succès.');
    }
}
