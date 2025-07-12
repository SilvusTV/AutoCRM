<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $invoice->isQuote() ? __('Ajouter des lignes au devis') : __('Ajouter des lignes à la facture') }}
                : {{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('invoices.preview', $invoice->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Prévisualiser') }}
                </a>
                <a href="{{ route('invoices.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Invoice Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-white">{{ __('Informations de la facture') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-white"><strong>Numéro:</strong> {{ $invoice->invoice_number }}</p>
                        <p class="text-white"><strong>Type:</strong> {{ $invoice->isQuote() ? 'Devis' : 'Facture' }}</p>
                        <p class="text-white"><strong>Statut:</strong> {{ ucfirst($invoice->status) }}</p>
                        <p class="text-white"><strong>Date
                                d'émission:</strong> {{ $invoice->issue_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-white">
                            <strong>Destinataire:</strong> {{ $invoice->client->name ?? $invoice->company->name ?? 'N/A' }}
                        </p>
                        <p class="text-white"><strong>Projet:</strong> {{ $invoice->project->name ?? 'N/A' }}</p>
                        <p class="text-white"><strong>TVA:</strong> {{ $invoice->tva_rate }}%</p>
                        <p class="text-white"><strong>Date
                                d'échéance:</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Current Invoice Lines -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 text-white">{{ __('Lignes existantes') }}</h3>

                @if($invoice->invoiceLines->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Description
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Quantité
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Prix unitaire
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Réduction
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Total HT
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($invoice->invoiceLines as $line)
                                <tr>
                                    <td class="px-6 py-4 text-white">
                                        {{ $line->item_type === 'service' ? 'Service' : 'Produit' }}
                                        @if($line->is_expense)
                                            (Débours)
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-white">{{ $line->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-white">{{ $line->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-white">{{ number_format($line->unit_price, 2, ',', ' ') }}
                                        €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-white">{{ $line->discount_percent > 0 ? $line->discount_percent . '%' : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-white">{{ number_format($line->total_ht, 2, ',', ' ') }}
                                        €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form action="{{ route('invoice-lines.destroy', $line->id) }}" method="POST"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center py-4 text-gray-500 dark:text-gray-400">Aucune ligne de facture ajoutée.</p>
                @endif

                <!-- Totals Recap -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mt-6">
                    <div class="flex flex-col items-end">
                        <div class="flex gap-4 mb-2">
                            <div class="text-white font-medium">Total HT:</div>
                            <div class="text-white font-medium w-32 text-right">{{ number_format($invoice->total_ht, 2, ',', ' ') }}
                                €
                            </div>
                        </div>

                        <div class="flex gap-4 mb-2">
                            <div class="text-white font-medium">TVA ({{ $invoice->tva_rate }}%):</div>
                            <div class="text-white font-medium w-32 text-right">{{ number_format($invoice->total_ttc - $invoice->total_ht, 2, ',', ' ') }}
                                €
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="text-white font-medium">Total TTC:</div>
                            <div class="text-white font-medium w-32 text-right">{{ number_format($invoice->total_ttc, 2, ',', ' ') }}
                                €
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Invoice Line Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-white">{{ __('Ajouter une ligne') }}</h3>
                    <p class="text-sm text-gray-400">Vous pouvez ajouter plusieurs lignes consécutivement</p>
                </div>

                <form method="POST" action="{{ route('invoice-lines.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Item Type -->
                        <div>
                            <x-input-label for="item_type" :value="__('Type')" class="text-white"/>
                            <select id="item_type" name="item_type"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="service">Service</option>
                                <option value="product">Produit</option>
                            </select>
                        </div>

                        <!-- Is Expense -->
                        <div class="flex items-center mt-8">
                            <label for="is_expense" class="inline-flex items-center">
                                <input id="is_expense" name="is_expense" type="checkbox"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                       value="1">
                                <span class="ml-2 text-sm text-white">{{ __('Débours') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                        <!-- Quantity -->
                        <div>
                            <x-input-label for="quantity" :value="__('Quantité')" class="text-white"/>
                            <x-text-input id="quantity" name="quantity" class="block mt-1 w-full" type="number"
                                          value="1" step="0.01" min="0.01" required/>
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2"/>
                        </div>

                        <!-- Unit Price -->
                        <div>
                            <x-input-label for="unit_price" :value="__('Prix unitaire (€)')" class="text-white"/>
                            <x-text-input id="unit_price" name="unit_price" class="block mt-1 w-full" type="number"
                                          value="0" step="0.01" min="0" required/>
                            <x-input-error :messages="$errors->get('unit_price')" class="mt-2"/>
                        </div>

                        <!-- Line TVA Rate -->
                        <div>
                            <x-input-label for="tva_rate" :value="__('TVA (%)')" class="text-white"/>
                            <x-text-input id="tva_rate" name="tva_rate" class="block mt-1 w-full" type="number"
                                          step="0.01" min="0" max="100" placeholder="Taux global par défaut"/>
                            <x-input-error :messages="$errors->get('tva_rate')" class="mt-2"/>
                        </div>

                        <!-- Discount Percent -->
                        <div>
                            <x-input-label for="discount_percent" :value="__('Réduction (%)')" class="text-white"/>
                            <x-text-input id="discount_percent" name="discount_percent" class="block mt-1 w-full"
                                          type="number" value="0" step="0.01" min="0" max="100"/>
                            <x-input-error :messages="$errors->get('discount_percent')" class="mt-2"/>
                        </div>

                        <!-- Total TTC (calculated field, read-only) -->
                        <div>
                            <x-input-label for="total_ttc_display" :value="__('Total TTC')" class="text-white"/>
                            <x-text-input id="total_ttc_display"
                                          class="block mt-1 w-full bg-gray-100 dark:bg-gray-700 text-white" type="text"
                                          readonly/>
                        </div>
                    </div>

                    <!-- Description (textarea with 3 rows) -->
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Description')" class="text-white"/>
                        <textarea id="description" name="description"
                                  class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                  rows="3" required></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>
                            {{ __('Ajouter cette ligne et continuer') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Finalize Invoice -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-white">{{ __('Finaliser') }}</h3>
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('invoices.edit', $invoice->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Modifier les détails de la facture') }}
                    </a>

                    <a href="{{ route('invoices.preview', $invoice->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Valider et Prévisualiser') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Add event listeners for calculating total
        document.getElementById('quantity').addEventListener('input', updateTotal);
        document.getElementById('unit_price').addEventListener('input', updateTotal);
        document.getElementById('discount_percent').addEventListener('input', updateTotal);
        document.getElementById('tva_rate').addEventListener('input', updateTotal);

        // Initial calculation
        updateTotal();
      });

      function updateTotal() {
        // Get values
        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
        const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
        const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;

        // Get TVA rate - use line-specific rate if provided, otherwise use invoice TVA rate
        let tvaRate;
        const lineTvaRate = document.getElementById('tva_rate').value;
        if (lineTvaRate) {
          tvaRate = parseFloat(lineTvaRate);
        } else {
          tvaRate = {{ $invoice->tva_rate }}; // Use invoice's TVA rate as default
        }

        // Calculate total HT
        let totalHT = quantity * unitPrice;
        if (discountPercent > 0) {
          totalHT = totalHT * (1 - (discountPercent / 100));
        }

        // Calculate total TTC
        const totalTTC = totalHT * (1 + (tvaRate / 100));

        // Display total TTC in the field
        document.getElementById('total_ttc_display').value = formatCurrency(totalTTC);
      }

      function formatCurrency(value) {
        return new Intl.NumberFormat('fr-FR', {style: 'currency', currency: 'EUR'}).format(value);
      }
    </script>
</x-app-layout>
