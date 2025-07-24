<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class UrssafController extends Controller
{
    /**
     * Update the user's URSSAF information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'declaration_frequency' => ['nullable', 'string', 'in:monthly,quarterly,annually'],
            'tax_level' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $user = $request->user();
        $user->update($validated);

        $activeTab = $request->input('active_tab', 'urssaf-tab');

        return Redirect::route('profile.edit', ['tab' => $activeTab])->with('status', 'urssaf-updated');
    }
}
