<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description"
              content="Auto-CRM Freelance - Gérez votre activité de freelance en toute simplicité. Suivez vos clients, projets, temps passé et factures en un seul endroit.">
        <meta name="keywords"
              content="freelance management, facturation freelance, gestion de projet freelance, suivi de temps, automatisation administrative, logiciel CRM freelance, productivité freelance, gestion clients, comptabilité indépendant, outil de gestion freelance">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:title" content="{{ config('app.name', 'Auto-CRM Freelance') }}">
        <meta property="og:description"
              content="Gérez votre activité de freelance en toute simplicité. Suivez vos clients, projets, temps passé et factures en un seul endroit.">
        <meta property="og:image" content="{{ asset('images/og-image.jpg', true) }}">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url('/') }}">
        <meta name="twitter:title" content="{{ config('app.name', 'Auto-CRM Freelance') }}">
        <meta name="twitter:description"
              content="Gérez votre activité de freelance en toute simplicité. Suivez vos clients, projets, temps passé et factures en un seul endroit.">
        <meta name="twitter:image" content="{{ asset('images/og-image.jpg', true) }}">

        <title>{{ config('app.name', 'Auto-CRM Freelance') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 dark:bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 fixed w-full z-10 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="font-bold text-xl text-indigo-600 dark:text-indigo-400">
                                Auto-CRM Freelance
                            </a>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:ml-10 sm:flex">
                        @if (Route::has('login'))
                            <div class="flex items-center">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 mr-4">Se connecter</a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">S'inscrire</a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out" aria-label="Main menu" aria-expanded="false" id="mobile-menu-button">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state -->
            <div class="sm:hidden hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition duration-150 ease-in-out">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition duration-150 ease-in-out">Se connecter</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block pl-3 pr-4 py-2 border-l-4 border-indigo-500 text-base font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out">S'inscrire</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="pt-16">
            <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 dark:from-indigo-800 dark:to-indigo-950 shadow-xl">
                <div class="relative">
                    <div class="absolute inset-0 overflow-hidden">
                        <img class="w-full h-full object-cover opacity-10" src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="Freelance working">
                    </div>
                    <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
                        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl drop-shadow-md">
                            Auto-CRM Freelance</h1>
                        <p class="mt-6 max-w-3xl text-xl font-medium text-white">Gérez votre activité de freelance en toute simplicité. Suivez vos clients, projets, temps passé et factures en un seul endroit.</p>
                        <div class="mt-10 flex flex-col sm:flex-row gap-4">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-900 bg-white hover:bg-gray-100 shadow-md transition-all duration-200 ease-in-out transform hover:scale-105">
                                    Commencer gratuitement
                                </a>
                            @endif
                            <a href="#features" class="inline-flex items-center justify-center px-5 py-3 border border-white text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all duration-200 ease-in-out transform hover:scale-105">
                                Découvrir les fonctionnalités
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Features Section -->
        <section id="features" class="py-16 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="lg:text-center mb-12">
                    <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">Fonctionnalités</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Tout ce dont vous avez besoin pour gérer votre activité
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-600 dark:text-gray-300 lg:mx-auto">
                        Une solution complète pour les freelances qui souhaitent se concentrer sur leur métier plutôt que sur l'administratif.
                    </p>
                </header>

                <div class="mt-12">
                    <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-12">
                        <!-- Client Management -->
                        <article
                                class="relative bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white shadow-md">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-xl leading-6 font-bold text-gray-900 dark:text-white">Gestion des clients</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Gardez toutes les informations de vos clients à portée de main. Coordonnées, historique des projets et factures en un seul endroit.
                            </p>
                        </article>

                        <!-- Project Tracking -->
                        <article
                                class="relative bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white shadow-md">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-xl leading-6 font-bold text-gray-900 dark:text-white">Suivi de projets</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Suivez l'avancement de vos projets, définissez des jalons et gardez un œil sur les délais pour livrer à temps.
                            </p>
                        </article>

                        <!-- Time Tracking -->
                        <article
                                class="relative bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white shadow-md">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-xl leading-6 font-bold text-gray-900 dark:text-white">Suivi du temps</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Enregistrez le temps passé sur chaque projet et tâche. Analysez votre productivité et facturez précisément vos heures.
                            </p>
                        </article>

                        <!-- Invoicing -->
                        <article
                                class="relative bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white shadow-md">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-xl leading-6 font-bold text-gray-900 dark:text-white">Facturation</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Créez des factures professionnelles en quelques clics. Suivez les paiements et générez des rapports financiers.
                            </p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section id="why-choose-us" class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="lg:text-center mb-12">
                    <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">
                        Pourquoi nous choisir</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        La solution idéale pour les freelances
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-600 dark:text-gray-300 lg:mx-auto">
                        Auto-CRM Freelance a été conçu spécifiquement pour répondre aux besoins des indépendants et
                        freelances.
                    </p>
                </header>

                <div class="mt-12">
                    <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-12">
                        <!-- Benefit 1 -->
                        <article class="relative p-6">
                            <div class="mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white">Gain de temps
                                    considérable</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                L'<strong>automatisation administrative</strong> vous permet de gagner jusqu'à 14 heures
                                par mois. Concentrez-vous sur votre cœur de métier plutôt que sur les tâches
                                administratives répétitives.
                            </p>
                        </article>

                        <!-- Benefit 2 -->
                        <article class="relative p-6">
                            <div class="mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white">Meilleure
                                    organisation</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Notre <strong>outil de gestion freelance</strong> centralise toutes vos informations en
                                un seul endroit. Fini les multiples tableurs et documents éparpillés sur votre
                                ordinateur.
                            </p>
                        </article>

                        <!-- Benefit 3 -->
                        <article class="relative p-6">
                            <div class="mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white">Facturation
                                    simplifiée</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Notre système de <strong>facturation freelance</strong> automatisé génère des factures
                                professionnelles en quelques clics, avec suivi des paiements et relances automatiques.
                            </p>
                        </article>

                        <!-- Benefit 4 -->
                        <article class="relative p-6">
                            <div class="mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white">Suivi précis du
                                    temps</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Notre <strong>suivi de temps</strong> intégré vous permet de mesurer précisément le
                                temps passé sur chaque projet et d'optimiser votre <strong>productivité
                                    freelance</strong>.
                            </p>
                        </article>

                        <!-- Benefit 5 -->
                        <article class="relative p-6">
                            <div class="mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white">Relation client
                                    améliorée</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Notre module de <strong>gestion clients</strong> vous aide à maintenir des relations
                                professionnelles et personnalisées avec chacun de vos clients.
                            </p>
                        </article>

                        <!-- Benefit 6 -->
                        <article class="relative p-6">
                            <div class="mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white">Comptabilité
                                    simplifiée</h3>
                            </div>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Facilitez votre <strong>comptabilité indépendant</strong> grâce à nos outils de
                                reporting financier et d'export pour votre comptable ou votre déclaration fiscale.
                            </p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="py-16 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="lg:text-center mb-12">
                    <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">
                        Comment ça marche</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Un processus simple et efficace
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-600 dark:text-gray-300 lg:mx-auto">
                        Notre <strong>logiciel AutoCRM</strong> est conçu pour être intuitif et facile à prendre en
                        main.
                    </p>
                </header>

                <div class="mt-12">
                    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                        <!-- Step 1 -->
                        <div class="relative">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white mb-4">
                                <span class="text-lg font-bold">1</span>
                            </div>
                            <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white mb-4">Créez votre
                                compte</h3>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Inscrivez-vous gratuitement et configurez votre profil en quelques minutes.
                                Personnalisez votre espace selon vos besoins spécifiques de <strong>freelance
                                    management</strong>.
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative mt-10 lg:mt-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white mb-4">
                                <span class="text-lg font-bold">2</span>
                            </div>
                            <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white mb-4">Importez vos
                                données</h3>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Ajoutez vos clients, projets en cours et historique de facturation. Notre assistant vous
                                guide pour une transition en douceur vers notre <strong>outil de gestion
                                    freelance</strong>.
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative mt-10 lg:mt-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white mb-4">
                                <span class="text-lg font-bold">3</span>
                            </div>
                            <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white mb-4">Gérez votre
                                activité</h3>
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                Utilisez nos outils de <strong>gestion de projet freelance</strong>, de suivi du temps
                                et de facturation pour optimiser votre activité quotidienne et augmenter votre
                                rentabilité.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="lg:text-center mb-12">
                    <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">
                        Témoignages</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Ce que disent nos utilisateurs
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-600 dark:text-gray-300 lg:mx-auto">
                        Découvrez comment Auto-CRM a transformé l'activité de nombreux indépendants.
                    </p>
                </header>

                <div class="mt-12">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Testimonial 1 -->
                        <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-800 font-bold text-xl">S</span>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Sophie Martin</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Graphiste freelance</p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">
                                "Grâce à Auto-CRM, j'ai pu automatiser ma <strong>facturation</strong> et gagner un
                                temps précieux. Je peux désormais me concentrer sur mes créations plutôt que sur
                                l'administratif."
                            </p>
                        </article>

                        <!-- Testimonial 2 -->
                        <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-800 font-bold text-xl">T</span>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Thomas Dubois</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Développeur web indépendant</p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">
                                "Le <strong>suivi de temps</strong> intégré m'a permis d'identifier les projets les
                                moins rentables et d'ajuster mes tarifs en conséquence. Ma <strong>productivité</strong>
                                a augmenté d'au moins 25% !"
                            </p>
                        </article>

                        <!-- Testimonial 3 -->
                        <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-800 font-bold text-xl">L</span>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Laura Petit</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Consultante marketing</p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">
                                "La <strong>gestion de projet freelance</strong> est devenue un jeu d'enfant. Je peux
                                suivre l'avancement de mes multiples projets et partager facilement les progrès avec mes
                                clients."
                            </p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-16 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="lg:text-center mb-12">
                    <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">
                        Questions fréquentes</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Tout ce que vous devez savoir
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-600 dark:text-gray-300 lg:mx-auto">
                        Vous avez des questions sur notre <strong>logiciel AutoCRM</strong> ? Voici les réponses aux
                        questions les plus fréquentes.
                    </p>
                </header>

                <div class="mt-12">
                    <dl class="space-y-6 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Question 1 -->
                        <div class="pt-6">
                            <dt class="text-lg font-medium text-gray-900 dark:text-white">
                                Est-ce que Auto-CRM Freelance est adapté à tous les types de freelances ?
                            </dt>
                            <dd class="mt-2 text-base text-gray-600 dark:text-gray-300">
                                Oui, notre <strong>outil de gestion freelance</strong> est conçu pour s'adapter à tous
                                les métiers : développeurs, designers, consultants, rédacteurs, photographes, etc.
                                L'interface est personnalisable selon vos besoins spécifiques.
                            </dd>
                        </div>

                        <!-- Question 2 -->
                        <div class="pt-6">
                            <dt class="text-lg font-medium text-gray-900 dark:text-white">
                                Comment fonctionne la facturation sur Auto-CRM Freelance ?
                            </dt>
                            <dd class="mt-2 text-base text-gray-600 dark:text-gray-300">
                                Notre système de <strong>facturation freelance</strong> vous permet de créer des devis
                                et factures personnalisés, de les envoyer directement à vos clients par email, et de
                                suivre les paiements. Vous pouvez également configurer des rappels automatiques pour les
                                factures impayées.
                            </dd>
                        </div>

                        <!-- Question 3 -->
                        <div class="pt-6">
                            <dt class="text-lg font-medium text-gray-900 dark:text-white">
                                Est-ce que je peux exporter mes données pour ma comptabilité ?
                            </dt>
                            <dd class="mt-2 text-base text-gray-600 dark:text-gray-300">
                                Absolument ! Pour faciliter votre <strong>comptabilité</strong>, nous proposons des
                                exports au format CSV et Excel compatibles avec les principaux logiciels de
                                comptabilité. Vous pouvez également générer des rapports financiers personnalisés.
                            </dd>
                        </div>

                        <!-- Question 4 -->
                        <div class="pt-6">
                            <dt class="text-lg font-medium text-gray-900 dark:text-white">
                                Comment fonctionne le suivi du temps de travail ?
                            </dt>
                            <dd class="mt-2 text-base text-gray-600 dark:text-gray-300">
                                Notre fonctionnalité de <strong>suivi de temps</strong> vous permet de chronométrer vos
                                tâches en temps réel ou d'entrer manuellement votre temps de travail. Vous pouvez
                                ensuite générer des rapports détaillés et facturer précisément vos clients selon le
                                temps passé.
                            </dd>
                        </div>

                        <!-- Question 5 -->
                        <div class="pt-6">
                            <dt class="text-lg font-medium text-gray-900 dark:text-white">
                                Est-ce que je peux essayer Auto-CRM Freelance gratuitement ?
                            </dt>
                            <dd class="mt-2 text-base text-gray-600 dark:text-gray-300">
                                Oui, nous proposons une version gratuite avec toutes les fonctionnalités essentielles de
                                <strong>freelance management</strong>. Vous pouvez l'utiliser sans limite de durée et
                                passer à un forfait premium lorsque votre activité se développe.
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="lg:text-center mb-12">
                    <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">
                        Tarifs</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Des formules adaptées à vos besoins
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-600 dark:text-gray-300 lg:mx-auto">
                        Choisissez la formule qui correspond le mieux à votre activité de <strong>freelance</strong>.
                    </p>
                </header>

                <div class="mt-12 space-y-12 lg:space-y-0 lg:grid lg:grid-cols-2 lg:gap-x-8">
                    <!-- Free Plan -->
                    <div class="relative p-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm flex flex-col">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Formule Gratuite</h3>
                            <p class="mt-4 flex items-baseline text-gray-900 dark:text-white">
                                <span class="text-5xl font-extrabold tracking-tight">0€</span>
                                <span class="ml-1 text-xl font-semibold">/mois</span>
                            </p>
                            <p class="mt-6 text-gray-500 dark:text-gray-400">Parfait pour démarrer et tester notre
                                <strong>outil de gestion</strong>.</p>

                            <!-- Feature List -->
                            <ul role="list" class="mt-6 space-y-6">
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">Jusqu'à 5 clients</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">Jusqu'à 10 projets</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400"><strong>Suivi de temps</strong> basique</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400"><strong>Facturation freelance</strong> (jusqu'à 10 factures/mois)</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">Tableaux de bord basiques</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-gray-500 dark:text-gray-400">Support par email</span>
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('register') }}"
                           class="mt-8 block w-full bg-indigo-50 dark:bg-indigo-900 py-3 px-6 border border-transparent rounded-md text-center font-medium text-indigo-700 dark:text-indigo-200 hover:bg-indigo-100 dark:hover:bg-indigo-800">Commencer
                            gratuitement</a>
                    </div>

                    <!-- Premium Plan -->
                    <div class="relative p-8 bg-indigo-600 dark:bg-indigo-700 rounded-2xl shadow-xl flex flex-col">
                        <div class="absolute inset-0 flex items-center justify-end">
                            <div class="h-24 w-24 rounded-full bg-indigo-800 bg-opacity-50 flex items-center justify-center transform translate-x-1/2 -translate-y-1/2">
                                <span class="text-xs font-bold text-white uppercase tracking-wide">Recommandé</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white">Formule Premium</h3>
                            <p class="mt-4 flex items-baseline text-white">
                                <span class="text-5xl font-extrabold tracking-tight">19.<span class="text-xl">99€</span></span>
                                <span class="ml-1 text-xl font-semibold">/mois</span>
                            </p>
                            <p class="mt-6 text-indigo-200">Idéal pour les <strong>freelances</strong> qui souhaitent
                                développer leur activité.</p>

                            <!-- Feature List -->
                            <ul role="list" class="mt-6 space-y-6">
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-indigo-200">Clients illimités</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-indigo-200">Projets illimités</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-indigo-200"><strong>Suivi de temps</strong> avancé avec rapports détaillés</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-indigo-200"><strong>Facturation freelance</strong> illimitée avec relances automatiques</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-indigo-200">Tableaux de bord avancés et personnalisables</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-indigo-200">Exports comptables avancés</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-indigo-200">Support prioritaire par email et téléphone</span>
                                </li>
                                <li class="flex">
                                    <svg class="flex-shrink-0 w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="ml-3 text-indigo-200">Intégrations avec des outils externes (comptabilité, CRM, etc.)</span>
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('register') }}"
                           class="mt-8 block w-full bg-white py-3 px-6 border border-transparent rounded-md text-center font-medium text-indigo-700 hover:bg-indigo-50">Essayer
                            Premium dès maintenant !</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-950 dark:to-blue-950 shadow-inner">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-20 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                            <span class="block">Prêt à simplifier votre gestion ?</span>
                            <span class="block text-indigo-600 dark:text-indigo-400 mt-2">Commencez dès aujourd'hui gratuitement.</span>
                        </h2>
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-3xl">
                            Rejoignez des centaines de freelances qui ont déjà optimisé leur gestion administrative grâce à notre plateforme intuitive et complète.
                        </p>
                    </div>
                    <div class="mt-8 lg:mt-0 flex flex-col sm:flex-row sm:justify-center lg:justify-end gap-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all duration-200 ease-in-out transform hover:scale-105">
                                S'inscrire gratuitement
                            </a>
                        @endif
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-md transition-all duration-200 ease-in-out transform hover:scale-105">
                            Se connecter
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
                <div class="flex justify-center space-x-6 md:order-2">
                    <span class="text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        &copy; {{ date('Y') }} Auto-CRM Freelance. Tous droits réservés.
                    </span>
                </div>
                <div class="mt-8 md:mt-0 md:order-1">
                    <p class="text-center text-base text-gray-600 dark:text-gray-400">
                        Simplifiez votre gestion freelance
                    </p>
                </div>
            </div>
        </footer>

        <script>
            // Mobile menu toggle
            document.addEventListener('DOMContentLoaded', function() {
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');

                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.addEventListener('click', function() {
                        mobileMenu.classList.toggle('hidden');
                    });
                }

                // Smooth scroll for anchor links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();

                        const targetId = this.getAttribute('href');
                        const targetElement = document.querySelector(targetId);

                        if (targetElement) {
                            window.scrollTo({
                                top: targetElement.offsetTop - 80, // Adjust for fixed header
                                behavior: 'smooth'
                            });
                        }
                    });
                });
            });
        </script>
    </body>
</html>
