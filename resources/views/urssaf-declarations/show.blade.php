<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Déclaration URSSAF - {{ \Carbon\Carbon::createFromDate($declaration->year, $declaration->month, 1)->format('F Y') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('urssaf-declarations.edit', $declaration->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('urssaf-declarations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Informations de la déclaration</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Période</p>
                            <p class="font-medium">{{ \Carbon\Carbon::createFromDate($declaration->year, $declaration->month, 1)->format('F Y') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Revenu déclaré</p>
                            <p class="font-medium">{{ number_format($declaration->declared_revenue, 2, ',', ' ') }} €</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Taux de cotisation</p>
                            <p class="font-medium">{{ $declaration->charge_rate }} %</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Montant des charges</p>
                            <p class="font-medium">{{ number_format($declaration->charges_amount, 2, ',', ' ') }} €</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Statut</p>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $declaration->is_paid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $declaration->is_paid ? 'Payée' : 'À payer' }}
                                </span>
                            </p>
                        </div>

                        @if($declaration->is_paid && $declaration->payment_date)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Date de paiement</p>
                            <p class="font-medium">{{ $declaration->payment_date->format('d/m/Y') }}</p>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Date de création</p>
                            <p class="font-medium">{{ $declaration->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Dernière mise à jour</p>
                            <p class="font-medium">{{ $declaration->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <form action="{{ route('urssaf-declarations.destroy', $declaration->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette déclaration?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Supprimer') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Informations complémentaires</h3>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="font-medium mb-2">Comment payer cette déclaration ?</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            Pour payer cette déclaration, connectez-vous à votre espace personnel sur le site de l'URSSAF :
                            <a href="https://www.autoentrepreneur.urssaf.fr/" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                www.autoentrepreneur.urssaf.fr
                            </a>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Une fois le paiement effectué, n'oubliez pas de mettre à jour le statut de cette déclaration.
                        </p>
                    </div>

                    @if(!$declaration->is_paid)
                    <div class="mt-4">
                        <form action="{{ route('urssaf-declarations.update', $declaration->id) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <!-- Hidden fields to maintain the current values -->
                            <input type="hidden" name="year" value="{{ $declaration->year }}">
                            <input type="hidden" name="month" value="{{ $declaration->month }}">
                            <input type="hidden" name="declared_revenue" value="{{ $declaration->declared_revenue }}">
                            <input type="hidden" name="charge_rate" value="{{ $declaration->charge_rate }}">
                            
                            <!-- Mark as paid -->
                            <div class="flex items-center">
                                <input type="checkbox" id="is_paid" name="is_paid" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" checked>
                                <label for="is_paid" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Marquer comme payée
                                </label>
                            </div>
                            
                            <!-- Payment date -->
                            <div>
                                <x-input-label for="payment_date" :value="__('Date de paiement')" />
                                <x-text-input id="payment_date" class="block mt-1 w-full" type="date" name="payment_date" :value="now()->format('Y-m-d')" required />
                                <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                            </div>
                            
                            <div class="flex justify-end">
                                <x-primary-button>
                                    {{ __('Enregistrer le paiement') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>