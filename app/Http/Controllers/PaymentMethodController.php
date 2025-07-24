<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    /**
     * Store a newly created payment method in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:stripe,paypal,other',
            'identifier' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        // If this is the default payment method, unset all other default payment methods
        if ($request->has('is_default') && $request->is_default) {
            $request->user()->paymentMethods()->update(['is_default' => false]);
        }

        $request->user()->paymentMethods()->create($validated);

        $activeTab = $request->input('active_tab', 'payment-tab');

        return Redirect::route('profile.edit', ['tab' => $activeTab])->with('status', 'payment-method-created');
    }

    /**
     * Show the form for creating a new payment method.
     */
    public function create(Request $request): View
    {
        $type = $request->query('type', 'stripe');

        return view('payment-methods.create', [
            'type' => $type,
        ]);
    }

    /**
     * Remove the specified payment method from storage.
     */
    public function destroy(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        try {
            // Use Gate facade instead of $this->authorize
            if ($request->user()->cannot('delete', $paymentMethod)) {
                throw new AuthorizationException('You are not authorized to delete this payment method.');
            }

            $paymentMethod->delete();

            $activeTab = $request->input('active_tab', 'payment-tab');

            return Redirect::route('profile.edit', ['tab' => $activeTab])->with('status', 'payment-method-deleted');
        } catch (Exception $e) {
            $activeTab = $request->input('active_tab', 'payment-tab');

            return Redirect::route('profile.edit', ['tab' => $activeTab])->with('error', 'Failed to delete payment method: '.$e->getMessage());
        }
    }
}
