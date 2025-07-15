<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informations de l\'entreprise') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Mettez à jour les informations de votre entreprise.") }}
        </p>
    </header>

    <form method="post" action="{{ isset($company) ? route('companies.update', $company) : route('companies.store') }}"
          class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @if(isset($company))
            @method('patch')
        @endif

        <div>
            <x-input-label for="name" :value="__('Raison sociale')"/>
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                          :value="old('name', $company->name ?? '')"/>
            <x-input-error class="mt-2" :messages="$errors->get('name')"/>
        </div>

        <div>
            <x-input-label for="address" :value="__('Adresse')"/>
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                          :value="old('address', $company->address ?? '')"/>
            <x-input-error class="mt-2" :messages="$errors->get('address')"/>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="email" :value="__('Email')"/>
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                              :value="old('email', $company->email ?? '')"/>
                <x-input-error class="mt-2" :messages="$errors->get('email')"/>
            </div>

            <div>
                <x-input-label for="phone" :value="__('Téléphone')"/>
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                              :value="old('phone', $company->phone ?? '')"/>
                <x-input-error class="mt-2" :messages="$errors->get('phone')"/>
            </div>
        </div>

        <div>
            <x-input-label for="regime" :value="__('Régime d\'entreprise')"/>
            <select id="regime" name="regime"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">{{ __('Sélectionnez une option') }}</option>
                <option value="auto-entrepreneur" {{ old('regime', $company->regime ?? '') === 'auto-entrepreneur' ? 'selected' : '' }}>{{ __('Auto-entrepreneur') }}</option>
                <option value="eirl" {{ old('regime', $company->regime ?? '') === 'eirl' ? 'selected' : '' }}>{{ __('EIRL') }}</option>
                <option value="eurl" {{ old('regime', $company->regime ?? '') === 'eurl' ? 'selected' : '' }}>{{ __('EURL') }}</option>
                <option value="sasu" {{ old('regime', $company->regime ?? '') === 'sasu' ? 'selected' : '' }}>{{ __('SASU') }}</option>
                <option value="sarl" {{ old('regime', $company->regime ?? '') === 'sarl' ? 'selected' : '' }}>{{ __('SARL') }}</option>
                <option value="sas" {{ old('regime', $company->regime ?? '') === 'sas' ? 'selected' : '' }}>{{ __('SAS') }}</option>
                <option value="sa" {{ old('regime', $company->regime ?? '') === 'sa' ? 'selected' : '' }}>{{ __('SA') }}</option>
                <option value="other" {{ old('regime', $company->regime ?? '') === 'other' ? 'selected' : '' }}>{{ __('Autre') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('regime')"/>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="siret" :value="__('SIRET')"/>
                <x-text-input id="siret" name="siret" type="text" class="mt-1 block w-full"
                              :value="old('siret', $company->siret ?? '')"/>
                <x-input-error class="mt-2" :messages="$errors->get('siret')"/>
            </div>

            <div id="tva_number_container">
                <x-input-label for="tva_number" :value="__('Numéro de TVA')"/>
                <x-text-input id="tva_number" name="tva_number" type="text" class="mt-1 block w-full"
                              :value="old('tva_number', $company->tva_number ?? '')"/>
                <x-input-error class="mt-2" :messages="$errors->get('tva_number')"/>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="naf_code" :value="__('Code NAF')"/>
                <x-text-input id="naf_code" name="naf_code" type="text" class="mt-1 block w-full"
                              :value="old('naf_code', $company->naf_code ?? '')"/>
                <x-input-error class="mt-2" :messages="$errors->get('naf_code')"/>
            </div>

            <div>
                <x-input-label for="country" :value="__('Pays')"/>
                <x-text-input id="country" name="country" type="text" class="mt-1 block w-full"
                              :value="old('country', $company->country ?? '')"/>
                <x-input-error class="mt-2" :messages="$errors->get('country')"/>
            </div>
        </div>

        <div>
            <x-input-label for="logo" :value="__('Logo de l\'entreprise')"/>
            <input id="logo" name="logo" type="file" accept="image/*"
                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
            <p class="mt-1 text-sm text-gray-500">Format recommandé: PNG, JPG ou WEBP, max 2MB</p>
            <x-input-error class="mt-2" :messages="$errors->get('logo')"/>

            @if(isset($company) && $company->logo_path)
                <div class="mt-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Logo actuel:</p>
                    <img src="{{Storage::url($company->logo_path) }}" alt="Logo de l'entreprise"
                         class="mt-1 h-20 w-auto">
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>

            @if (session('status') === 'company-updated')
                <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Enregistré.') }}</p>
            @endif
        </div>
    </form>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const regimeSelect = document.getElementById('regime');
        const tvaContainer = document.getElementById('tva_number_container');
        const tvaInput = document.getElementById('tva_number');

        // Function to toggle TVA number visibility
        function toggleTvaVisibility() {
          if (regimeSelect.value === 'auto-entrepreneur') {
            tvaContainer.style.display = 'none';
            tvaInput.value = ''; // Clear the TVA number when hidden
          } else {
            tvaContainer.style.display = 'block';
          }
        }

        // Initial check
        toggleTvaVisibility();

        // Add event listener for changes
        regimeSelect.addEventListener('change', toggleTvaVisibility);
      });
    </script>
</section>
