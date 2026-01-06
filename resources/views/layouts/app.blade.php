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
        <style>
            /* Always show lock screen first to prevent content flash */
            .app-lock-wrapper { z-index: 999 !important; }
            .app-content-wrapper { opacity: 0; transition: opacity 0.3s; }
            .app-content-wrapper.unlocked { opacity: 1; }
        </style>
        
        <!-- App Lock Overlay -->
        @include('components.app-lock')

        <!-- Biometric Setup Prompt -->
        @include('components.biometric-setup')

        <div class="app-content-wrapper min-h-screen bg-gray-100">
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
                Alpine.store('appLock', {
                    isLocked: true, // Always start locked to prevent content flash
                    lastActivity: Date.now(),
                    inactivityTimeout: 30 * 60 * 1000, // 30 minutes
                    lockReason: '', // For debugging

                    unlock() {
                        this.isLocked = false;
                        this.lastActivity = Date.now();
                        localStorage.setItem('biometric_unlocked', 'true');
                        localStorage.setItem('unlock_timestamp', Date.now().toString());
                        this.lockReason = '';
                        document.querySelector('.app-content-wrapper')?.classList.add('unlocked');
                        console.log('🔓 UNLOCKED');
                    },

                    lock(reason = 'unknown') {
                        this.isLocked = true;
                        this.lockReason = reason;
                        localStorage.setItem('biometric_unlocked', 'false');
                        localStorage.removeItem('unlock_timestamp');
                        document.querySelector('.app-content-wrapper')?.classList.remove('unlocked');
                        console.log('🔒 LOCKED:', reason);
                        alert('🔒 App locked: ' + reason);
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

                        // Check biometric status and determine if lock is needed
                        try {
                            const response = await fetch('/api/biometric/status');
                            const status = await response.json();
                            
                            console.log('🔍 Checking biometric status:', status);
                            
                            // Simple logic: check if already unlocked
                            const isUnlocked = localStorage.getItem('biometric_unlocked') === 'true';
                            const unlockTimestamp = localStorage.getItem('unlock_timestamp');
                            
                            console.log('🔍 Current state:', { isUnlocked, unlockTimestamp, biometric_enabled: status.biometric_enabled });
                            
                            // Unlock immediately if:
                            // 1. Biometric is not enabled, OR
                            // 2. Already unlocked in localStorage
                            if (!status.biometric_enabled) {
                                console.log('✅ Unlocking: biometric disabled');
                                Alpine.store('appLock').unlock();
                            } else if (isUnlocked && unlockTimestamp) {
                                console.log('✅ Unlocking: already unlocked in storage');
                                Alpine.store('appLock').unlock();
                            } else {
                                console.log('🔒 Staying locked: biometric enabled and not unlocked');
                                Alpine.store('appLock').lock('biometric enabled, not unlocked');
                            }

                            // Track activity
                            document.addEventListener('click', () => Alpine.store('appLock').activity());
                            document.addEventListener('keypress', () => Alpine.store('appLock').activity());

                            // Handle page visibility change (lock when going to background)
                            let visibilityTimeout;
                            document.addEventListener('visibilitychange', () => {
                                if (document.hidden && status.biometric_enabled) {
                                    // Delay lock to avoid false positives during navigation
                                    visibilityTimeout = setTimeout(() => {
                                        console.log('🌙 App going to background - locking');
                                        Alpine.store('appLock').lock('app went to background');
                                    }, 500); // 500ms delay
                                } else if (!document.hidden) {
                                    // Cancel lock if page becomes visible again quickly
                                    if (visibilityTimeout) {
                                        clearTimeout(visibilityTimeout);
                                        console.log('✅ Cancelled background lock - page visible again');
                                    }
                                }
                            });
                            
                            // Handle tab/window focus change
                            let blurTimeout;
                            window.addEventListener('blur', () => {
                                if (status.biometric_enabled) {
                                    // Delay lock to avoid false positives during navigation
                                    blurTimeout = setTimeout(() => {
                                        console.log('🌫️ Window lost focus - locking');
                                        Alpine.store('appLock').lock('window lost focus');
                                    }, 500); // 500ms delay
                                }
                            });
                            
                            window.addEventListener('focus', () => {
                                // Cancel lock if window gets focus back quickly
                                if (blurTimeout) {
                                    clearTimeout(blurTimeout);
                                    console.log('✅ Cancelled focus lock - window focused again');
                                }
                            });
                            
                            // Auto-lock on timeout (30 min inactivity)
                            setInterval(() => {
                                const lastActivity = Alpine.store('appLock').lastActivity;
                                const timeSinceActivity = Date.now() - lastActivity;
                                const timeout = 30 * 60 * 1000; // 30 minutes
                                
                                if (timeSinceActivity > timeout && status.biometric_enabled && !Alpine.store('appLock').isLocked) {
                                    console.log('⏰ Inactivity timeout - locking');
                                    Alpine.store('appLock').lock('inactivity timeout (30 min)');
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
