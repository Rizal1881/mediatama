<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|cormorant-garamond:500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10 bg-[#0F0D14] bg-[radial-gradient(ellipse_at_top,#2A2436,#0F0D14_65%)]">
            <div class="text-center">
                <a href="/" class="font-['Cormorant_Garamond'] text-5xl font-semibold text-[#C9A961]">
                    Mediatama
                </a>
                <p class="mt-2 text-sm text-[#9A93A8]">Tonton video dengan izin dari admin</p>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-8 py-8 bg-[#FBF7EE] border border-[#C9A961]/40 rounded-xl shadow-2xl shadow-black/40">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>