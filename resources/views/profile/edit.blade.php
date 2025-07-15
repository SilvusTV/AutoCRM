<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex">
                    <!-- Lateral Navigation -->
                    <div class="w-1/4 border-r border-gray-200 dark:border-gray-700">
                        <nav class="p-4">
                            <ul>
                                <li class="mb-2">
                                    <button id="profile-tab"
                                            class="w-full text-left px-4 py-2 rounded-md bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 font-medium">
                                        {{ __('Profile') }}
                                    </button>
                                </li>
                                <li class="mb-2">
                                    <button id="urssaf-tab"
                                            class="w-full text-left px-4 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        {{ __('URSSAF') }}
                                    </button>
                                </li>
                                <li class="mb-2">
                                    <button id="company-tab"
                                            class="w-full text-left px-4 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        {{ __('Entreprise') }}
                                    </button>
                                </li>
                                <li class="mb-2">
                                    <button id="payment-tab"
                                            class="w-full text-left px-4 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        {{ __('Moyens de paiement') }}
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Content Area -->
                    <div class="w-3/4 p-6">
                        <!-- Profile Section -->
                        <div id="profile-section" class="space-y-6">
                            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                                <div class="max-w-xl">
                                    @include('profile.partials.update-profile-information-form')
                                </div>
                            </div>

                            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                                <div class="max-w-xl">
                                    @include('profile.partials.update-password-form')
                                </div>
                            </div>

                            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                                <div class="max-w-xl">
                                    @include('profile.partials.delete-user-form')
                                </div>
                            </div>
                        </div>

                        <!-- URSSAF Section -->
                        <div id="urssaf-section" class="hidden space-y-6">
                            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                                <div class="max-w-xl">
                                    @include('profile.partials.update-urssaf-form')
                                </div>
                            </div>
                        </div>

                        <!-- Company Section -->
                        <div id="company-section" class="hidden space-y-6">
                            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                                <div class="max-w-xl">
                                    @include('profile.partials.update-company-form')
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods Section -->
                        <div id="payment-section" class="hidden space-y-6">
                            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                                <div class="max-w-xl">
                                    @include('profile.partials.update-payment-methods-form')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
      // Tab switching functionality
      document.addEventListener('DOMContentLoaded', function () {
        const tabs = {
          'profile-tab': 'profile-section',
          'urssaf-tab': 'urssaf-section',
          'company-tab': 'company-section',
          'payment-tab': 'payment-section'
        };

        // Function to activate a tab
        function activateTab(tabId) {
          // Hide all sections
          Object.values(tabs).forEach(sectionId => {
            document.getElementById(sectionId).classList.add('hidden');
          });

          // Show the selected section
          document.getElementById(tabs[tabId]).classList.remove('hidden');

          // Update tab styles
          Object.keys(tabs).forEach(tab => {
            const element = document.getElementById(tab);
            if (tab === tabId) {
              element.classList.add('bg-indigo-50', 'dark:bg-indigo-900', 'text-indigo-700', 'dark:text-indigo-300', 'font-medium');
              element.classList.remove('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            } else {
              element.classList.remove('bg-indigo-50', 'dark:bg-indigo-900', 'text-indigo-700', 'dark:text-indigo-300', 'font-medium');
              element.classList.add('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            }
          });
        }

        // Add click event listeners to tabs
        Object.keys(tabs).forEach(tabId => {
          document.getElementById(tabId).addEventListener('click', function () {
            activateTab(tabId);
          });
        });
      });
    </script>
</x-app-layout>
