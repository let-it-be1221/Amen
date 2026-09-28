<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Amen Restaurant') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex">
            <!-- Left side - branding -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white flex-col justify-between p-12 relative overflow-hidden">
                <!-- Decorative gradient blobs -->
                <div class="absolute top-0 -left-20 w-72 h-72 bg-amber-500/20 rounded-full mix-blend-overlay blur-3xl"></div>
                <div class="absolute bottom-0 -right-20 w-96 h-96 bg-orange-500/20 rounded-full mix-blend-overlay blur-3xl"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-slate-900 font-bold text-2xl shadow-lg shadow-amber-500/30">
                            A
                        </div>
                        <div>
                            <p class="text-xl font-bold">Amen Restaurant</p>
                            <p class="text-xs text-slate-400">Management System</p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 space-y-6">
                    <h1 class="text-4xl font-bold leading-tight">Run your restaurant with precision.</h1>
                    <p class="text-slate-300 text-lg">Track orders from kitchen to table, manage payments, and gain real-time insights into your operations - all from one elegant platform.</p>

                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-4">
                            <div class="text-amber-400 text-2xl mb-1">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.305 0-4.46-.786-6.187-2.111a4.125 4.125 0 0 1 7.533-2.493M9 4.5a4.125 4.125 0 1 0 0 8.25 4.125 4.125 0 0 0 0-8.25Z" /></svg>
                            </div>
                            <p class="text-sm font-medium">5 Roles</p>
                            <p class="text-xs text-slate-400">Admin, supervisor, waiter, cooker, cashier</p>
                        </div>
                        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-4">
                            <div class="text-amber-400 text-2xl mb-1">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            </div>
                            <p class="text-sm font-medium">Full Workflow</p>
                            <p class="text-xs text-slate-400">Order → Cook → Deliver → Pay</p>
                        </div>
                        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-4">
                            <div class="text-amber-400 text-2xl mb-1">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                            </div>
                            <p class="text-sm font-medium">Reports</p>
                            <p class="text-xs text-slate-400">Daily, weekly, monthly analytics</p>
                        </div>
                        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-4">
                            <div class="text-amber-400 text-2xl mb-1">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            </div>
                            <p class="text-sm font-medium">Payments</p>
                            <p class="text-xs text-slate-400">Cash, card, mobile money</p>
                        </div>
                    </div>
                </div>

                <p class="relative z-10 text-xs text-slate-500">&copy; {{ date('Y') }} Amen Restaurant. All rights reserved.</p>
            </div>

            <!-- Right side - form -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 bg-slate-50">
                <div class="w-full max-w-md">
                    <!-- Mobile logo -->
                    <div class="lg:hidden mb-8 flex items-center justify-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-slate-900 font-bold text-xl">
                            A
                        </div>
                        <div>
                            <p class="text-base font-bold text-slate-900">Amen Restaurant</p>
                            <p class="text-xs text-slate-500">Management System</p>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
