<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informations URSSAF') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Mettez à jour vos informations URSSAF.") }}
        </p>
    </header>

    <form method="post" action="{{ route('urssaf.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="declaration_frequency" :value="__('Rythme de déclaration')"/>
            <select id="declaration_frequency" name="declaration_frequency"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">{{ __('Sélectionnez une option') }}</option>
                <option value="monthly" {{ old('declaration_frequency', $user->declaration_frequency) === 'monthly' ? 'selected' : '' }}>{{ __('Mensuel') }}</option>
                <option value="quarterly" {{ old('declaration_frequency', $user->declaration_frequency) === 'quarterly' ? 'selected' : '' }}>{{ __('Trimestriel') }}</option>
                <option value="annually" {{ old('declaration_frequency', $user->declaration_frequency) === 'annually' ? 'selected' : '' }}>{{ __('Annuel') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('declaration_frequency')"/>
        </div>

        <div>
            <x-input-label for="tax_level" :value="__('Taux d\'imposition en %')"/>
            <div class="relative mt-1">
                <x-text-input
                        id="tax_level"
                        name="tax_level"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        class="block w-full pr-10 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        :value="old('tax_level', $user->tax_level)"
                        placeholder="20.00"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <span class="text-gray-500 dark:text-gray-400">%</span>
                </div>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Entrez votre taux d\'imposition (ex: 20.00 pour 20%)') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('tax_level')"/>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>

            @if (session('status') === 'urssaf-updated')
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
</section>
