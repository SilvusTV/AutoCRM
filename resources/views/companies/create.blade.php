<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Nouvelle entreprise') }}
            </h2>
            <a href="{{ route('companies.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('companies.store') }}" class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nom')"/>
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                          :value="old('name')" required autofocus/>
                            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email (optionnel)')"/>
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                          :value="old('email')"/>
                            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-input-label for="phone" :value="__('Téléphone (optionnel)')"/>
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                                          :value="old('phone')"/>
                            <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
                        </div>

                        <!-- Address -->
                        <div>
                            <x-input-label for="address" :value="__('Adresse (optionnel)')"/>
                            <textarea id="address" name="address"
                                      class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">{{ old('address') }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2"/>
                        </div>

                        <!-- SIRET -->
                        <div>
                            <x-input-label for="siret" :value="__('Numéro SIRET (optionnel)')"/>
                            <x-text-input id="siret" class="block mt-1 w-full" type="text" name="siret"
                                          :value="old('siret')"/>
                            <x-input-error :messages="$errors->get('siret')" class="mt-2"/>
                        </div>

                        <!-- TVA Number -->
                        <div>
                            <x-input-label for="tva_number" :value="__('Numéro TVA (optionnel)')"/>
                            <x-text-input id="tva_number" class="block mt-1 w-full" type="text" name="tva_number"
                                          :value="old('tva_number')"/>
                            <x-input-error :messages="$errors->get('tva_number')" class="mt-2"/>
                        </div>

                        <!-- NAF Code -->
                        <div>
                            <x-input-label for="naf_code" :value="__('Code NAF')"/>
                            <x-text-input id="naf_code" class="block mt-1 w-full" type="text" name="naf_code"
                                          :value="old('naf_code', '')" placeholder="Entrez le code NAF"/>
                            <x-input-error :messages="$errors->get('naf_code')" class="mt-2"/>
                        </div>

                        <!-- Country -->
                        <div>
                            <x-input-label for="country" :value="__('Pays (optionnel)')"/>
                            <x-text-input id="country" class="block mt-1 w-full" type="text" name="country"
                                          :value="old('country')"/>
                            <x-input-error :messages="$errors->get('country')" class="mt-2"/>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Créer l\'entreprise') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
