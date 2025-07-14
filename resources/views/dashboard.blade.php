<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Active Projects -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Projets actifs</h3>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ App\Models\Project::where('status', 'en_cours')->where('user_id', auth()->id())->count() }}</p>
                    </div>
                </div>

                <!-- Clients -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Clients</h3>
                        @php
                            $clientCount = App\Models\Client::where('user_id', auth()->id())->count();
                            $companyClientCount = App\Models\Company::where('user_id', auth()->id())->where('is_own_company', false)->count();
                            $totalClientCount = $clientCount + $companyClientCount;
                        @endphp
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalClientCount }}</p>
                    </div>
                </div>

                <!-- Time Tracked This Month -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Heures ce mois</h3>
                        @php
                            $totalMinutes = App\Models\TimeEntry::whereMonth('date', now()->month)
                                ->whereYear('date', now()->year)
                                ->where('user_id', auth()->id())
                                ->sum('duration_minutes');
                            $hours = floor($totalMinutes / 60);
                            $minutes = $totalMinutes % 60;
                        @endphp
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $hours }}h{{ $minutes }}</p>
                    </div>
                </div>

                <!-- Unpaid Invoices -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Factures impayées</h3>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ App\Models\Invoice::where('status', '!=', 'paid')->where('user_id', auth()->id())->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Financial Statistics -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Statistiques financières</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Revenue This Month -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenu ce mois</h4>
                            @php
                                $revenueThisMonth = App\Models\Invoice::where('status', 'paid')
                                    ->where('user_id', auth()->id())
                                    ->whereMonth('payment_date', now()->month)
                                    ->whereYear('payment_date', now()->year)
                                    ->sum('total_ht');
                            @endphp
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($revenueThisMonth, 2, ',', ' ') }} €</p>
                        </div>

                        <!-- Revenue This Year -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenu cette année</h4>
                            @php
                                $revenueThisYear = App\Models\Invoice::where('status', 'paid')
                                    ->where('user_id', auth()->id())
                                    ->whereYear('payment_date', now()->year)
                                    ->sum('total_ht');
                            @endphp
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($revenueThisYear, 2, ',', ' ') }} €</p>
                        </div>

                        <!-- URSSAF Charges To Pay -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Charges URSSAF à payer</h4>
                            @php
                                $urssafToPay = App\Models\URSSAFDeclaration::where('user_id', auth()->id())
                                    ->where('is_paid', false)
                                    ->sum('charges_amount');
                            @endphp
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($urssafToPay, 2, ',', ' ') }} €</p>
                        </div>

                        <!-- URSSAF Charges Paid This Year -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Charges URSSAF payées (année)</h4>
                            @php
                                $urssafPaid = App\Models\URSSAFDeclaration::where('user_id', auth()->id())
                                    ->where('is_paid', true)
                                    ->whereYear('payment_date', now()->year)
                                    ->sum('charges_amount');
                            @endphp
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($urssafPaid, 2, ',', ' ') }} €</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Actions rapides</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <a href="{{ route('clients.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Nouveau client
                        </a>
                        <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Nouveau projet
                        </a>
                        <a href="{{ route('time-entries.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Enregistrer du temps
                        </a>
                        <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Créer une facture
                        </a>
                        <a href="{{ route('urssaf-declarations.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Déclarer URSSAF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Activité récente</h3>
                    <div class="space-y-4">
                        @foreach(App\Models\TimeEntry::where('user_id', auth()->id())->latest()->take(5)->get() as $timeEntry)
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $timeEntry->date->format('d/m/Y') }} - {{ $timeEntry->project->name }}</p>
                                <p class="text-gray-800 dark:text-gray-200">{{ $timeEntry->description }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    @if($timeEntry->start_time && $timeEntry->end_time)
                                        {{ $timeEntry->start_time->format('H:i') }} - {{ $timeEntry->end_time->format('H:i') }}
                                    @else
                                        {{ floor($timeEntry->duration_minutes / 60) }}h{{ $timeEntry->duration_minutes % 60 }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
