<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Chronomètre') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('time-entries.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form id="stopwatchForm" method="POST" action="{{ route('time-entries.store-stopwatch') }}"
                          class="space-y-6">
                        @csrf
                        <input type="hidden" id="start_time" name="start_time" value="">
                        <input type="hidden" id="end_time" name="end_time" value="">
                        <input type="hidden" id="selected_project_id" name="project_id" value="">

                        <!-- Project -->
                        <div>
                            <x-input-label for="project_id" :value="__('Projet')"/>
                            <select id="project_id"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                    required>
                                <option value="">Sélectionnez un projet</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $selectedProjectId) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->client->name }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('project_id')" class="mt-2"/>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description (optionnel)')"/>
                            <textarea id="description" name="description"
                                      class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                      rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                        </div>

                        <!-- Stopwatch Display -->
                        <div class="mt-8 text-center">
                            <div id="stopwatch" class="text-6xl font-mono mb-6">00:00:00</div>

                            <div class="flex justify-center space-x-4">
                                <button type="button" id="startBtn"
                                        class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Démarrer
                                </button>

                                <button type="button" id="stopBtn"
                                        class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                                    </svg>
                                    Arrêter
                                </button>

                                <button type="button" id="resetBtn"
                                        class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Réinitialiser
                                </button>
                            </div>
                        </div>

                        <!-- Save Button (visible only after stopping) -->
                        <div id="saveContainer" class="flex items-center justify-center mt-8 hidden">
                            <x-primary-button type="submit" class="px-8 py-4 text-base">
                                {{ __('Enregistrer le temps') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const stopwatchDisplay = document.getElementById('stopwatch');
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const resetBtn = document.getElementById('resetBtn');
        const saveContainer = document.getElementById('saveContainer');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const projectSelect = document.getElementById('project_id');
        const selectedProjectIdInput = document.getElementById('selected_project_id');

        let startTime;
        let elapsedTime = 0;
        let timerInterval;
        let isRunning = false;

        // Format time as HH:MM:SS
        function formatTime(timeInSeconds) {
          const hours = Math.floor(timeInSeconds / 3600).toString().padStart(2, '0');
          const minutes = Math.floor((timeInSeconds % 3600) / 60).toString().padStart(2, '0');
          const seconds = Math.floor(timeInSeconds % 60).toString().padStart(2, '0');
          return `${hours}:${minutes}:${seconds}`;
        }

        // Update the stopwatch display
        function updateDisplay() {
          const currentTime = isRunning ? Math.floor((Date.now() - startTime) / 1000) + elapsedTime : elapsedTime;
          stopwatchDisplay.textContent = formatTime(currentTime);
        }

        // Start the stopwatch
        startBtn.addEventListener('click', function () {
          if (!projectSelect.value) {
            alert('Veuillez sélectionner un projet avant de démarrer le chronomètre.');
            return;
          }

          if (!isRunning) {
            // Record the start time
            startTime = Date.now();
            if (elapsedTime === 0) {
              // Only set the start_time input if we're starting from zero
              startTimeInput.value = new Date().toISOString().slice(0, 19).replace('T', ' ');
            }

            // Start the timer
            isRunning = true;
            timerInterval = setInterval(updateDisplay, 1000);

            // Update UI
            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            resetBtn.classList.add('hidden');
            saveContainer.classList.add('hidden');

            // Store the selected project ID in the hidden field
            selectedProjectIdInput.value = projectSelect.value;

            // Disable project selection while timer is running
            projectSelect.setAttribute('disabled', 'disabled');
          }
        });

        // Stop the stopwatch
        stopBtn.addEventListener('click', function () {
          if (isRunning) {
            // Stop the timer
            clearInterval(timerInterval);
            elapsedTime += Math.floor((Date.now() - startTime) / 1000);
            isRunning = false;

            // Record the end time
            endTimeInput.value = new Date().toISOString().slice(0, 19).replace('T', ' ');

            // Update UI
            stopBtn.classList.add('hidden');
            startBtn.classList.remove('hidden');
            resetBtn.classList.remove('hidden');
            saveContainer.classList.remove('hidden');
          }
        });

        // Reset the stopwatch
        resetBtn.addEventListener('click', function () {
          // Reset the timer
          clearInterval(timerInterval);
          elapsedTime = 0;
          isRunning = false;
          startTimeInput.value = '';
          endTimeInput.value = '';
          selectedProjectIdInput.value = '';

          // Update UI
          stopwatchDisplay.textContent = '00:00:00';
          resetBtn.classList.add('hidden');
          startBtn.classList.remove('hidden');
          stopBtn.classList.add('hidden');
          saveContainer.classList.add('hidden');

          // Enable project selection
          projectSelect.removeAttribute('disabled');
        });

        // Form submission validation
        document.getElementById('stopwatchForm').addEventListener('submit', function (e) {
          if (!startTimeInput.value || !endTimeInput.value) {
            e.preventDefault();
            alert('Veuillez démarrer et arrêter le chronomètre avant d\'enregistrer.');
            return;
          }

          if (!selectedProjectIdInput.value) {
            e.preventDefault();
            alert('Veuillez sélectionner un projet avant d\'enregistrer.');

          }
        });
      });
    </script>
</x-app-layout>
