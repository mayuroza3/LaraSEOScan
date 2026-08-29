<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LaraSEOScan') }}</title>

        <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-icon.jpg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #ffffff !important;
                color: #495057;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background-color: #f4f6f8; position: relative; overflow: hidden;">
            <!-- Ambient Glow matching app layout -->
            <div style="content: ''; position: absolute; width: 600px; height: 600px; top: -200px; left: -200px; background: radial-gradient(circle, rgba(13, 110, 253, 0.05) 0%, rgba(0,0,0,0) 70%); z-index: 1; pointer-events: none;"></div>
            
            <div style="position: relative; z-index: 10;" class="text-center mb-2">
                <a href="/">
                    <img src="{{ asset('images/logo-full.png') }}" alt="LaraSEOScan - SEO Analytics Platform Logo" style="height: 60px; width: auto;" height="60">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-4 px-6 py-5 bg-white border shadow-sm overflow-hidden sm:rounded-xl" style="position: relative; z-index: 10; border-color: #e9ecef !important; border-radius: 12px;">
                {{ $slot }}
            </div>

            <div class="mt-5 text-center text-sm text-muted" style="position: relative; z-index: 10;">
                <a href="{{ route('legal.privacy') }}" class="text-decoration-none text-muted hover:text-dark mx-2">Privacy Policy</a> &bull;
                <a href="{{ route('legal.terms') }}" class="text-decoration-none text-muted hover:text-dark mx-2">Terms of Service</a> &bull;
                <a href="{{ route('legal.cookies') }}" class="text-decoration-none text-muted hover:text-dark mx-2">Cookie Policy</a>
            </div>
        </div>
    </body>
</html>
