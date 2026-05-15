<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-950">
        @php
            $isAr = app()->getLocale() === 'ar';
        @endphp
        <div class="relative min-h-screen flex flex-col sm:justify-center items-center px-4 py-8">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(59,130,246,.25),transparent_35%),radial-gradient(circle_at_80%_10%,rgba(168,85,247,.2),transparent_30%),radial-gradient(circle_at_50%_90%,rgba(34,211,238,.2),transparent_35%)]"></div>
            <div class="relative w-full sm:max-w-lg flex justify-end mb-3">
                <div class="inline-flex rounded-xl bg-white/10 backdrop-blur border border-white/20 p-1 text-xs font-semibold text-white">
                    <a href="{{ route('locale.switch', 'ar') }}"
                       class="px-3 py-1.5 rounded-lg transition {{ $isAr ? 'bg-white text-slate-900 shadow' : 'text-white/80 hover:text-white' }}">
                        AR
                    </a>
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="px-3 py-1.5 rounded-lg transition {{ ! $isAr ? 'bg-white text-slate-900 shadow' : 'text-white/80 hover:text-white' }}">
                        EN
                    </a>
                </div>
            </div>
            <div class="relative">
                <a href="/">
                    <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-24 h-auto drop-shadow-lg">
                </a>
            </div>

            <div class="relative w-full sm:max-w-lg mt-6 px-6 py-6 bg-white/95 backdrop-blur-xl border border-white/50 shadow-2xl overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
