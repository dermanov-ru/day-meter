<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="push-public-key" content="{{ config('push.public_key') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        <link rel="icon" type="image/png" href="/images/icon-192.png">
        <meta name="theme-color" content="#3b82f6">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'DayMeter') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="appInitializer()" x-init="init()">
        <script>
            // Check lock state BEFORE Alpine initializes to prevent content flash
            if (localStorage.getItem('app_lock_state') === 'locked') {
                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';
            }
        </script>
        
        <!-- App Lock Overlay -->
        @include('components.app-lock')

        <!-- Biometric Setup Prompt -->
        @include('components.biometric-setup')

        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Floating Action Button (Mobile) -->
            @if (Route::currentRouteName() !== 'entry.show')
                <a href="{{ route('entry.show') }}" class="sm:hidden fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg z-40 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            @endif
        </div>

        <script>
            // Initialize Alpine store for app lock
            document.addEventListener('alpine:init', () => {
                // Check if app should be locked from previous session
                const lockState = localStorage.getItem('app_lock_state');
                const shouldBeLocked = lockState === 'locked';
                
                Alpine.store('appLock', {
                    isLocked: shouldBeLocked,
                    lastActivity: Date.now(),
                    inactivityTimeout: 30 * 60 * 1000, // 30 minutes

                    unlock() {
                        this.isLocked = false;
                        this.lastActivity = Date.now();
                        localStorage.setItem('app_lock_state', 'unlocked');
                    },

                    lock() {
                        this.isLocked = true;
                        localStorage.setItem('app_lock_state', 'locked');
                    },

                    activity() {
                        this.lastActivity = Date.now();
                    },
                });
            });

            // App initializer component
            function appInitializer() {
                return {
                    async init() {
                        // Wait for Alpine to be ready
                        await new Promise(resolve => {
                            if (window.Alpine) {
                                resolve();
                            } else {
                                document.addEventListener('alpine:initialized', resolve);
                            }
                        });

                        // Check biometric status
                        try {
                            const response = await fetch('/api/biometric/status');
                            const status = await response.json();
                            
                            // DON'T lock on initial page load
                            // User is already authenticated with session
                            // Lock only on visibility change (background return)

                            // Track activity
                            document.addEventListener('click', () => Alpine.store('appLock').activity());
                            document.addEventListener('keypress', () => Alpine.store('appLock').activity());

                            // Handle page visibility change (lock when returning from background)
                            document.addEventListener('visibilitychange', () => {
                                if (document.hidden && status.biometric_enabled) {
                                    console.log('App going to background - locking');
                                    Alpine.store('appLock').lock();
                                } else if (!document.hidden && status.biometric_enabled && Alpine.store('appLock').isLocked) {
                                    console.log('App returning from background - keeping locked');
                                    // Already locked, no need to change
                                }
                            });
                            
                            // Handle tab/window focus change
                            window.addEventListener('blur', () => {
                                if (status.biometric_enabled) {
                                    console.log('Window lost focus - locking');
                                    Alpine.store('appLock').lock();
                                }
                            });
                            
                            // Auto-lock on timeout (30 min inactivity)
                            setInterval(() => {
                                const lastActivity = Alpine.store('appLock').lastActivity;
                                const timeSinceActivity = Date.now() - lastActivity;
                                const timeout = 30 * 60 * 1000; // 30 minutes
                                
                                if (timeSinceActivity > timeout && status.biometric_enabled && !Alpine.store('appLock').isLocked) {
                                    console.log('Inactivity timeout - locking');
                                    Alpine.store('appLock').lock();
                                }
                            }, 60000); // Check every minute
                        } catch (error) {
                            console.error('Error initializing app lock:', error);
                        }
                    }
                };
            }
        </script>
    </body>
</html>
