<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::orderBy('name')->paginate(10);

        return view('companies.index', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'siret' => 'nullable|string|max:14',
            'tva_number' => 'nullable|string|max:255',
            'naf_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        $company = Company::create($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Entreprise créée avec succès.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::findOrFail($id);
        $clients = $company->clients()->orderBy('name')->get();
        $invoices = $company->invoices()->orderBy('created_at', 'desc')->get();

        return view('companies.show', compact('company', 'clients', 'invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Company::findOrFail($id);

        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'siret' => 'nullable|string|max:14',
            'tva_number' => 'nullable|string|max:255',
            'naf_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        $company->update($validated);

        return redirect()->route('companies.show', $company->id)
            ->with('success', 'Entreprise mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Entreprise supprimée avec succès.');
    }
}
