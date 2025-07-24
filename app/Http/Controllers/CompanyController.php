<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::where('user_id', auth()->id())->where('is_own_company', 0)->orderBy('name')->paginate(10);

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
            'country' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_id' => 'nullable|exists:users,id',
            'regime' => 'nullable|string|in:auto-entrepreneur,eirl,eurl,sasu,sarl,sas,sa,other',
        ]);

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            $validated['logo_path'] = encodeAndUploadImg(logo: $request->file('logo'));
        }

        // If creating from profile, associate with current user
        if (! isset($validated['user_id']) && $request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        // Determine if this is the user's own company based on the referer
        if ($request->header('referer') && str_contains($request->header('referer'), 'profile')) {
            $validated['is_own_company'] = true;
        } else {
            $validated['is_own_company'] = false;
        }

        $company = Company::create($validated);

        // Determine the redirect based on the referer
        if ($request->header('referer') && str_contains($request->header('referer'), 'profile')) {
            $activeTab = $request->input('active_tab', 'company-tab');

            return redirect()->route('profile.edit', ['tab' => $activeTab])
                ->with('status', 'company-created');
        }

        return redirect()->route('companies.index')
            ->with('success', 'Entreprise créée avec succès.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = CountryService::getCountries();

        return view('companies.create', compact('countries'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::where('user_id', auth()->id())->findOrFail($id);
        $clients = $company->clients()->orderBy('name')->get();
        $invoices = $company->invoices()->orderBy('created_at', 'desc')->get();

        return view('companies.show', compact('company', 'clients', 'invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Company::where('user_id', auth()->id())->findOrFail($id);
        $countries = CountryService::getCountries();

        return view('companies.edit', compact('company', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $company = Company::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'siret' => 'nullable|string|max:14',
            'tva_number' => 'nullable|string|max:255',
            'naf_code' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'regime' => 'nullable|string|in:auto-entrepreneur,eirl,eurl,sasu,sarl,sas,sa,other',
        ]);

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            $validated['logo_path'] = encodeAndUploadImg($company, $request->file('logo'));
        }

        $company->update($validated);

        // Determine the redirect based on the referer
        if ($request->header('referer') && str_contains($request->header('referer'), 'profile')) {
            $activeTab = $request->input('active_tab', 'company-tab');

            return redirect()->route('profile.edit', ['tab' => $activeTab])
                ->with('status', 'company-updated');
        }

        return redirect()->route('companies.show', $company->id)
            ->with('success', 'Entreprise mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::where('user_id', auth()->id())->findOrFail($id);
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Entreprise supprimée avec succès.');
    }
}

function encodeAndUploadImg($company, $logo)
{
    if ($company && $company->logo_path) {
        Storage::disk('s3')->delete($company->logo_path);
    }

    $uuid = Str::uuid()->toString();
    $extension = 'webp';
    $filename = $uuid.'.'.$extension;

    // Create image manager with GD driver
    $manager = new ImageManager(new Driver);

    // Convert image to WebP format
    $img = $manager->read($logo);
    $encoded = (string) $img->toWebp(90); // 90% quality

    // Store in S3
    $path = 'company_logo/'.$filename;
    Storage::disk('s3')->put($path, $encoded, 'public');

    return $path;
}
