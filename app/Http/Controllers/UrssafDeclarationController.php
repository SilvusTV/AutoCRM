<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\URSSAFDeclaration;
use Illuminate\Http\JsonResponse;
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
        // Get the current month, quarter, and year as default values
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $currentQuarter = ceil($currentMonth / 3);

        // Get the user
        $user = auth()->user();

        // Get the default charge rate from user profile or fallback to 22% for micro-entrepreneurs
        $defaultChargeRate = $user->tax_level ?? 22.00;

        // Get the declaration frequency from user profile
        $declarationFrequency = $user->declaration_frequency ?? 'monthly';

        // Calculate the revenue based on declaration frequency
        $revenue = 0;
        $query = Invoice::where('status', 'paid')
            ->where('user_id', auth()->id());

        if ($declarationFrequency === 'monthly') {
            // Monthly: current month
            $query->whereMonth('issue_date', $currentMonth)
                ->whereYear('issue_date', $currentYear);
        } elseif ($declarationFrequency === 'quarterly') {
            // Quarterly: current quarter
            $startMonth = ($currentQuarter - 1) * 3 + 1;
            $endMonth = $currentQuarter * 3;

            $query->whereYear('issue_date', $currentYear)
                ->where(function ($q) use ($startMonth, $endMonth) {
                    for ($i = $startMonth; $i <= $endMonth; $i++) {
                        $q->orWhereMonth('issue_date', $i);
                    }
                });
        } elseif ($declarationFrequency === 'annually') {
            // Annually: current year
            $query->whereYear('issue_date', $currentYear);
        }

        $revenue = $query->sum('total_ht');

        return view('urssaf-declarations.create', compact('currentMonth', 'currentYear', 'currentQuarter', 'defaultChargeRate', 'revenue', 'declarationFrequency'));
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
        $declaration = URSSAFDeclaration::where('user_id', auth()->id())->findOrFail($id);

        // Get the user's declaration frequency
        $user = auth()->user();
        $declarationFrequency = $user->declaration_frequency ?? 'monthly';

        return view('urssaf-declarations.show', compact('declaration', 'declarationFrequency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $declaration = URSSAFDeclaration::where('user_id', auth()->id())->findOrFail($id);

        // Get the user's declaration frequency
        $user = auth()->user();
        $declarationFrequency = $user->declaration_frequency ?? 'monthly';

        // Calculate current quarter if needed
        $currentQuarter = ceil(now()->month / 3);

        return view('urssaf-declarations.edit', compact('declaration', 'declarationFrequency', 'currentQuarter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $declaration = URSSAFDeclaration::where('user_id', auth()->id())->findOrFail($id);

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
        $declaration = URSSAFDeclaration::where('user_id', auth()->id())->findOrFail($id);

        $declaration->delete();

        return redirect()->route('urssaf-declarations.index')
            ->with('success', 'Déclaration URSSAF supprimée avec succès.');
    }

    /**
     * Calculate revenue for a specific period.
     */
    public function calculateRevenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:'.(now()->year + 1),
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = $validated['year'];
        $month = $validated['month'];

        // Get the user's declaration frequency
        $user = auth()->user();
        $declarationFrequency = $user->declaration_frequency ?? 'monthly';

        // Calculate the revenue based on declaration frequency and selected period
        $query = Invoice::where('status', 'paid')
            ->where('user_id', auth()->id());

        if ($declarationFrequency === 'monthly') {
            // Monthly: selected month
            $query->whereMonth('issue_date', $month)
                ->whereYear('issue_date', $year);
        } elseif ($declarationFrequency === 'quarterly') {
            // Quarterly: selected quarter
            $startMonth = ($month - 1) * 3 + 1;
            $endMonth = $month * 3;

            $query->whereYear('issue_date', $year)
                ->where(function ($q) use ($startMonth, $endMonth) {
                    for ($i = $startMonth; $i <= $endMonth; $i++) {
                        $q->orWhereMonth('issue_date', $i);
                    }
                });
        } elseif ($declarationFrequency === 'annually') {
            // Annually: selected year
            $query->whereYear('issue_date', $year);
        }

        $revenue = $query->sum('total_ht');

        return response()->json([
            'revenue' => $revenue,
        ]);
    }
}
