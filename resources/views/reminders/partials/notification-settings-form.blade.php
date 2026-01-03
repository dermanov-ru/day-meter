<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Напоминания') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Включите уведомления и установите время напоминания') }}
        </p>
    </header>

    <div class="mt-6 space-y-6" x-data="notificationSettings()">
        <!-- Enable Notifications Toggle -->
        <div class="flex items-center gap-4">
            <label for="notifications-enabled" class="inline-flex items-center cursor-pointer">
                <input
                    id="notifications-enabled"
                    type="checkbox"
                    x-model="settings.enabled"
                    @change="updateSettings()"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                />
                <span class="ml-2 text-sm text-gray-600">{{ __('Включить уведомления') }}</span>
            </label>
        </div>

        <!-- Remind Time Input -->
        <div x-show="settings.enabled" x-transition>
            <x-input-label for="remind-time" :value="__('Время напоминания')" />
            <x-text-input
                id="remind-time"
                type="time"
                x-model="settings.remind_time"
                @change="updateSettings()"
                class="mt-1 block w-full"
            />
            <p class="mt-2 text-sm text-gray-600">{{ __('Время указано в часовом поясе Europe/Moscow') }}</p>
        </div>

        <!-- Status Message -->
        <div x-show="message" x-transition class="mt-4 p-3 rounded"
             :class="messageType === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'">
            <p class="text-sm" x-text="message"></p>
        </div>

        <!-- PWA Installation Status -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-900">
                <strong>{{ __('Статус PWA:') }}</strong>
                <span x-text="pwaStatus"></span>
            </p>
            <button
                x-show="installPrompt"
                @click="installApp()"
                type="button"
                class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700"
            >
                {{ __('Установить приложение') }}
            </button>
        </div>
    </div>
</section>

<script>
function notificationSettings() {
    return {
        settings: {
            enabled: false,
            remind_time: '09:00',
            timezone: 'Europe/Moscow',
        },
        message: '',
        messageType: 'success',
        pwaStatus: 'Проверка...',
        installPrompt: null,

        async init() {
            await this.loadSettings();
            this.checkPWASupport();
            this.setupInstallPrompt();
            this.setupPushNotifications();
        },

        async loadSettings() {
            try {
                const response = await fetch('/api/notifications/settings');
                const data = await response.json();
                if (data.success) {
                    this.settings = data.settings;
                }
            } catch (error) {
                console.error('Failed to load settings:', error);
            }
        },

        async updateSettings() {
            try {
                const response = await fetch('/api/notifications/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.settings),
                });

                const data = await response.json();
                if (data.success) {
                    this.message = 'Настройки сохранены';
                    this.messageType = 'success';
                    
                    if (this.settings.enabled) {
                        await this.subscribeToPush();
                    }
                } else {
                    this.message = 'Ошибка при сохранении настроек';
                    this.messageType = 'error';
                }
            } catch (error) {
                console.error('Failed to update settings:', error);
                this.message = 'Ошибка: ' + error.message;
                this.messageType = 'error';
            }

            setTimeout(() => {
                this.message = '';
            }, 3000);
        },

        async subscribeToPush() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                console.log('Push notifications not supported');
                return;
            }

            try {
                // Request notification permission
                if (Notification.permission === 'denied') {
                    this.message = 'Разрешение на уведомления было отклонено';
                    this.messageType = 'error';
                    return;
                }

                if (Notification.permission !== 'granted') {
                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') {
                        return;
                    }
                }

                // Register service worker
                const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

                // Get push subscription
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(
                        document.querySelector('meta[name="push-public-key"]')?.content || ''
                    ),
                });

                // Send subscription to backend
                await this.sendSubscriptionToServer(subscription);
                this.message = 'Подписка на уведомления успешна';
                this.messageType = 'success';
            } catch (error) {
                console.error('Failed to subscribe to push:', error);
                this.message = 'Ошибка при подписке на уведомления: ' + error.message;
                this.messageType = 'error';
            }

            setTimeout(() => {
                this.message = '';
            }, 3000);
        },

        async sendSubscriptionToServer(subscription) {
            const response = await fetch('/api/notifications/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    p256dh: this.arrayBufferToBase64(subscription.getKey('p256dh')),
                    auth: this.arrayBufferToBase64(subscription.getKey('auth')),
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to save subscription');
            }
        },

        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        },

        arrayBufferToBase64(buffer) {
            let binary = '';
            const bytes = new Uint8Array(buffer);
            for (let i = 0; i < bytes.byteLength; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return window.btoa(binary);
        },

        checkPWASupport() {
            if ('serviceWorker' in navigator && 'PushManager' in window) {
                this.pwaStatus = 'PWA поддерживается';
            } else {
                this.pwaStatus = 'PWA не поддерживается в этом браузере';
            }
        },

        setupInstallPrompt() {
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.installPrompt = e;
            });

            window.addEventListener('appinstalled', () => {
                this.installPrompt = null;
                this.pwaStatus = 'Приложение установлено';
            });
        },

        async installApp() {
            if (!this.installPrompt) {
                return;
            }

            this.installPrompt.prompt();
            const { outcome } = await this.installPrompt.userChoice;
            console.log(`User response: ${outcome}`);
            this.installPrompt = null;
        },

        setupPushNotifications() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.ready.then(() => {
                    console.log('Service Worker ready for push notifications');
                });
            }
        },
    };
}
</script>
