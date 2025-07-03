<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modifier la déclaration URSSAF') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('urssaf-declarations.show', $declaration->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Voir les détails') }}
                </a>
                <a href="{{ route('urssaf-declarations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('urssaf-declarations.update', $declaration->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Year -->
                            <div>
                                <x-input-label for="year" :value="__('Année')" />
                                <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', $declaration->year)" min="2000" max="{{ now()->year + 1 }}" required />
                                <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            </div>

                            <!-- Month -->
                            <div>
                                <x-input-label for="month" :value="__('Mois')" />
                                <select id="month" name="month" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="1" {{ old('month', $declaration->month) == 1 ? 'selected' : '' }}>Janvier</option>
                                    <option value="2" {{ old('month', $declaration->month) == 2 ? 'selected' : '' }}>Février</option>
                                    <option value="3" {{ old('month', $declaration->month) == 3 ? 'selected' : '' }}>Mars</option>
                                    <option value="4" {{ old('month', $declaration->month) == 4 ? 'selected' : '' }}>Avril</option>
                                    <option value="5" {{ old('month', $declaration->month) == 5 ? 'selected' : '' }}>Mai</option>
                                    <option value="6" {{ old('month', $declaration->month) == 6 ? 'selected' : '' }}>Juin</option>
                                    <option value="7" {{ old('month', $declaration->month) == 7 ? 'selected' : '' }}>Juillet</option>
                                    <option value="8" {{ old('month', $declaration->month) == 8 ? 'selected' : '' }}>Août</option>
                                    <option value="9" {{ old('month', $declaration->month) == 9 ? 'selected' : '' }}>Septembre</option>
                                    <option value="10" {{ old('month', $declaration->month) == 10 ? 'selected' : '' }}>Octobre</option>
                                    <option value="11" {{ old('month', $declaration->month) == 11 ? 'selected' : '' }}>Novembre</option>
                                    <option value="12" {{ old('month', $declaration->month) == 12 ? 'selected' : '' }}>Décembre</option>
                                </select>
                                <x-input-error :messages="$errors->get('month')" class="mt-2" />
                            </div>

                            <!-- Declared Revenue -->
                            <div>
                                <x-input-label for="declared_revenue" :value="__('Revenu déclaré (€)')" />
                                <x-text-input id="declared_revenue" class="block mt-1 w-full" type="number" name="declared_revenue" :value="old('declared_revenue', $declaration->declared_revenue)" step="0.01" min="0" required />
                                <x-input-error :messages="$errors->get('declared_revenue')" class="mt-2" />
                            </div>

                            <!-- Charge Rate -->
                            <div>
                                <x-input-label for="charge_rate" :value="__('Taux de cotisation (%)')" />
                                <x-text-input id="charge_rate" class="block mt-1 w-full" type="number" name="charge_rate" :value="old('charge_rate', $declaration->charge_rate)" step="0.01" min="0" max="100" required />
                                <x-input-error :messages="$errors->get('charge_rate')" class="mt-2" />
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Taux par défaut pour les micro-entrepreneurs : 22%
                                </p>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="mt-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_paid" name="is_paid" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('is_paid', $declaration->is_paid) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_paid" class="font-medium text-gray-700 dark:text-gray-300">Déjà payée</label>
                                    <p class="text-gray-500 dark:text-gray-400">Cochez cette case si vous avez déjà payé cette déclaration</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Date (visible only when is_paid is checked) -->
                        <div id="payment_date_container" class="mt-4 {{ old('is_paid', $declaration->is_paid) ? '' : 'hidden' }}">
                            <x-input-label for="payment_date" :value="__('Date de paiement')" />
                            <x-text-input id="payment_date" class="block mt-1 w-full" type="date" name="payment_date" :value="old('payment_date', $declaration->payment_date ? $declaration->payment_date->format('Y-m-d') : now()->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Mettre à jour') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isPaidCheckbox = document.getElementById('is_paid');
            const paymentDateContainer = document.getElementById('payment_date_container');
            
            isPaidCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    paymentDateContainer.classList.remove('hidden');
                } else {
                    paymentDateContainer.classList.add('hidden');
                }
            });
            
            // Calculate charges amount when revenue or rate changes
            const declaredRevenueInput = document.getElementById('declared_revenue');
            const chargeRateInput = document.getElementById('charge_rate');
            
            function updateChargesAmount() {
                const revenue = parseFloat(declaredRevenueInput.value) || 0;
                const rate = parseFloat(chargeRateInput.value) || 0;
                const chargesAmount = revenue * (rate / 100);
                
                // You could display this somewhere on the form if needed
                console.log('Charges amount:', chargesAmount.toFixed(2));
            }
            
            declaredRevenueInput.addEventListener('input', updateChargesAmount);
            chargeRateInput.addEventListener('input', updateChargesAmount);
        });
    </script>
</x-app-layout>