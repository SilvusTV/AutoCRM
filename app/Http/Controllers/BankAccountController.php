<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BankAccountController extends Controller
{
    /**
     * Store a newly created bank account in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
            'bic' => 'required|string|max:255',
            'is_default' => 'boolean',
        ]);

        // If this is the default account, unset all other default accounts
        if ($request->has('is_default') && $request->is_default) {
            $request->user()->bankAccounts()->update(['is_default' => false]);
        }

        $request->user()->bankAccounts()->create($validated);

        return Redirect::route('profile.edit')->with('status', 'bank-account-created');
    }

    /**
     * Update the specified bank account in storage.
     */
    public function update(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        try {
            // Check if user is authorized to update this bank account
            if ($request->user()->cannot('update', $bankAccount)) {
                throw new AuthorizationException('You are not authorized to update this bank account.');
            }

            $validated = $request->validate([
                'account_name' => 'required|string|max:255',
                'account_holder' => 'required|string|max:255',
                'bank_name' => 'required|string|max:255',
                'iban' => 'required|string|max:255',
                'bic' => 'required|string|max:255',
                'is_default' => 'boolean',
            ]);

            // If this is the default account, unset all other default accounts
            if ($request->has('is_default') && $request->is_default) {
                $request->user()->bankAccounts()->where('id', '!=', $bankAccount->id)->update(['is_default' => false]);
            }

            $bankAccount->update($validated);

            return Redirect::route('profile.edit')->with('status', 'bank-account-updated');
        } catch (Exception $e) {
            return Redirect::route('profile.edit')->with('error', 'Failed to update bank account: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified bank account.
     */
    public function edit(Request $request, BankAccount $bankAccount)
    {
        try {
            // Check if user is authorized to update this bank account
            if ($request->user()->cannot('update', $bankAccount)) {
                throw new AuthorizationException('You are not authorized to edit this bank account.');
            }

            return response()->json($bankAccount);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Set the specified bank account as default.
     */
    public function setDefault(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        try {
            // Check if user is authorized to update this bank account
            if ($request->user()->cannot('update', $bankAccount)) {
                throw new AuthorizationException('You are not authorized to update this bank account.');
            }

            // Unset all other bank accounts as default
            $request->user()->bankAccounts()->where('id', '!=', $bankAccount->id)->update(['is_default' => false]);

            // Set this bank account as default
            $bankAccount->update(['is_default' => true]);

            return Redirect::route('profile.edit')->with('status', 'bank-account-set-as-default');
        } catch (Exception $e) {
            return Redirect::route('profile.edit')->with('error', 'Failed to set bank account as default: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified bank account from storage.
     */
    public function destroy(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        try {
            // Use Gate facade instead of $this->authorize
            if ($request->user()->cannot('delete', $bankAccount)) {
                throw new AuthorizationException('You are not authorized to delete this bank account.');
            }

            $bankAccount->delete();

            return Redirect::route('profile.edit')->with('status', 'bank-account-deleted');
        } catch (Exception $e) {
            return Redirect::route('profile.edit')->with('error', 'Failed to delete bank account: '.$e->getMessage());
        }
    }
}
