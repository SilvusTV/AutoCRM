<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Nouvelle facture') }}
            </h2>
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('invoices.store') }}" class="space-y-6">
                        @csrf

                        <!-- Client -->
                        <div>
                            <x-input-label for="client_id" :value="__('Client')" />
                            <select id="client_id" name="client_id" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">Sélectionnez un client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $selectedClientId) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} {{ $client->company ? '(' . $client->company . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                        </div>

                        <!-- Project -->
                        <div>
                            <x-input-label for="project_id" :value="__('Projet')" />
                            <select id="project_id" name="project_id" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">Sélectionnez un projet</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $selectedProjectId) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->client->name }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                        </div>

                        <!-- Invoice Number -->
                        <div>
                            <x-input-label for="invoice_number" :value="__('Numéro de facture')" />
                            <x-text-input id="invoice_number" class="block mt-1 w-full" type="text" name="invoice_number" :value="old('invoice_number', 'FACT-' . date('Ymd') . '-' . rand(100, 999))" required />
                            <x-input-error :messages="$errors->get('invoice_number')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Statut')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="brouillon" {{ old('status') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                <option value="envoyee" {{ old('status') == 'envoyee' ? 'selected' : '' }}>Envoyée</option>
                                <option value="payee" {{ old('status') == 'payee' ? 'selected' : '' }}>Payée</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Total HT -->
                        <div>
                            <x-input-label for="total_ht" :value="__('Montant HT')" />
                            <x-text-input id="total_ht" class="block mt-1 w-full" type="number" name="total_ht" :value="old('total_ht', 0)" step="0.01" min="0" required />
                            <x-input-error :messages="$errors->get('total_ht')" class="mt-2" />
                        </div>

                        <!-- TVA Rate -->
                        <div>
                            <x-input-label for="tva_rate" :value="__('Taux de TVA (%)')" />
                            <x-text-input id="tva_rate" class="block mt-1 w-full" type="number" name="tva_rate" :value="old('tva_rate', 20)" step="0.01" min="0" max="100" required />
                            <x-input-error :messages="$errors->get('tva_rate')" class="mt-2" />
                        </div>

                        <!-- Issue Date -->
                        <div>
                            <x-input-label for="issue_date" :value="__('Date d\'émission')" />
                            <x-text-input id="issue_date" class="block mt-1 w-full" type="date" name="issue_date" :value="old('issue_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                        </div>

                        <!-- Due Date -->
                        <div>
                            <x-input-label for="due_date" :value="__('Date d\'échéance')" />
                            <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', date('Y-m-d', strtotime('+30 days')))" required />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>

                        <!-- Payment Date -->
                        <div>
                            <x-input-label for="payment_date" :value="__('Date de paiement (optionnel)')" />
                            <x-text-input id="payment_date" class="block mt-1 w-full" type="date" name="payment_date" :value="old('payment_date')" />
                            <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes (optionnel)')" />
                            <textarea id="notes" name="notes" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="3">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Créer la facture') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>