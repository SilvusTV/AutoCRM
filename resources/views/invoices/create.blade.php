<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                @if(isset($invoice))
                    {{ $invoice->isQuote() ? __('Modifier le devis') : __('Modifier la facture') }}
                    : {{ $invoice->invoice_number }}
                @else
                    {{ isset($invoiceType) && $invoiceType === 'quote' ? __('Nouveau devis') : __('Nouvelle facture') }}
                @endif
            </h2>
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST"
                  action="{{ isset($invoice) ? route('invoices.update', $invoice->id) : route('invoices.store') }}"
                  class="space-y-6" id="invoice-form">
                @csrf
                @if(isset($invoice))
                    @method('PUT')
                @endif

                <!-- Hidden Type Field -->
                <input type="hidden" name="type" value="{{ $invoiceType ?? 'invoice' }}"/>

                <!-- Status field -->
                @if(isset($invoice))
                    <input type="hidden" name="status" value="{{ $invoice->status }}"/>
                @else
                    <!-- Status is always draft when creating -->
                    <input type="hidden" name="status" value="draft"/>
                @endif

                <!-- Section: Destinataire -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-white">{{ __('Destinataire') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Combined Recipient Selector -->
                        <div>
                            <x-input-label for="recipient_id" :value="__('Destinataire')"/>
                            <select id="recipient_id" name="recipient_id"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">Sélectionnez un destinataire</option>
                                @foreach($recipients as $recipient)
                                    <option value="{{ $recipient['id'] }}" {{ old('recipient_id', $selectedRecipientId) == $recipient['id'] ? 'selected' : '' }}>
                                        {{ $recipient['type_icon'] }} {{ $recipient['name'] }} {{ $recipient['details'] }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('recipient_id')" class="mt-2"/>
                        </div>

                        <!-- Project -->
                        <div id="existing-project-div">
                            <x-input-label for="project_id" :value="__('Projet existant')"/>
                            <select id="project_id" name="project_id"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                    onchange="toggleProjectNameField()">
                                <option value="">Sélectionnez un projet</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $selectedProjectId) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                        ({{ $project->client->name ?? $project->company->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                        </div>

                        <!-- New Project Name (for quotes) -->
                        <div id="new-project-div"
                             class="{{ isset($invoiceType) && $invoiceType === 'quote' ? (old('project_id', $selectedProjectId) ? 'hidden' : '') : 'hidden' }}">
                            <x-input-label for="project_name" :value="__('Nom du nouveau projet')"/>
                            <x-text-input id="project_name" class="block mt-1 w-full" type="text" name="project_name"
                                          :value="old('project_name')"/>
                            <x-input-error :messages="$errors->get('project_name')" class="mt-2"/>
                        </div>
                    </div>
                </div>

                <!-- Section: Informations générales -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-white">{{ __('Informations générales') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Invoice Number -->
                        <div>
                            <x-input-label for="invoice_number" :value="__('Numéro de facture')" />
                            <x-text-input id="invoice_number" class="block mt-1 w-full bg-gray-100" type="text"
                                          name="invoice_number"
                                          :value="old('invoice_number', isset($invoiceType) && $invoiceType === 'quote' ? $quoteNumber : $invoiceNumber)"
                                          required
                                          readonly/>
                            <x-input-error :messages="$errors->get('invoice_number')" class="mt-2" />
                        </div>


                        <!-- TVA Non Applicable Checkbox -->
                        <div>
                            <div class="flex items-center mt-4">
                                <input id="tva_non_applicable" type="checkbox" name="tva_non_applicable"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                       value="1"
                                       {{ old('tva_non_applicable', isset($invoice) ? ($invoice->tva_rate == 0) : true) ? 'checked' : '' }} onchange="toggleTvaRate()">
                                <label for="tva_non_applicable"
                                       class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('TVA non applicable') }}</label>
                            </div>
                            <x-input-error :messages="$errors->get('tva_non_applicable')" class="mt-2"/>
                        </div>

                        <!-- TVA Rate (disabled when TVA is not applicable) -->
                        <div id="tva_rate_container">
                            <x-input-label for="tva_rate" :value="__('TVA (%)')"/>
                            <select id="tva_rate" name="tva_rate"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="0" {{ old('tva_rate', isset($invoice) ? $invoice->tva_rate : '') == '0' ? 'selected' : '' }}>
                                    0%
                                </option>
                                <option value="5.5" {{ old('tva_rate', isset($invoice) ? $invoice->tva_rate : '') == '5.5' ? 'selected' : '' }}>
                                    5.5%
                                </option>
                                <option value="10" {{ old('tva_rate', isset($invoice) ? $invoice->tva_rate : '') == '10' ? 'selected' : '' }}>
                                    10%
                                </option>
                                <option value="20" {{ old('tva_rate', isset($invoice) ? $invoice->tva_rate : 20) == '20' ? 'selected' : '' }}>
                                    20%
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('tva_rate')" class="mt-2"/>
                        </div>

                        <!-- Currency -->
                        <div>
                            <x-input-label for="currency" :value="__('Devise')"/>
                            <select id="currency" name="currency"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="EUR" {{ old('currency', 'EUR') == 'EUR' ? 'selected' : '' }}>Euro (€)
                                </option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>Dollar ($)</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>Livre Sterling
                                    (£)
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2"/>
                        </div>

                        <!-- Hidden fields for dates -->
                        <input type="hidden" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}"/>
                        <input type="hidden" name="due_date"
                               value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}"/>
                        <input type="hidden" name="payment_date" value="{{ old('payment_date') }}"/>
                    </div>
                </div>

                <!-- Section: Note about Invoice Lines -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-white">{{ __('Articles et Débours') }}</h3>

                    <div class="text-center py-4">
                        <p class="text-gray-500 dark:text-gray-400">Les lignes de facture pourront être ajoutées après
                            la création de la facture.</p>
                    </div>
                </div>

                <!-- Section: Règlement -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-white">{{ __('Règlement') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Payment Terms -->
                        <div>
                            <x-input-label for="payment_terms" :value="__('Conditions de règlement')"/>
                            <select id="payment_terms" name="payment_terms"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="immediate" {{ old('payment_terms', isset($invoice) ? $invoice->payment_terms : '') == 'immediate' ? 'selected' : '' }}>
                                    Paiement immédiat
                                </option>
                                <option value="15_days" {{ old('payment_terms', isset($invoice) ? $invoice->payment_terms : '') == '15_days' ? 'selected' : '' }}>
                                    15 jours
                                </option>
                                <option value="30_days" {{ old('payment_terms', isset($invoice) ? $invoice->payment_terms : '30_days') == '30_days' ? 'selected' : '' }}>
                                    30 jours
                                </option>
                                <option value="45_days" {{ old('payment_terms', isset($invoice) ? $invoice->payment_terms : '') == '45_days' ? 'selected' : '' }}>
                                    45 jours
                                </option>
                                <option value="60_days" {{ old('payment_terms', isset($invoice) ? $invoice->payment_terms : '') == '60_days' ? 'selected' : '' }}>
                                    60 jours
                                </option>
                                <option value="end_of_month" {{ old('payment_terms', isset($invoice) ? $invoice->payment_terms : '') == 'end_of_month' ? 'selected' : '' }}>
                                    Fin de mois
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_terms')" class="mt-2"/>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <x-input-label for="payment_method" :value="__('Mode de règlement')"/>
                            <select id="payment_method" name="payment_method"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="bank_transfer" {{ old('payment_method', isset($invoice) ? $invoice->payment_method : 'bank_transfer') == 'bank_transfer' ? 'selected' : '' }}>
                                    Virement bancaire
                                </option>
                                <option value="check" {{ old('payment_method', isset($invoice) ? $invoice->payment_method : '') == 'check' ? 'selected' : '' }}>
                                    Chèque
                                </option>
                                <option value="cash" {{ old('payment_method', isset($invoice) ? $invoice->payment_method : '') == 'cash' ? 'selected' : '' }}>
                                    Espèces
                                </option>
                                <option value="credit_card" {{ old('payment_method', isset($invoice) ? $invoice->payment_method : '') == 'credit_card' ? 'selected' : '' }}>
                                    Carte bancaire
                                </option>
                                <option value="paypal" {{ old('payment_method', isset($invoice) ? $invoice->payment_method : '') == 'paypal' ? 'selected' : '' }}>
                                    PayPal
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2"/>
                        </div>

                        <!-- Late Fees -->
                        <div>
                            <x-input-label for="late_fees" :value="__('Intérêts de retard')"/>
                            <select id="late_fees" name="late_fees"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="none" {{ old('late_fees', isset($invoice) ? $invoice->late_fees : 'none') == 'none' ? 'selected' : '' }}>
                                    Pas d'intérêts
                                </option>
                                <option value="legal_rate" {{ old('late_fees', isset($invoice) ? $invoice->late_fees : '') == 'legal_rate' ? 'selected' : '' }}>
                                    Taux légal
                                </option>
                                <option value="fixed_percent" {{ old('late_fees', isset($invoice) ? $invoice->late_fees : '') == 'fixed_percent' ? 'selected' : '' }}>
                                    Taux fixe
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('late_fees')" class="mt-2"/>
                        </div>

                        <!-- Bank Account (shown only when payment method is bank transfer) -->
                        <div id="bank_account_container" style="display: none;">
                            <x-input-label for="bank_account" :value="__('Compte bancaire (RIB)')"/>
                            <select id="bank_account" name="bank_account"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">{{ __('Sélectionnez un compte bancaire') }}</option>
                                @foreach($bankAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('bank_account', isset($invoice) ? $invoice->bank_account : '') == $account->id ? 'selected' : '' }}>
                                        {{ $account->account_name }} - {{ $account->iban }} ({{ $account->bank_name }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('bank_account')" class="mt-2"/>
                        </div>

                        <!-- Note about legal rates -->
                        <div class="md:col-span-2 mt-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <strong>Note:</strong> Le taux légal correspond au taux d'intérêt légal fixé par décret
                                pour le semestre concerné.
                                Le taux fixe correspond à un pourcentage défini dans les conditions générales de vente.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section: Textes affichés sur le document -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-white">{{ __('Textes affichés sur le document') }}</h3>

                    <div class="space-y-6">
                        <!-- Intro Text -->
                        <div>
                            <x-input-label for="intro_text" :value="__('Texte d\'introduction')"/>
                            <textarea id="intro_text" name="intro_text"
                                      class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                      rows="3">{{ old('intro_text', isset($invoice) ? $invoice->intro_text : '') }}</textarea>
                            <x-input-error :messages="$errors->get('intro_text')" class="mt-2"/>
                        </div>

                        <!-- Conclusion Text -->
                        <div>
                            <x-input-label for="conclusion_text" :value="__('Texte de conclusion')"/>
                            <textarea id="conclusion_text" name="conclusion_text"
                                      class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                      rows="3">{{ old('conclusion_text', isset($invoice) ? $invoice->conclusion_text : '') }}</textarea>
                            <x-input-error :messages="$errors->get('conclusion_text')" class="mt-2"/>
                        </div>

                        <!-- Footer Text -->
                        <div>
                            <x-input-label for="footer_text" :value="__('Pied de page')"/>
                            <textarea id="footer_text" name="footer_text"
                                      class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                      rows="3">{{ old('footer_text', isset($invoice) ? $invoice->footer_text : 'Dispensé d\'immatriculation au Registre du Commerce et des Sociétés (RCS) et au Répertoire des Métiers (RM).') }}</textarea>
                            <x-input-error :messages="$errors->get('footer_text')" class="mt-2"/>
                        </div>
                    </div>
                </div>

                <!-- Section: Notes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-white">{{ __('Notes') }}</h3>

                    <div>
                        <x-input-label for="notes" :value="__('Notes internes (non affichées sur la facture)')"/>
                        <textarea id="notes" name="notes"
                                  class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                  rows="3">{{ old('notes', isset($invoice) ? $invoice->notes : '') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2"/>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end">
                    <x-primary-button>
                        @if(isset($invoice))
                            {{ $invoice->isQuote() ? __('Mettre à jour le devis') : __('Mettre à jour la facture') }}
                        @else
                            {{ isset($invoiceType) && $invoiceType === 'quote' ? __('Créer le devis') : __('Créer la facture') }}
                        @endif
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Initial toggle of TVA rate field based on checkbox
        toggleTvaRate();

        // Add event listener for payment method to show/hide bank account field
        document.getElementById('payment_method').addEventListener('change', function () {
          toggleBankAccount();
        });

        // Initial toggle of bank account field
        toggleBankAccount();

        // Initial toggle of project name field
        toggleProjectNameField();

        // Add event listener for TVA rate changes
        document.getElementById('tva_rate').addEventListener('change', updateTvaRateDisplay);
        document.getElementById('tva_non_applicable').addEventListener('change', updateTvaRateDisplay);
      });

      function toggleTvaRate() {
        const tvaNonApplicable = document.getElementById('tva_non_applicable').checked;
        const tvaRateSelect = document.getElementById('tva_rate');
        const zeroOption = tvaRateSelect.querySelector('option[value="0"]');

        if (tvaNonApplicable) {
          tvaRateSelect.value = '0';
        } else {
          tvaRateSelect.disabled = false; // Enable the select element
        }

        updateTvaRateDisplay();
      }

      function toggleBankAccount() {
        const paymentMethod = document.getElementById('payment_method').value;
        const bankAccountContainer = document.getElementById('bank_account_container');

        if (paymentMethod === 'bank_transfer') {
          bankAccountContainer.style.display = 'block';
        } else {
          bankAccountContainer.style.display = 'none';
        }
      }

      function updateTvaRateDisplay() {
        const tvaRate = document.getElementById('tva_non_applicable').checked ? 0 : parseFloat(document.getElementById('tva_rate').value);
        // No need to update display as we removed the totals section
      }

      function formatCurrency(value) {
        return new Intl.NumberFormat('fr-FR', {style: 'currency', currency: 'EUR'}).format(value);
      }

      function toggleProjectNameField() {
        const invoiceType = document.querySelector('input[name="type"]').value;
        const projectId = document.getElementById('project_id').value;
        const newProjectDiv = document.getElementById('new-project-div');

        // Only show the project name field for quotes when no project is selected
        if (invoiceType === 'quote' && !projectId) {
          newProjectDiv.classList.remove('hidden');
        } else {
          newProjectDiv.classList.add('hidden');
        }
      }
    </script>
</x-app-layout>
