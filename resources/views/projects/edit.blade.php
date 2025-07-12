<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modifier le projet') }}: {{ $project->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Annuler') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('projects.update', $project->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Combined Recipient Selector -->
                        <div>
                            <x-input-label for="recipient_id" :value="__('Destinataire')"/>
                            <select id="recipient_id" name="recipient_id"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">Sélectionnez un destinataire</option>
                                @foreach($recipients as $recipient)
                                    <option value="{{ $recipient['id'] }}" {{ ((old('recipient_id', $selectedRecipientId) == $recipient['id']) && ($project->client_type == $recipient['type'])) ? 'selected' : '' }}>
                                        {{ $recipient['type_icon'] }} {{ $recipient['name'] }} {{ $recipient['details'] }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('recipient_id')" class="mt-2"/>
                        </div>

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nom du projet')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $project->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description (optionnel)')" />
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="4">{{ old('description', $project->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Statut')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="en_cours" {{ old('status', $project->status) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="termine" {{ old('status', $project->status) == 'termine' ? 'selected' : '' }}>Terminé</option>
                                <option value="archive" {{ old('status', $project->status) == 'archive' ? 'selected' : '' }}>Archivé</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Start Date -->
                        <div>
                            <x-input-label for="start_date" :value="__('Date de début (optionnel)')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <!-- End Date -->
                        <div>
                            <x-input-label for="end_date" :value="__('Date de fin (optionnel)')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
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
</x-app-layout>
