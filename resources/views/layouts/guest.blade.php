<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-brand-gray relative overflow-hidden z-0">
            <!-- Decorative Background Element -->
            <div class="absolute top-0 left-0 w-full h-[400px] bg-brand-teal transform -skew-y-6 origin-top-left -z-10 shadow-lg"></div>

            <div class="z-10 text-center mb-8 mt-12 sm:mt-0">
                <a href="/" class="flex items-center justify-center gap-2 group">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                        <svg class="w-7 h-7 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-3xl tracking-tight text-white">Turnero<span class="text-brand-dark">Médico</span></span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-2xl z-10 border border-gray-100">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-sm text-gray-500 z-10">
                <a href="/" class="hover:text-brand-dark transition font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver al inicio
                </a>
            </div>
        </div>
    </body>
</html>
