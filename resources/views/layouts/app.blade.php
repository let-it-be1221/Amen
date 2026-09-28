<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Amen Restaurant'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
        @stack('scripts')
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen flex">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col min-w-0 md:pl-64">
                @include('layouts.topbar')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white border-b border-slate-200">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Flash messages -->
                @if (session('success'))
                    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition>
                        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-start gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error') || $errors->any())
                    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
                        <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">
                            @if (session('error'))
                                <p>{{ session('error') }}</p>
                            @endif
                            @if ($errors->any())
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>

                <footer class="border-t border-slate-200 bg-white py-4 mt-auto">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
                        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Amen Restaurant') }}. All rights reserved.</p>
                        <p>Powered by Laravel {{ app()->version() }}</p>
                    </div>
                </footer>
            </div>
        </div>

        @if (isset($modal) && $modal)
            @yield('modal-content')
        @endif
    </body>
</html>
