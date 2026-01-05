<div x-data="biometricSetupComponent()" x-init="init()" class="biometric-setup">
    <!-- Setup Modal -->
    <template x-if="showModal">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center" @click.self="close()">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-white text-xl font-bold">Enhance Security</h2>
                    <p class="text-blue-100 text-sm mt-1">Enable biometric unlock for quick access</p>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Feature List -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700 text-sm">Unlock with your face or fingerprint</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700 text-sm">Stay logged in, secure access to your data</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700 text-sm">You can disable it anytime in settings</span>
                        </div>
                    </div>

                    <!-- Status/Error Messages -->
                    <template x-if="message">
                        <div class="mb-6 p-4 rounded-lg" :class="messageType === 'error' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700'">
                            <p x-text="message" class="text-sm"></p>
                        </div>
                    </template>

                    <!-- Availability Check -->
                    <template x-if="checkingSupport">
                        <div class="mb-6 text-center">
                            <div class="inline-block w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mb-2"></div>
                            <p class="text-sm text-gray-600">Checking device capabilities...</p>
                        </div>
                    </template>

                    <template x-if="!checkingSupport && !isSupported">
                        <div class="mb-6 p-4 bg-yellow-50 text-yellow-700 rounded-lg text-sm">
                            Biometric authentication is not supported on this device.
                        </div>
                    </template>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-4 flex gap-3">
                    <button @click="close()" class="flex-1 px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                        Skip for now
                    </button>
                    <button @click="setupBiometric()" :disabled="isLoading || checkingSupport || !isSupported" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                        <template x-if="!isLoading">
                            <span>Enable</span>
                        </template>
                        <template x-if="isLoading">
                            <span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span>Setting up...</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    function biometricSetupComponent() {
        return {
            showModal: false,
            isLoading: false,
            isSupported: false,
            checkingSupport: false,
            message: '',
            messageType: 'info',

            async init() {
                // Check if we should show setup modal
                const hasSetup = localStorage.getItem('biometric_setup_dismissed');
                if (!hasSetup) {
                    // Check biometric support
                    await this.checkSupport();
                    if (this.isSupported) {
                        this.showModal = true;
                    }
                }
            },

            async checkSupport() {
                this.checkingSupport = true;
                try {
                    // Use global biometric service from window
                    if (window.biometricService && window.biometricService.isSupported()) {
                        this.isSupported = await window.biometricService.isPlatformAuthenticatorAvailable();
                    }
                } catch (error) {
                    console.error('Error checking biometric support:', error);
                    this.isSupported = false;
                } finally {
                    this.checkingSupport = false;
                }
            },

            async setupBiometric() {
                this.isLoading = true;
                this.message = '';

                try {
                    // Use global biometric service from window
                    if (!window.biometricService) {
                        throw new Error('Biometric service not available');
                    }
                    
                    this.message = 'Please verify with your biometric...';
                    this.messageType = 'info';

                    await window.biometricService.register();
                    
                    this.message = 'Biometric successfully enabled!';
                    this.messageType = 'success';
                    
                    localStorage.setItem('biometric_setup_dismissed', 'true');
                    
                    // Close modal after success
                    setTimeout(() => {
                        this.close();
                    }, 2000);
                } catch (error) {
                    this.message = error.message || 'Setup failed. Try again.';
                    this.messageType = 'error';
                } finally {
                    this.isLoading = false;
                }
            },

            close() {
                this.showModal = false;
                localStorage.setItem('biometric_setup_dismissed', 'true');
            }
        };
    }
</script>
