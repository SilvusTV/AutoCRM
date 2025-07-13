<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ajouter un moyen de paiement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-6">
                    <form method="post" action="{{ route('payment-methods.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                @if($type === 'stripe')
                                    {{ __('Ajouter une carte bancaire (Stripe)') }}
                                @elseif($type === 'paypal')
                                    {{ __('Ajouter un compte PayPal') }}
                                @else
                                    {{ __('Ajouter un autre moyen de paiement') }}
                                @endif
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Veuillez remplir les informations ci-dessous.') }}
                            </p>
                        </div>

                        @if($type === 'stripe')
                            <div>
                                <x-input-label for="identifier" :value="__('Numéro de carte')"/>
                                <x-text-input id="identifier" name="identifier" type="text" class="mt-1 block w-full"
                                              placeholder="•••• •••• •••• ••••" required/>
                                <x-input-error class="mt-2" :messages="$errors->get('identifier')"/>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="expiry" :value="__('Date d\'expiration')"/>
                                    <x-text-input id="expiry" name="expiry" type="text" class="mt-1 block w-full"
                                                  placeholder="MM/YY" required/>
                                </div>
                                <div>
                                    <x-input-label for="cvc" :value="__('CVC')"/>
                                    <x-text-input id="cvc" name="cvc" type="text" class="mt-1 block w-full"
                                                  placeholder="123" required/>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="details" :value="__('Nom sur la carte')"/>
                                <x-text-input id="details" name="details" type="text" class="mt-1 block w-full"
                                              required/>
                                <x-input-error class="mt-2" :messages="$errors->get('details')"/>
                            </div>
                        @elseif($type === 'paypal')
                            <div>
                                <x-input-label for="identifier" :value="__('Email PayPal')"/>
                                <x-text-input id="identifier" name="identifier" type="email" class="mt-1 block w-full"
                                              required/>
                                <x-input-error class="mt-2" :messages="$errors->get('identifier')"/>
                            </div>

                            <div>
                                <x-input-label for="details" :value="__('Nom du compte')"/>
                                <x-text-input id="details" name="details" type="text" class="mt-1 block w-full"/>
                                <x-input-error class="mt-2" :messages="$errors->get('details')"/>
                            </div>
                        @else
                            <div>
                                <x-input-label for="identifier" :value="__('Identifiant du moyen de paiement')"/>
                                <x-text-input id="identifier" name="identifier" type="text" class="mt-1 block w-full"/>
                                <x-input-error class="mt-2" :messages="$errors->get('identifier')"/>
                            </div>

                            <div>
                                <x-input-label for="details" :value="__('Description')"/>
                                <x-text-input id="details" name="details" type="text" class="mt-1 block w-full"/>
                                <x-input-error class="mt-2" :messages="$errors->get('details')"/>
                            </div>
                        @endif

                        <div class="flex items-center">
                            <input id="is_default" name="is_default" type="checkbox"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="is_default"
                                   class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ __('Définir comme moyen de paiement par défaut') }}</label>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('profile.edit') }}"
                               class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>
                            <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>