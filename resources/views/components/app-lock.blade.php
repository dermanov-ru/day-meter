<div class="app-lock-wrapper" x-data="appLockComponent()" x-init="init()" @keydown.escape="cancel()">
    <template x-if="isLocked">
        <div class="fixed inset-0 bg-gradient-to-br from-slate-900 to-slate-800 z-50 flex items-center justify-center">
            <div class="text-center">
                <!-- Lock Icon -->
                <div class="mb-6">
                    <svg class="w-16 h-16 mx-auto text-blue-400 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1C6.48 1 2 5.48 2 11v8c0 .55.45 1 1 1h1v3c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-3h8v3c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-3h1c.55 0 1-.45 1-1v-8c0-5.52-4.48-10-10-10zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold text-white mb-2">{{ config('app.name') }}</h1>
                <p class="text-slate-300 mb-8">Unlock to continue</p>

                <!-- Status Message -->
                <template x-if="message">
                    <div class="mb-6 p-4 rounded-lg" :class="messageType === 'error' ? 'bg-red-900/20 text-red-300' : 'bg-green-900/20 text-green-300'">
                        <p x-text="message"></p>
                    </div>
                </template>

                <!-- Unlock Button -->
                <button @click="unlock()" :disabled="isLoading" class="mb-4 px-8 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-700 text-white rounded-lg font-semibold transition-colors duration-200 flex items-center justify-center gap-2 mx-auto">
                    <template x-if="!isLoading">
                        <span>🔓 Unlock with Biometric</span>
                    </template>
                    <template x-if="isLoading">
                        <span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        <span>Verifying...</span>
                    </template>
                </button>

                <!-- Fallback Option -->
                <button @click="logout()" class="text-slate-300 hover:text-white text-sm transition-colors">
                    Sign out
                </button>
            </div>

            <!-- Background Animation -->
            <style>
                .app-lock-wrapper {
                    --gradient-x: 0%;
                    animation: gradient-shift 8s ease infinite;
                }

                @keyframes gradient-shift {
                    0%, 100% { --gradient-x: 0%; }
                    50% { --gradient-x: 100%; }
                }
            </style>
        </div>
    </template>
</div>

<script>
    function appLockComponent() {
        return {
            isLocked: Alpine.store('appLock').isLocked,
            isLoading: false,
            message: '',
            messageType: 'info',

            init() {
                // Clear any previous messages on init
                this.message = '';
                this.messageType = 'info';
                
                // Subscribe to app lock store changes
                this.$watch('$store.appLock.isLocked', (value) => {
                    this.isLocked = value;
                });
            },

            async unlock() {
                this.isLoading = true;
                this.message = '';

                try {
                    if (!window.biometricService) {
                        throw new Error('Biometric service not available');
                    }
                    await window.biometricService.unlock();
                    
                    // Mark as unlocked
                    Alpine.store('appLock').unlock();
                    this.isLocked = false;
                    
                    this.message = 'Unlocked successfully!';
                    this.messageType = 'success';
                } catch (error) {
                    this.message = error.message || 'Unlock failed. Try again.';
                    this.messageType = 'error';
                } finally {
                    this.isLoading = false;
                }
            },

            logout() {
                if (confirm('Sign out from {{ config("app.name") }}?')) {
                    window.location.href = '/logout';
                }
            },

            cancel() {
                // ESC key - no-op, user must unlock or sign out
            }
        };
    }
</script>
