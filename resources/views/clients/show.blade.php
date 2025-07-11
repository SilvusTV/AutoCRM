<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $client->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('clients.edit', $client->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('clients.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
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

            <!-- Client Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Informations du client</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Nom</p>
                            <p class="font-medium">{{ $client->name }}</p>
                        </div>


                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Entreprise associée</p>
                            <p class="font-medium">
                                @if($client->company)
                                    <a href="{{ route('companies.show', $client->company->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        {{ $client->company->name }}
                                    </a>
                                @else
                                    Non spécifié
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                            <p class="font-medium">{{ $client->email }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Téléphone</p>
                            <p class="font-medium">{{ $client->phone ?? 'Non spécifié' }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Adresse</p>
                            <p class="font-medium">{{ $client->address ?? 'Non spécifié' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Pays</p>
                            <p class="font-medium">{{ $client->country ?? 'Non spécifié' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Date de création</p>
                            <p class="font-medium">{{ $client->created_at->format('d/m/Y') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Temps total</p>
                            <p class="font-medium">{{ $hours }}h{{ $minutes }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Supprimer ce client') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Client Projects -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Projets du client</h3>
                        <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Nouveau projet') }}
                        </a>
                    </div>

                    @if ($projects->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date de début</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date de fin</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach ($projects as $project)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($project->status == 'en_cours') bg-green-100 text-green-800 
                                                    @elseif($project->status == 'termine') bg-blue-100 text-blue-800 
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $project->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $project->start_date ? $project->start_date->format('d/m/Y') : '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $project->end_date ? $project->end_date->format('d/m/Y') : '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('projects.show', $project->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Voir</a>
                                                    <a href="{{ route('projects.edit', $project->id) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">Modifier</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">Aucun projet trouvé pour ce client.</p>
                            <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Créer un premier projet') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
