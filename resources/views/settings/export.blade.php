<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Экспорт летописи') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('settings.metrics') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    {{ __('Метрики') }}
                </a>
                <a href="{{ route('settings.categories') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    {{ __('Категории') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Export Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        {{ __('Настройки экспорта') }}
                    </h3>

                    <!-- Preset Buttons -->
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-700 mb-3">{{ __('Быстрые пресеты:') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @if ($allTimeFrom && $allTimeTo)
                                <button type="button" onclick="setPreset('allTime')"
                                        class="px-3 py-2 bg-purple-200 text-purple-800 rounded text-sm hover:bg-purple-300 font-medium">
                                    {{ __('За все время') }}
                                </button>
                            @endif
                            <button type="button" onclick="setPreset('lastMonth')"
                                    class="px-3 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                {{ __('Прошлый месяц') }}
                            </button>
                            <button type="button" onclick="setPreset('thisMonth')"
                                    class="px-3 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                {{ __('Этот месяц') }}
                            </button>
                            <button type="button" onclick="setPreset('quarter')"
                                    class="px-3 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                {{ __('Квартал') }}
                            </button>
                            <button type="button" onclick="setPreset('year')"
                                    class="px-3 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                {{ __('Год') }}
                            </button>
                            <button type="button" onclick="setPreset('lastYear')"
                                    class="px-3 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                {{ __('Прошлый год') }}
                            </button>
                            <button type="button" onclick="setPreset('halfYear')"
                                    class="px-3 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                {{ __('Это полугодие') }}
                            </button>
                            <button type="button" onclick="setPreset('lastHalfYear')"
                                    class="px-3 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                {{ __('Прошлое полугодие') }}
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('settings.export.generate') }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="from" class="block text-sm font-medium text-gray-700">
                                    {{ __('От (From)') }}
                                </label>
                                <input type="date"
                                       id="from"
                                       name="from"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                       value="{{ old('from', $from ?? '') }}"
                                       required>
                                @error('from')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="to" class="block text-sm font-medium text-gray-700">
                                    {{ __('До (To)') }}
                                </label>
                                <input type="date"
                                       id="to"
                                       name="to"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                       value="{{ old('to', $to ?? '') }}"
                                       required>
                                @error('to')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ __('Экспортировать') }}
                        </button>
                    </form>

                    <p class="text-sm text-gray-500 mt-4">
                        {{ __('По умолчанию: полный прошлый месяц') }}
                    </p>
                </div>
            </div>

            <!-- Export Result -->
            @if (isset($content))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ __('Результат экспорта') }}
                            </h3>
                            <div class="flex gap-2">
                                <a href="data:text/plain;charset=utf-8,{{ rawurlencode($content) }}"
                                   download="{{ $filename }}"
                                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                    {{ __('Скачать файл') }}
                                </a>
                                <button onclick="copyToClipboard()"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                    {{ __('Копировать в буфер') }}
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-x-auto">
                            <pre id="content" class="text-sm font-mono text-gray-800 whitespace-pre-wrap break-words">{{ $content }}</pre>
                        </div>

                        <p class="text-sm text-gray-500 mt-4">
                            {{ __('Период: ') }}<strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $from)->format('d.m.Y') }}</strong> {{ __('—') }} <strong>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $to)->format('d.m.Y') }}</strong>
                        </p>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-gray-300">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-gray-600">
                            {{ __('Выберите период и нажмите "Экспортировать"') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function setPreset(preset) {
            const today = new Date();
            let from, to;

            if (preset === 'allTime') {
                // All time - from first entry to last entry
                const allTimeFrom = '{{ $allTimeFrom ?? '' }}';
                const allTimeTo = '{{ $allTimeTo ?? '' }}';
                if (allTimeFrom && allTimeTo) {
                    document.getElementById('from').value = allTimeFrom;
                    document.getElementById('to').value = allTimeTo;
                }
                return;
            } else if (preset === 'lastMonth') {
                // Previous month
                from = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                to = new Date(today.getFullYear(), today.getMonth(), 0);
            } else if (preset === 'thisMonth') {
                // Current month
                from = new Date(today.getFullYear(), today.getMonth(), 1);
                to = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else if (preset === 'quarter') {
                // Current quarter
                const quarter = Math.floor(today.getMonth() / 3);
                from = new Date(today.getFullYear(), quarter * 3, 1);
                to = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
            } else if (preset === 'year') {
                // Current year
                from = new Date(today.getFullYear(), 0, 1);
                to = new Date(today.getFullYear(), 11, 31);
            } else if (preset === 'lastYear') {
                // Previous year
                from = new Date(today.getFullYear() - 1, 0, 1);
                to = new Date(today.getFullYear() - 1, 11, 31);
            } else if (preset === 'halfYear') {
                // Current half-year
                const halfYear = today.getMonth() < 6 ? 0 : 6;
                from = new Date(today.getFullYear(), halfYear, 1);
                to = new Date(today.getFullYear(), halfYear + 6, 0);
            } else if (preset === 'lastHalfYear') {
                // Previous half-year
                const halfYear = today.getMonth() < 6 ? 6 : 0;
                const year = today.getMonth() < 6 ? today.getFullYear() - 1 : today.getFullYear();
                from = new Date(year, halfYear, 1);
                to = new Date(year, halfYear + 6, 0);
            }

            // Format dates as YYYY-MM-DD
            const fromStr = formatDate(from);
            const toStr = formatDate(to);

            // Set input values
            document.getElementById('from').value = fromStr;
            document.getElementById('to').value = toStr;
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function copyToClipboard() {
            const content = document.getElementById('content').textContent;
            navigator.clipboard.writeText(content).then(() => {
                alert('{{ __("Скопировано в буфер обмена") }}');
            }).catch(() => {
                alert('{{ __("Ошибка копирования") }}');
            });
        }
    </script>
</x-app-layout>
