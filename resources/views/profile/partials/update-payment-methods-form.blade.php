<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Moyens de paiement') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Gérez vos moyens de paiement.") }}
        </p>
    </header>

    <!-- Bank Accounts Section -->
    <div class="mt-6">
        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">{{ __('Comptes bancaires (RIB)') }}</h3>

        @if($user->bankAccounts->count() > 0)
            <div class="mt-4 space-y-4">
                @foreach($user->bankAccounts as $account)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md flex justify-between items-center">
                        <div>
                            <p class="font-medium text-white dark:text-white">{{ $account->account_name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $account->account_holder ?? __('Non spécifié') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $account->bank_name }}
                                - {{ $account->iban }}</p>
                            @if($account->is_default)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                    {{ __('Par défaut') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" onclick="openEditBankAccountModal({{ $account->id }})"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-2">
                                {{ __('Modifier') }}
                            </button>
                            @if(!$account->is_default)
                                <form method="post" action="{{ route('bank-accounts.set-default', $account) }}"
                                      class="inline mr-2">
                                    @csrf
                                    @method('patch')
                                    <input type="hidden" name="active_tab" value="{{ $activeTab ?? 'payment-tab' }}">
                                    <button type="submit"
                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                        {{ __('Définir comme compte par défaut') }}
                                    </button>
                                </form>
                            @endif
                            <form method="post" action="{{ route('bank-accounts.destroy', $account) }}" class="inline">
                                @csrf
                                @method('delete')
                                <input type="hidden" name="active_tab" value="{{ $activeTab ?? 'payment-tab' }}">
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        onclick="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce compte bancaire?') }}')">
                                    {{ __('Supprimer') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Aucun compte bancaire enregistré.') }}</p>
        @endif

        <button type="button" onclick="openAddBankAccountModal()"
                class="mt-4 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
            {{ __('Ajouter un compte bancaire') }}
        </button>
    </div>

    <!-- Payment Methods Section -->
    <div class="mt-8">
        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">{{ __('Autres moyens de paiement') }}</h3>

        @if($user->paymentMethods->count() > 0)
            <div class="mt-4 space-y-4">
                @foreach($user->paymentMethods as $method)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md flex justify-between items-center">
                        <div>
                            <p class="font-medium">
                                @if($method->type === 'stripe')
                                    {{ __('Carte bancaire (Stripe)') }}
                                @elseif($method->type === 'paypal')
                                    {{ __('PayPal') }}
                                @else
                                    {{ __('Autre') }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $method->details }}</p>
                            @if($method->is_default)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                    {{ __('Par défaut') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <form method="post" action="{{ route('payment-methods.destroy', $method) }}" class="inline">
                                @csrf
                                @method('delete')
                                <input type="hidden" name="active_tab" value="{{ $activeTab ?? 'payment-tab' }}">
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        onclick="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce moyen de paiement?') }}')">
                                    {{ __('Supprimer') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Aucun moyen de paiement enregistré.') }}</p>
        @endif

        <div class="mt-4 space-y-2">
            <button type="button"
                    onclick="window.location.href='{{ route('payment-methods.create', ['type' => 'stripe']) }}'"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Ajouter une carte bancaire (Stripe)') }}
            </button>

            <button type="button"
                    onclick="window.location.href='{{ route('payment-methods.create', ['type' => 'paypal']) }}'"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Ajouter un compte PayPal') }}
            </button>

            <button type="button"
                    onclick="window.location.href='{{ route('payment-methods.create', ['type' => 'other']) }}'"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Ajouter un autre moyen de paiement') }}
            </button>
        </div>
    </div>

    <!-- Bank Account Modal (Hidden by default) -->
    <div id="bankAccountModal"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
            <h3 id="bankAccountModalTitle"
                class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Ajouter un compte bancaire') }}</h3>

            <form id="bankAccountForm" method="post" action="{{ route('bank-accounts.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" id="bankAccountId" name="id" value="">
                <input type="hidden" id="bankAccountMethod" name="_method" value="post">
                <input type="hidden" name="active_tab" value="{{ $activeTab ?? 'payment-tab' }}">

                <div>
                    <x-input-label for="account_name" :value="__('Libellé du compte')"/>
                    <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full" required/>
                    <x-input-error class="mt-2" :messages="$errors->get('account_name')"/>
                </div>

                <div>
                    <x-input-label for="account_holder" :value="__('Titulaire du compte')"/>
                    <x-text-input id="account_holder" name="account_holder" type="text" class="mt-1 block w-full"
                                  required/>
                    <x-input-error class="mt-2" :messages="$errors->get('account_holder')"/>
                </div>

                <div>
                    <x-input-label for="bank_name" :value="__('Nom de la banque')"/>
                    <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" required/>
                    <x-input-error class="mt-2" :messages="$errors->get('bank_name')"/>
                </div>


                <div>
                    <x-input-label for="iban" :value="__('IBAN')"/>
                    <x-text-input id="iban" name="iban" type="text" class="mt-1 block w-full" required/>
                    <x-input-error class="mt-2" :messages="$errors->get('iban')"/>
                </div>

                <div>
                    <x-input-label for="bic" :value="__('BIC')"/>
                    <x-text-input id="bic" name="bic" type="text" class="mt-1 block w-full" required/>
                    <x-input-error class="mt-2" :messages="$errors->get('bic')"/>
                </div>

                <div class="flex items-center">
                    <input id="is_default" name="is_default" type="checkbox"
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="is_default"
                           class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ __('Définir comme compte par défaut') }}</label>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBankAccountModal()"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Annuler') }}
                    </button>
                    <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
      function openAddBankAccountModal() {
        // Reset form
        document.getElementById('bankAccountForm').reset();
        document.getElementById('bankAccountId').value = '';
        document.getElementById('bankAccountMethod').value = 'post';
        document.getElementById('bankAccountForm').action = "{{ route('bank-accounts.store') }}";
        document.getElementById('bankAccountModalTitle').textContent = "{{ __('Ajouter un compte bancaire') }}";

        // Show modal
        document.getElementById('bankAccountModal').classList.remove('hidden');
      }

      function openEditBankAccountModal(id) {
        // Fetch bank account data and populate form
        fetch("{{ route('bank-accounts.edit', ':bankAccount') }}".replace(':bankAccount', id))
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            if (data.error) {
              throw new Error(data.error);
            }

            // Populate form with bank account data
            document.getElementById('account_name').value = data.account_name;
            document.getElementById('account_holder').value = data.account_holder;
            document.getElementById('bank_name').value = data.bank_name;
            document.getElementById('iban').value = data.iban;
            document.getElementById('bic').value = data.bic;
            document.getElementById('is_default').checked = data.is_default;

            // Set form action and method for update
            document.getElementById('bankAccountId').value = data.id;
            document.getElementById('bankAccountMethod').value = 'patch';
            document.getElementById('bankAccountForm').action = "{{ route('bank-accounts.update', ':bankAccount') }}".replace(':bankAccount', data.id);
            document.getElementById('bankAccountModalTitle').textContent = "{{ __('Modifier le compte bancaire') }}";

            // Show modal
            document.getElementById('bankAccountModal').classList.remove('hidden');
          })
          .catch(error => {
            console.error('Error fetching bank account data:', error);
            alert('Une erreur est survenue lors de la récupération des données du compte bancaire: ' + error.message);
          });
      }

      function closeBankAccountModal() {
        document.getElementById('bankAccountModal').classList.add('hidden');
      }
    </script>
</section>
