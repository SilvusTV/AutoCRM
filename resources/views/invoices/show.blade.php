<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Facture {{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
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

            <!-- Invoice Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Informations de la facture</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Numéro</p>
                            <p class="font-medium">{{ $invoice->invoice_number }}</p>
                        </div>

                        @if($invoice->client)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Client</p>
                            <p class="font-medium">
                                <a href="{{ route('clients.show', $invoice->client->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    {{ $invoice->client->name }}
                                </a>
                            </p>
                        </div>
                        @endif

                        @if($invoice->company)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Entreprise</p>
                                <p class="font-medium">
                                    <a href="{{ route('companies.show', $invoice->company->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        {{ $invoice->company->name }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Projet</p>
                            <p class="font-medium">
                                <a href="{{ route('projects.show', $invoice->project->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    {{ $invoice->project->name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Statut</p>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($invoice->status == 'brouillon') bg-gray-100 text-gray-800 
                                    @elseif($invoice->status == 'envoyee') bg-blue-100 text-blue-800 
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ $invoice->status }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Date d'émission</p>
                            <p class="font-medium">{{ $invoice->issue_date->format('d/m/Y') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Date d'échéance</p>
                            <p class="font-medium">{{ $invoice->due_date->format('d/m/Y') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Date de paiement</p>
                            <p class="font-medium">{{ $invoice->payment_date ? $invoice->payment_date->format('d/m/Y') : 'Non payée' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Montant HT</p>
                            <p class="font-medium">{{ number_format($invoice->total_ht, 2, ',', ' ') }} €</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Taux de TVA</p>
                            <p class="font-medium">{{ $invoice->tva_rate }} %</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Montant TTC</p>
                            <p class="font-medium">{{ number_format($invoice->total_ttc, 2, ',', ' ') }} €</p>
                        </div>

                        @if($invoice->notes)
                        <div class="md:col-span-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Notes</p>
                            <p class="font-medium">{{ $invoice->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <a href="{{ route('invoices.pdf', $invoice->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Télécharger PDF') }}
                        </a>

                        @if(!$invoice->isValidated())
                            <!-- Validation Button -->
                            <form action="{{ route('invoices.validate', $invoice->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir valider cette facture? Une fois validée, elle ne pourra plus être modifiée.');">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Valider') }}
                                </button>
                            </form>

                            <!-- Edit Button -->
                            <a href="{{ route('invoices.edit', $invoice->id) }}"
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Modifier') }}
                            </a>

                            <!-- Delete Button -->
                            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Supprimer') }}
                                </button>
                            </form>
                        @else
                            <!-- Validated Badge -->
                            <span class="inline-flex items-center px-4 py-2 bg-green-100 border border-green-200 rounded-md font-semibold text-xs text-green-800 uppercase">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                {{ __('Document validé') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>


            <!-- Invoice Lines -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Lignes de facture</h3>

                    @if ($invoice->invoiceLines->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantité</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prix unitaire</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total HT</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach ($invoice->invoiceLines as $line)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $line->description }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $line->quantity }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($line->unit_price, 2, ',', ' ') }} €</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($line->total_ht, 2, ',', ' ') }} €</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if(!$invoice->isValidated())
                                                    <div class="flex space-x-2">
                                                        <button type="button"
                                                                class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 edit-line"
                                                                data-id="{{ $line->id }}"
                                                                data-description="{{ $line->description }}"
                                                                data-quantity="{{ $line->quantity }}"
                                                                data-unit-price="{{ $line->unit_price }}"
                                                                data-item-type="{{ $line->item_type ?? 'service' }}"
                                                                data-is-expense="{{ $line->is_expense ? 'true' : 'false' }}"
                                                                data-discount-percent="{{ $line->discount_percent ?? 0 }}"
                                                                data-tva-rate="{{ $line->tva_rate ?? $invoice->tva_rate }}">
                                                            Modifier
                                                        </button>
                                                        <form action="{{ route('invoice-lines.destroy', $line->id) }}"
                                                              method="POST" class="inline"
                                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                                Supprimer
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">Document validé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right font-medium">Total HT:</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($invoice->total_ht, 2, ',', ' ') }} €</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right font-medium">TVA ({{ $invoice->tva_rate }}%):</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($invoice->total_ttc - $invoice->total_ht, 2, ',', ' ') }} €</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right font-medium">Total TTC:</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($invoice->total_ttc, 2, ',', ' ') }} €</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-400">Aucune ligne de facture trouvée.</p>
                        </div>
                    @endif
                </div>
            </div>

                @if(!$invoice->isValidated())
            <!-- Add Invoice Line Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Ajouter une ligne de facture</h3>

                    <form method="POST" action="{{ route('invoice-lines.store') }}" class="space-y-6" id="add-line-form">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Item Type -->
                            <div>
                                <x-input-label for="item_type" :value="__('Type')"/>
                                <select id="item_type" name="item_type"
                                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                    <option value="service" {{ old('item_type') == 'service' ? 'selected' : '' }}>
                                        Service
                                    </option>
                                    <option value="product" {{ old('item_type') == 'product' ? 'selected' : '' }}>
                                        Produit
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('item_type')" class="mt-2"/>
                            </div>

                            <!-- Is Expense -->
                            <div class="flex items-center mt-8">
                                <label for="is_expense" class="inline-flex items-center">
                                    <input id="is_expense" type="checkbox" name="is_expense"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                           value="1" {{ old('is_expense') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Débours') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('is_expense')" class="mt-2"/>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')" required />
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Quantity -->
                            <div>
                                <x-input-label for="quantity" :value="__('Quantité')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" :value="old('quantity', 1)" step="0.01" min="0.01" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>

                            <!-- Unit Price -->
                            <div>
                                <x-input-label for="unit_price" :value="__('Prix unitaire (€)')" />
                                <x-text-input id="unit_price" class="block mt-1 w-full" type="number" name="unit_price" :value="old('unit_price', 0)" step="0.01" min="0" required />
                                <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Discount Percent -->
                            <div>
                                <x-input-label for="discount_percent" :value="__('Réduction (%)')"/>
                                <x-text-input id="discount_percent" class="block mt-1 w-full" type="number"
                                              name="discount_percent" :value="old('discount_percent', 0)" step="0.01"
                                              min="0" max="100"/>
                                <x-input-error :messages="$errors->get('discount_percent')" class="mt-2"/>
                            </div>

                            <!-- TVA Rate -->
                            <div>
                                <x-input-label for="tva_rate" :value="__('TVA (%) - optionnel')"/>
                                <x-text-input id="tva_rate" class="block mt-1 w-full" type="number" name="tva_rate"
                                              :value="old('tva_rate')" step="0.01" min="0" max="100"
                                              placeholder="Taux global par défaut"/>
                                <p class="mt-1 text-xs text-gray-500">Laissez vide pour utiliser le taux global
                                    ({{ $invoice->tva_rate }}%)</p>
                                <x-input-error :messages="$errors->get('tva_rate')" class="mt-2"/>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Ajouter') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
                @endif

            <!-- Edit Invoice Line Modal (hidden by default) -->
            <div id="edit-line-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden" style="z-index: 50;">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Modifier la ligne de facture</h3>

                    <form method="POST" action="" id="edit-line-form" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Item Type -->
                        <div class="mb-4">
                            <x-input-label for="edit_item_type" :value="__('Type')"/>
                            <select id="edit_item_type" name="item_type"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="service">Service</option>
                                <option value="product">Produit</option>
                            </select>
                        </div>

                        <!-- Is Expense -->
                        <div class="mb-4">
                            <label for="edit_is_expense" class="inline-flex items-center">
                                <input id="edit_is_expense" type="checkbox" name="is_expense"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                       value="1">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Débours') }}</span>
                            </label>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="edit_description" :value="__('Description')" />
                            <x-text-input id="edit_description" class="block mt-1 w-full" type="text" name="description" required />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Quantity -->
                            <div>
                                <x-input-label for="edit_quantity" :value="__('Quantité')" />
                                <x-text-input id="edit_quantity" class="block mt-1 w-full" type="number" name="quantity" step="0.01" min="0.01" required />
                            </div>

                            <!-- Unit Price -->
                            <div>
                                <x-input-label for="edit_unit_price" :value="__('Prix unitaire (€)')" />
                                <x-text-input id="edit_unit_price" class="block mt-1 w-full" type="number" name="unit_price" step="0.01" min="0" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Discount Percent -->
                            <div>
                                <x-input-label for="edit_discount_percent" :value="__('Réduction (%)')"/>
                                <x-text-input id="edit_discount_percent" class="block mt-1 w-full" type="number"
                                              name="discount_percent" step="0.01" min="0" max="100" value="0"/>
                            </div>

                            <!-- TVA Rate -->
                            <div>
                                <x-input-label for="edit_tva_rate" :value="__('TVA (%) - optionnel')"/>
                                <x-text-input id="edit_tva_rate" class="block mt-1 w-full" type="number" name="tva_rate"
                                              step="0.01" min="0" max="100" placeholder="Taux global par défaut"/>
                                <p class="mt-1 text-xs text-gray-500">Laissez vide pour utiliser le taux global</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 space-x-2">
                            <button type="button" id="close-modal" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </button>
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
            const editButtons = document.querySelectorAll('.edit-line');
            const editModal = document.getElementById('edit-line-modal');
            const editForm = document.getElementById('edit-line-form');
            const closeModalButton = document.getElementById('close-modal');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const description = this.getAttribute('data-description');
                    const quantity = this.getAttribute('data-quantity');
                    const unitPrice = this.getAttribute('data-unit-price');
                  const itemType = this.getAttribute('data-item-type');
                  const isExpense = this.getAttribute('data-is-expense');
                  const discountPercent = this.getAttribute('data-discount-percent');
                  const tvaRate = this.getAttribute('data-tva-rate');

                    editForm.action = `/invoice-lines/${id}`;
                    document.getElementById('edit_description').value = description;
                    document.getElementById('edit_quantity').value = quantity;
                    document.getElementById('edit_unit_price').value = unitPrice;

                  // Set item type
                  const itemTypeSelect = document.getElementById('edit_item_type');
                  for (let i = 0; i < itemTypeSelect.options.length; i++) {
                    if (itemTypeSelect.options[i].value === itemType) {
                      itemTypeSelect.selectedIndex = i;
                      break;
                    }
                  }

                  // Set is_expense checkbox
                  document.getElementById('edit_is_expense').checked = isExpense === 'true';

                  // Set discount percent
                  document.getElementById('edit_discount_percent').value = discountPercent;

                  // Set TVA rate if provided
                  if (tvaRate) {
                    document.getElementById('edit_tva_rate').value = tvaRate;
                  } else {
                    document.getElementById('edit_tva_rate').value = '';
                  }

                    editModal.classList.remove('hidden');
                });
            });

            closeModalButton.addEventListener('click', function() {
                editModal.classList.add('hidden');
            });

            // Close modal when clicking outside
            editModal.addEventListener('click', function(e) {
                if (e.target === editModal) {
                    editModal.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
