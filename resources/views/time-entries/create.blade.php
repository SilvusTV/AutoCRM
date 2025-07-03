<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Ajouter du temps') }}
            </h2>
            <a href="{{ route('time-entries.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('time-entries.store') }}" class="space-y-6">
                        @csrf

                        <!-- Project -->
                        <div>
                            <x-input-label for="project_id" :value="__('Projet')" />
                            <select id="project_id" name="project_id" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">Sélectionnez un projet</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $selectedProjectId) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->client->name }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                        </div>

                        <!-- Date -->
                        <div>
                            <x-input-label for="date" :value="__('Date')" />
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <!-- Entry Type Selection -->
                        <div>
                            <x-input-label :value="__('Type de saisie')" />
                            <div class="mt-2 space-y-4">
                                <div class="flex items-center">
                                    <input id="entry_type_duration" type="radio" name="entry_type" value="duration" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('entry_type', 'duration') === 'duration' ? 'checked' : '' }} />
                                    <label for="entry_type_duration" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                        Durée totale
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="entry_type_time_range" type="radio" name="entry_type" value="time_range" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('entry_type') === 'time_range' ? 'checked' : '' }} />
                                    <label for="entry_type_time_range" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                        Plage horaire
                                    </label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('entry_type')" class="mt-2" />
                        </div>

                        <!-- Duration Fields (shown when entry_type is duration) -->
                        <div id="duration_fields" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="duration_hours" :value="__('Heures')" />
                                    <x-text-input id="duration_hours" class="block mt-1 w-full" type="number" name="duration_hours" :value="old('duration_hours', 0)" min="0" />
                                    <x-input-error :messages="$errors->get('duration_hours')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="duration_minutes" :value="__('Minutes')" />
                                    <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" name="duration_minutes" :value="old('duration_minutes', 0)" min="0" max="59" />
                                    <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Time Range Fields (shown when entry_type is time_range) -->
                        <div id="time_range_fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="start_time" :value="__('Heure de début')" />
                                    <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" :value="old('start_time')" />
                                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="end_time" :value="__('Heure de fin')" />
                                    <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" :value="old('end_time')" />
                                    <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description (optionnel)')" />
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Enregistrer') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const durationFields = document.getElementById('duration_fields');
            const timeRangeFields = document.getElementById('time_range_fields');
            const entryTypeDuration = document.getElementById('entry_type_duration');
            const entryTypeTimeRange = document.getElementById('entry_type_time_range');

            function toggleFields() {
                if (entryTypeDuration.checked) {
                    durationFields.classList.remove('hidden');
                    timeRangeFields.classList.add('hidden');
                } else {
                    durationFields.classList.add('hidden');
                    timeRangeFields.classList.remove('hidden');
                }
            }

            // Initial toggle
            toggleFields();

            // Toggle on change
            entryTypeDuration.addEventListener('change', toggleFields);
            entryTypeTimeRange.addEventListener('change', toggleFields);
        });
    </script>
</x-app-layout>