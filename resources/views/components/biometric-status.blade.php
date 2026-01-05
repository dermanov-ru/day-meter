<div x-data="biometricStatusComponent()" x-init="init()" class="biometric-status">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Biometric Security</h3>
        </div>

        <div class="p-6">
            <!-- Status Section -->
            <template x-if="!checkingStatus">
                <div>
                    <!-- Enabled Status -->
                    <template x-if="biometricEnabled">
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Biometric Unlock Enabled</p>
                                    <p class="text-sm text-gray-600">Your account is protected with biometric authentication</p>
                                </div>
                            </div>

                            <button @click="disable()" :disabled="isLoading" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50">
                                <template x-if="!isLoading">
                                    <span>Disable Biometric</span>
                                </template>
                                <template x-if="isLoading">
                                    <span>Disabling...</span>
                                </template>
                            </button>
                        </div>
                    </template>

                    <!-- Disabled Status -->
                    <template x-if="!biometricEnabled">
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Biometric Unlock Not Enabled</p>
                                    <p class="text-sm text-gray-600">Enable biometric authentication for faster access</p>
                                </div>
                            </div>

                            <button @click="setup()" :disabled="!isSupported || isLoading" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 rounded-lg transition-colors disabled:opacity-50">
                                <template x-if="!isLoading">
                                    <span>Enable Biometric</span>
                                </template>
                                <template x-if="isLoading">
                                    <span>Setting up...</span>
                                </template>
                            </button>

                            <template x-if="!isSupported">
                                <p class="mt-2 text-sm text-yellow-600">Biometric authentication is not supported on this device</p>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Loading State -->
            <template x-if="checkingStatus">
                <div class="flex items-center gap-3 justify-center">
                    <div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-gray-600">Loading...</p>
                </div>
            </template>

            <!-- Status Messages -->
            <template x-if="message">
                <div class="mt-4 p-4 rounded-lg" :class="messageType === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'">
                    <p class="text-sm" x-text="message"></p>
                </div>
            </template>
        </div>

        <!-- Info Section -->
        <div class="bg-blue-50 px-6 py-4 border-t border-gray-200">
            <p class="text-sm text-blue-900">
                <strong>What is biometric unlock?</strong><br>
                Biometric unlock lets you quickly access your account using your device's fingerprint or face recognition. Your biometric data never leaves your device—it's used only for local authentication.
            </p>
        </div>
    </div>
</div>

<script>
    function biometricStatusComponent() {
        return {
            biometricEnabled: false,
            isSupported: false,
            checkingStatus: true,
            isLoading: false,
            message: '',
            messageType: 'info',

            async init() {
                await this.checkStatus();
                await this.checkSupport();
            },

            async checkStatus() {
                try {
                    const response = await fetch('/api/biometric/status');
                    const data = await response.json();
                    this.biometricEnabled = data.biometric_enabled || false;
                } catch (error) {
                    console.error('Error checking status:', error);
                } finally {
                    this.checkingStatus = false;
                }
            },

            async checkSupport() {
                try {
                    if (window.biometricService && window.biometricService.isSupported()) {
                        this.isSupported = await window.biometricService.isPlatformAuthenticatorAvailable();
                    }
                } catch (error) {
                    console.error('Error checking biometric support:', error);
                    this.isSupported = false;
                }
            },

            async setup() {
                this.isLoading = true;
                this.message = '';

                try {
                    if (!window.biometricService) {
                        throw new Error('Biometric service not available');
                    }
                    
                    this.message = 'Please verify with your biometric...';
                    this.messageType = 'info';

                    await window.biometricService.register();
                    
                    this.biometricEnabled = true;
                    this.message = 'Biometric successfully enabled!';
                    this.messageType = 'success';
                } catch (error) {
                    this.message = error.message || 'Setup failed. Try again.';
                    this.messageType = 'error';
                } finally {
                    this.isLoading = false;
                }
            },

            async disable() {
                if (!confirm('Disable biometric unlock? You can re-enable it anytime.')) {
                    return;
                }

                this.isLoading = true;
                this.message = '';

                try {
                    const response = await fetch('/api/biometric/disable', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to disable biometric');
                    }

                    this.biometricEnabled = false;
                    this.message = 'Biometric successfully disabled';
                    this.messageType = 'success';
                } catch (error) {
                    this.message = error.message || 'Failed to disable biometric';
                    this.messageType = 'error';
                } finally {
                    this.isLoading = false;
                }
            }
        };
    }
</script>
