<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\URSSAFDeclaration;
use Illuminate\Http\Request;

class UrssafDeclarationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $declarations = URSSAFDeclaration::where('user_id', auth()->id())
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(10);

        return view('urssaf-declarations.index', compact('declarations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get the current month and year as default values
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Get the default charge rate (22% for micro-entrepreneurs)
        $defaultChargeRate = 22.00;

        // Calculate the total revenue for the current month from invoices
        $monthlyRevenue = Invoice::whereMonth('issue_date', $currentMonth)
            ->whereYear('issue_date', $currentYear)
            ->where('status', 'payee')
            ->sum('total_ht');

        return view('urssaf-declarations.create', compact('currentMonth', 'currentYear', 'defaultChargeRate', 'monthlyRevenue'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:'.(now()->year + 1),
            'month' => 'required|integer|min:1|max:12',
            'declared_revenue' => 'required|numeric|min:0',
            'charge_rate' => 'required|numeric|min:0|max:100',
            'is_paid' => 'boolean',
            'payment_date' => 'nullable|required_if:is_paid,1|date',
        ]);

        // Check if a declaration already exists for this month and year
        $existingDeclaration = URSSAFDeclaration::where('user_id', auth()->id())
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->first();

        if ($existingDeclaration) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['month' => 'Une déclaration existe déjà pour cette période.']);
        }

        // Calculate charges amount
        $chargesAmount = $validated['declared_revenue'] * ($validated['charge_rate'] / 100);

        // Create the declaration
        $declaration = new URSSAFDeclaration;
        $declaration->user_id = auth()->id();
        $declaration->year = $validated['year'];
        $declaration->month = $validated['month'];
        $declaration->declared_revenue = $validated['declared_revenue'];
        $declaration->charge_rate = $validated['charge_rate'];
        $declaration->charges_amount = $chargesAmount;
        $declaration->is_paid = $request->has('is_paid');
        $declaration->payment_date = $request->has('is_paid') ? $validated['payment_date'] : null;
        $declaration->save();

        return redirect()->route('urssaf-declarations.show', $declaration->id)
            ->with('success', 'Déclaration URSSAF créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $declaration = URSSAFDeclaration::findOrFail($id);

        // Ensure the user can only view their own declarations
        if ($declaration->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('urssaf-declarations.show', compact('declaration'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $declaration = URSSAFDeclaration::findOrFail($id);

        // Ensure the user can only edit their own declarations
        if ($declaration->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('urssaf-declarations.edit', compact('declaration'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $declaration = URSSAFDeclaration::findOrFail($id);

        // Ensure the user can only update their own declarations
        if ($declaration->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:'.(now()->year + 1),
            'month' => 'required|integer|min:1|max:12',
            'declared_revenue' => 'required|numeric|min:0',
            'charge_rate' => 'required|numeric|min:0|max:100',
            'is_paid' => 'boolean',
            'payment_date' => 'nullable|required_if:is_paid,1|date',
        ]);

        // Check if another declaration exists for this month and year
        $existingDeclaration = URSSAFDeclaration::where('user_id', auth()->id())
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->where('id', '!=', $id)
            ->first();

        if ($existingDeclaration) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['month' => 'Une autre déclaration existe déjà pour cette période.']);
        }

        // Calculate charges amount
        $chargesAmount = $validated['declared_revenue'] * ($validated['charge_rate'] / 100);

        // Update the declaration
        $declaration->year = $validated['year'];
        $declaration->month = $validated['month'];
        $declaration->declared_revenue = $validated['declared_revenue'];
        $declaration->charge_rate = $validated['charge_rate'];
        $declaration->charges_amount = $chargesAmount;
        $declaration->is_paid = $request->has('is_paid');
        $declaration->payment_date = $request->has('is_paid') ? $validated['payment_date'] : null;
        $declaration->save();

        return redirect()->route('urssaf-declarations.show', $declaration->id)
            ->with('success', 'Déclaration URSSAF mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $declaration = URSSAFDeclaration::findOrFail($id);

        // Ensure the user can only delete their own declarations
        if ($declaration->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $declaration->delete();

        return redirect()->route('urssaf-declarations.index')
            ->with('success', 'Déclaration URSSAF supprimée avec succès.');
    }
}
