<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Nouvelle déclaration URSSAF') }}
            </h2>
            <a href="{{ route('urssaf-declarations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('urssaf-declarations.store') }}" class="space-y-6">
                        @csrf

                        <!-- Declaration Frequency Info -->
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-md mb-6">
                            <p class="text-blue-800 dark:text-blue-200">
                                @if($declarationFrequency === 'monthly')
                                    Vous êtes configuré pour des déclarations <strong>mensuelles</strong> selon votre
                                    profil.
                                @elseif($declarationFrequency === 'quarterly')
                                    Vous êtes configuré pour des déclarations <strong>trimestrielles</strong> selonvotre
                                    profil.
                                @elseif($declarationFrequency === 'annually')
                                    Vous êtes configuré pour des déclarations <strong>annuelles</strong> selon votre
                                    profil.
                                @else
                                    Votre fréquence de déclaration n'est pas configurée dans votre profil.
                                @endif
                                <a href="{{ route('profile.edit') }}#urssaf-section" class="underline">Modifier</a>
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Year -->
                            <div>
                                <x-input-label for="year" :value="__('Année')" />
                                <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', $currentYear)" min="2000" max="{{ now()->year + 1 }}" required />
                                <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            </div>

                            <!-- Period Selection (Month/Quarter/Year) -->
                            <div>
                                @if($declarationFrequency === 'monthly')
                                    <x-input-label for="month" :value="__('Mois')"/>
                                    <select id="month" name="month"
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>
                                        <option value="1" {{ old('month', $currentMonth) == 1 ? 'selected' : '' }}>
                                            Janvier
                                        </option>
                                        <option value="2" {{ old('month', $currentMonth) == 2 ? 'selected' : '' }}>
                                            Février
                                        </option>
                                        <option value="3" {{ old('month', $currentMonth) == 3 ? 'selected' : '' }}>
                                            Mars
                                        </option>
                                        <option value="4" {{ old('month', $currentMonth) == 4 ? 'selected' : '' }}>
                                            Avril
                                        </option>
                                        <option value="5" {{ old('month', $currentMonth) == 5 ? 'selected' : '' }}>Mai
                                        </option>
                                        <option value="6" {{ old('month', $currentMonth) == 6 ? 'selected' : '' }}>
                                            Juin
                                        </option>
                                        <option value="7" {{ old('month', $currentMonth) == 7 ? 'selected' : '' }}>
                                            Juillet
                                        </option>
                                        <option value="8" {{ old('month', $currentMonth) == 8 ? 'selected' : '' }}>
                                            Août
                                        </option>
                                        <option value="9" {{ old('month', $currentMonth) == 9 ? 'selected' : '' }}>
                                            Septembre
                                        </option>
                                        <option value="10" {{ old('month', $currentMonth) == 10 ? 'selected' : '' }}>
                                            Octobre
                                        </option>
                                        <option value="11" {{ old('month', $currentMonth) == 11 ? 'selected' : '' }}>
                                            Novembre
                                        </option>
                                        <option value="12" {{ old('month', $currentMonth) == 12 ? 'selected' : '' }}>
                                            Décembre
                                        </option>
                                    </select>
                                @elseif($declarationFrequency === 'quarterly')
                                    <x-input-label for="month" :value="__('Trimestre')"/>
                                    <select id="month" name="month"
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>
                                        <option value="1" {{ old('month', $currentQuarter) == 1 ? 'selected' : '' }}>Q1
                                            (Janvier - Mars)
                                        </option>
                                        <option value="2" {{ old('month', $currentQuarter) == 2 ? 'selected' : '' }}>Q2
                                            (Avril - Juin)
                                        </option>
                                        <option value="3" {{ old('month', $currentQuarter) == 3 ? 'selected' : '' }}>Q3
                                            (Juillet - Septembre)
                                        </option>
                                        <option value="4" {{ old('month', $currentQuarter) == 4 ? 'selected' : '' }}>Q4
                                            (Octobre - Décembre)
                                        </option>
                                    </select>
                                @elseif($declarationFrequency === 'annually')
                                    <x-input-label for="month" :value="__('Période')"/>
                                    <select id="month" name="month"
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>
                                        <option value="1" selected>Année complète</option>
                                    </select>
                                @else
                                    <x-input-label for="month" :value="__('Mois')"/>
                                    <select id="month" name="month"
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>
                                        <option value="1" {{ old('month', $currentMonth) == 1 ? 'selected' : '' }}>
                                            Janvier
                                        </option>
                                        <option value="2" {{ old('month', $currentMonth) == 2 ? 'selected' : '' }}>
                                            Février
                                        </option>
                                        <option value="3" {{ old('month', $currentMonth) == 3 ? 'selected' : '' }}>
                                            Mars
                                        </option>
                                        <option value="4" {{ old('month', $currentMonth) == 4 ? 'selected' : '' }}>
                                            Avril
                                        </option>
                                        <option value="5" {{ old('month', $currentMonth) == 5 ? 'selected' : '' }}>Mai
                                        </option>
                                        <option value="6" {{ old('month', $currentMonth) == 6 ? 'selected' : '' }}>
                                            Juin
                                        </option>
                                        <option value="7" {{ old('month', $currentMonth) == 7 ? 'selected' : '' }}>
                                            Juillet
                                        </option>
                                        <option value="8" {{ old('month', $currentMonth) == 8 ? 'selected' : '' }}>
                                            Août
                                        </option>
                                        <option value="9" {{ old('month', $currentMonth) == 9 ? 'selected' : '' }}>
                                            Septembre
                                        </option>
                                        <option value="10" {{ old('month', $currentMonth) == 10 ? 'selected' : '' }}>
                                            Octobre
                                        </option>
                                        <option value="11" {{ old('month', $currentMonth) == 11 ? 'selected' : '' }}>
                                            Novembre
                                        </option>
                                        <option value="12" {{ old('month', $currentMonth) == 12 ? 'selected' : '' }}>
                                            Décembre
                                        </option>
                                    </select>
                                @endif
                                <x-input-error :messages="$errors->get('month')" class="mt-2" />
                            </div>

                            <!-- Declared Revenue -->
                            <div>
                                <x-input-label for="declared_revenue" :value="__('Revenu déclaré (€)')" />
                                <x-text-input id="declared_revenue" class="block mt-1 w-full" type="number"
                                              name="declared_revenue" :value="old('declared_revenue', $revenue)"
                                              step="0.01" min="0" required/>
                                <x-input-error :messages="$errors->get('declared_revenue')" class="mt-2" />
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    @if($declarationFrequency === 'monthly')
                                        Montant calculé à partir des factures payées du mois. Vous pouvez le modifier si
                                        nécessaire.
                                    @elseif($declarationFrequency === 'quarterly')
                                        Montant calculé à partir des factures payées du trimestre. Vous pouvez le
                                        modifier si nécessaire.
                                    @elseif($declarationFrequency === 'annually')
                                        Montant calculé à partir des factures payées de l'année. Vous pouvez le modifier
                                        si nécessaire.
                                    @else
                                        Montant calculé à partir des factures payées. Vous pouvez le modifier si
                                        nécessaire.
                                    @endif
                                </p>
                            </div>

                            <!-- Charge Rate -->
                            <div>
                                <x-input-label for="charge_rate" :value="__('Taux de cotisation (%)')" />
                                <x-text-input id="charge_rate" class="block mt-1 w-full" type="number" name="charge_rate" :value="old('charge_rate', $defaultChargeRate)" step="0.01" min="0" max="100" required />
                                <x-input-error :messages="$errors->get('charge_rate')" class="mt-2" />
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Taux récupéré depuis votre profil. Vous pouvez le modifier si nécessaire.
                                </p>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="mt-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_paid" name="is_paid" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('is_paid') ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_paid" class="font-medium text-gray-700 dark:text-gray-300">Déjà payée</label>
                                    <p class="text-gray-500 dark:text-gray-400">Cochez cette case si vous avez déjà payé cette déclaration</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Date (visible only when is_paid is checked) -->
                        <div id="payment_date_container" class="mt-4 {{ old('is_paid') ? '' : 'hidden' }}">
                            <x-input-label for="payment_date" :value="__('Date de paiement')" />
                            <x-text-input id="payment_date" class="block mt-1 w-full" type="date" name="payment_date" :value="old('payment_date', now()->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Créer la déclaration') }}
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
          const yearInput = document.getElementById('year');
          const monthInput = document.getElementById('month');
          const declaredRevenueInput = document.getElementById('declared_revenue');
          const chargeRateInput = document.getElementById('charge_rate');

            isPaidCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    paymentDateContainer.classList.remove('hidden');
                } else {
                    paymentDateContainer.classList.add('hidden');
                }
            });

            // Calculate charges amount when revenue or rate changes
            function updateChargesAmount() {
                const revenue = parseFloat(declaredRevenueInput.value) || 0;
                const rate = parseFloat(chargeRateInput.value) || 0;
                const chargesAmount = revenue * (rate / 100);

                // You could display this somewhere on the form if needed
                console.log('Charges amount:', chargesAmount.toFixed(2));
            }

          // Update revenue when period changes
          function updateRevenue() {
            const year = yearInput.value;
            const month = monthInput.value;

            if (!year || !month) return;

            // Show loading indicator
            declaredRevenueInput.setAttribute('disabled', 'disabled');
            declaredRevenueInput.value = 'Chargement...';

            // Make AJAX request to calculate revenue
            fetch('{{ route('urssaf-declarations.calculate-revenue') }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({
                year: year,
                month: month
              })
            })
              .then(response => response.json())
              .then(data => {
                // Update revenue field
                declaredRevenueInput.value = data.revenue;
                declaredRevenueInput.removeAttribute('disabled');

                // Update charges amount
                updateChargesAmount();
              })
              .catch(error => {
                console.error('Error:', error);
                declaredRevenueInput.value = '';
                declaredRevenueInput.removeAttribute('disabled');
              });
          }

          // Add event listeners
            declaredRevenueInput.addEventListener('input', updateChargesAmount);
            chargeRateInput.addEventListener('input', updateChargesAmount);
          yearInput.addEventListener('change', updateRevenue);
          monthInput.addEventListener('change', updateRevenue);
        });
    </script>
</x-app-layout>
