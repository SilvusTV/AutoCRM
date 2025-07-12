<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $invoice->isQuote() ? 'Aperçu du devis' : 'Aperçu de la facture' }} {{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('invoices.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
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

            <!-- Preview Notice -->
            @if(!$invoice->isValidated())
                <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                    <p class="font-semibold">{{ __('Ceci est un aperçu. Vous pouvez encore modifier ce document avant de le finaliser.') }}</p>
                    <div class="flex justify-end mt-2 space-x-2">
                        <a href="{{ route('invoices.edit', $invoice->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Modifier') }}
                        </a>
                        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Supprimer') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <p class="font-semibold">{{ __('Ce document a été finalisé et ne peut plus être modifié.') }}</p>
                </div>
            @endif

            <!-- Invoice Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ $invoice->isQuote() ? 'Informations du devis' : 'Informations de la facture' }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Numéro</p>
                            <p class="font-medium">{{ $invoice->invoice_number }}</p>
                        </div>

                        @if($invoice->client)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Client</p>
                                <p class="font-medium">{{ $invoice->client->name }}</p>
                            </div>
                        @endif

                        @if($invoice->company)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Entreprise</p>
                                <p class="font-medium">{{ $invoice->company->name }}</p>
                            </div>
                        @endif

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Projet</p>
                            <p class="font-medium">{{ $invoice->project->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Statut</p>
                            <div class="flex items-center space-x-2">
                                <p class="font-medium">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($invoice->status == 'draft') bg-gray-100 text-gray-800 
                                        @elseif($invoice->status == 'sent') bg-blue-100 text-blue-800 
                                        @elseif($invoice->status == 'paid') bg-green-100 text-green-800
                                        @elseif($invoice->status == 'cancelled') bg-red-100 text-red-800
                                        @elseif($invoice->status == 'overdue') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($invoice->status == 'draft')
                                            Brouillon
                                        @elseif($invoice->status == 'sent')
                                            Envoyée
                                        @elseif($invoice->status == 'paid')
                                            Payée
                                        @elseif($invoice->status == 'cancelled')
                                            Annulée
                                        @elseif($invoice->status == 'overdue')
                                            Expirée
                                        @else
                                            {{ $invoice->status }}
                                        @endif
                                    </span>
                                </p>
                                <form action="{{ route('invoices.update-status', $invoice->id) }}" method="POST"
                                      class="inline-flex items-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                            class="ml-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm">
                                        <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>
                                            Brouillon
                                        </option>
                                        <option value="sent" {{ $invoice->status == 'sent' ? 'selected' : '' }}>
                                            Envoyée
                                        </option>
                                        <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Payée
                                        </option>
                                        <option value="cancelled" {{ $invoice->status == 'cancelled' ? 'selected' : '' }}>
                                            Annulée
                                        </option>
                                        <option value="overdue" {{ $invoice->status == 'overdue' ? 'selected' : '' }}>
                                            Expirée
                                        </option>
                                    </select>
                                    <button type="submit"
                                            class="ml-2 inline-flex items-center px-2 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Mettre à jour') }}
                                    </button>
                                </form>
                            </div>
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
                        <a href="{{ route('invoices.pdf', $invoice->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Télécharger PDF') }}
                        </a>
                        @if(!$invoice->isValidated())
                            <form action="{{ route('invoices.validate', $invoice->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Finaliser') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Lines -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Lignes
                        de {{ $invoice->isQuote() ? 'devis' : 'facture' }}</h3>

                    @if ($invoice->invoiceLines->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Quantité
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Prix unitaire
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total HT
                                    </th>
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
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($line->unit_price, 2, ',', ' ') }}
                                                €
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($line->total_ht, 2, ',', ' ') }}
                                                €
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-medium">Total HT:</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($invoice->total_ht, 2, ',', ' ') }}
                                        €
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-medium">TVA
                                        ({{ $invoice->tva_rate }}%):
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($invoice->total_ttc - $invoice->total_ht, 2, ',', ' ') }}
                                        €
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-medium">Total TTC:</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($invoice->total_ttc, 2, ',', ' ') }}
                                        €
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-400">Aucune ligne trouvée. Vous pourrez ajouter des
                                lignes après avoir finalisé ce document.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
