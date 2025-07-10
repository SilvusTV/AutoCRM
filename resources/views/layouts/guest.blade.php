<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description"
              content="Auto-CRM Freelance - Gérez votre activité de freelance en toute simplicité. Suivez vos clients, projets, temps passé et factures en un seul endroit.">

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
        <meta name="twitter:title" content="{{ config('app.name', 'Laravel') }}">
        <meta name="twitter:description"
              content="Gérez votre activité de freelance en toute simplicité. Suivez vos clients, projets, temps passé et factures en un seul endroit.">
        <meta name="twitter:image" content="{{ asset('images/og-image.jpg', true) }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="h-20 fill-current text-gray-500"/>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
