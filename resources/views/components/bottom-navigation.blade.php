<!-- Fixed Bottom Navigation (Mobile only) -->
<nav class="sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-30" style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom))">
    <div class="flex items-center justify-center h-12 relative px-1">
        <!-- Left section (2 items) -->
        <div class="flex flex-1 items-center justify-around gap-1">
            <!-- Финансы -->
            <x-bottom-nav-item
                :active="false"
                :href="null"
                label="Финансы"
                icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
            />

            <!-- Болезни -->
            <x-bottom-nav-item
                :active="false"
                :href="null"
                label="Болезни"
                icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>'
            />
        </div>

        <!-- Center FAB (Floating Action Button) -->
        <div class="absolute left-1/2 transform -translate-x-1/2 -top-3">
            <a href="{{ route('entry.show') }}" class="flex items-center justify-center w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg transition-all duration-200 transform hover:scale-110 focus:outline-none focus:ring-4 focus:ring-blue-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </a>
        </div>

        <!-- Right section (2 items) -->
        <div class="flex flex-1 items-center justify-around gap-1">
            <!-- Культурные события -->
            <x-bottom-nav-item
                :active="false"
                :href="null"
                label="Культура"
                icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
            />

            <!-- Журнал -->
            <x-bottom-nav-item
                :active="request()->routeIs('chronicle.*')"
                :href="route('chronicle.index')"
                label="Журнал"
                icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" /></svg>'
            />
        </div>
    </div>
</nav>

<!-- Content padding for mobile to account for fixed bottom nav -->
<style>
    @media (max-width: 640px) {
        .app-content-wrapper main {
            padding-bottom: 5.5rem;
        }
    }
</style>
