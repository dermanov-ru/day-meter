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

            <!-- AI Prompts Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        🧠 {{ __('Проанализировать с ИИ') }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">
                        {{ __('Скопируйте экспортированные данные и один из промтов ниже, затем вставьте их в любой ИИ (ChatGPT, Claude и т.д.).') }}
                    </p>

                    <div class="space-y-3" id="prompts-container">
                        <!-- Prompt 1: General Analysis -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button type="button" onclick="togglePrompt(1)" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-150 text-left flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="accordion-chevron transition-transform duration-300" style="transform: rotate(0deg);">▼</span>
                                    <span class="font-medium text-gray-800">1. {{ __('Общий анализ периода') }}</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ __('Рекомендуемый') }}</span>
                            </button>
                            <div class="prompt-content hidden bg-white p-4">
                                <textarea readonly class="w-full h-64 p-3 border border-gray-200 rounded bg-gray-50 text-sm font-mono text-gray-700" id="prompt-1">Ниже — моя личная летопись за выбранный период.
Это дневные записи с метриками, категориями и короткими комментариями.

Задача:
1. Сделай краткое summary периода (5–7 предложений).
2. Выдели основные повторяющиеся паттерны.
3. Обрати внимание на связи между:
   - работой
   - семьёй
   - энергией
   - эмоциональным состоянием.
4. Не давай советов и не оценивай — только наблюдения и выводы.

[ЭКСПОРТИРОВАННЫЕ ДАННЫЕ]</textarea>
                                <button onclick="copyPrompt(1, this)" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                    📋 {{ __('Скопировать промт') }}
                                </button>
                            </div>
                        </div>

                        <!-- Prompt 2: Correlations and Triggers -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button type="button" onclick="togglePrompt(2)" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-150 text-left flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="accordion-chevron transition-transform duration-300" style="transform: rotate(0deg);">▼</span>
                                    <span class="font-medium text-gray-800">2. {{ __('Корреляции и триггеры') }}</span>
                                </div>
                            </button>
                            <div class="prompt-content hidden bg-white p-4">
                                <textarea readonly class="w-full h-64 p-3 border border-gray-200 rounded bg-gray-50 text-sm font-mono text-gray-700" id="prompt-2">Ниже — мои дневные записи за период.
В них есть:
- булевые и числовые метрики,
- категории жизни,
- комментарии к отдельным событиям.

Задача:
1. Найди возможные корреляции между метриками.
2. Определи частые триггеры ухудшения состояния.
3. Отдельно отметь факторы, которые чаще всего совпадают с:
   - раздражением
   - криками
   - низким настроением.
4. Формулируй выводы осторожно, без категоричных утверждений.

[ЭКСПОРТИРОВАННЫЕ ДАННЫЕ]</textarea>
                                <button onclick="copyPrompt(2, this)" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                    📋 {{ __('Скопировать промт') }}
                                </button>
                            </div>
                        </div>

                        <!-- Prompt 3: Energy and Load -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button type="button" onclick="togglePrompt(3)" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-150 text-left flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="accordion-chevron transition-transform duration-300" style="transform: rotate(0deg);">▼</span>
                                    <span class="font-medium text-gray-800">3. {{ __('Энергия и нагрузка') }}</span>
                                </div>
                            </button>
                            <div class="prompt-content hidden bg-white p-4">
                                <textarea readonly class="w-full h-64 p-3 border border-gray-200 rounded bg-gray-50 text-sm font-mono text-gray-700" id="prompt-3">Ниже — летопись моих дней за период.

Задача:
1. Проанализируй уровень энергии и усталости.
2. Посмотри, какие события или метрики чаще всего связаны с истощением.
3. Отметь, что чаще всего совпадает с восстановлением ресурса.
4. Сформулируй выводы в формате наблюдений, а не рекомендаций.

[ЭКСПОРТИРОВАННЫЕ ДАННЫЕ]</textarea>
                                <button onclick="copyPrompt(3, this)" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                    📋 {{ __('Скопировать промт') }}
                                </button>
                            </div>
                        </div>

                        <!-- Prompt 4: Family and Behavior -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button type="button" onclick="togglePrompt(4)" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-150 text-left flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="accordion-chevron transition-transform duration-300" style="transform: rotate(0deg);">▼</span>
                                    <span class="font-medium text-gray-800">4. {{ __('Семья и поведение') }}</span>
                                </div>
                            </button>
                            <div class="prompt-content hidden bg-white p-4">
                                <textarea readonly class="w-full h-64 p-3 border border-gray-200 rounded bg-gray-50 text-sm font-mono text-gray-700" id="prompt-4">Ниже — мои дневные записи с метриками, связанными с семьёй и домом.

Задача:
1. Проанализируй моё поведение в семейном контексте.
2. Отметь периоды с более спокойными и вовлечёнными днями.
3. Посмотри, что чаще всего совпадает с напряжением или криками.
4. Описывай наблюдения нейтрально, без осуждения.

[ЭКСПОРТИРОВАННЫЕ ДАННЫЕ]</textarea>
                                <button onclick="copyPrompt(4, this)" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                    📋 {{ __('Скопировать промт') }}
                                </button>
                            </div>
                        </div>

                        <!-- Prompt 5: Brief Summary -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button type="button" onclick="togglePrompt(5)" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-150 text-left flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="accordion-chevron transition-transform duration-300" style="transform: rotate(0deg);">▼</span>
                                    <span class="font-medium text-gray-800">5. {{ __('Краткое резюме') }}</span>
                                </div>
                            </button>
                            <div class="prompt-content hidden bg-white p-4">
                                <textarea readonly class="w-full h-64 p-3 border border-gray-200 rounded bg-gray-50 text-sm font-mono text-gray-700" id="prompt-5">Ниже — мои дневные записи за период.

Задача:
1. Сформулировать краткое текстовое резюме периода.
2. Без анализа причин и следствий.
3. Просто описать, каким был этот период в целом.

Используй спокойный, нейтральный тон.

[ЭКСПОРТИРОВАННЫЕ ДАННЫЕ]</textarea>
                                <button onclick="copyPrompt(5, this)" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                    📋 {{ __('Скопировать промт') }}
                                </button>
                            </div>
                        </div>

                        <!-- Prompt 6: Work Environment -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button type="button" onclick="togglePrompt(6)" class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-150 text-left flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="accordion-chevron transition-transform duration-300" style="transform: rotate(0deg);">▼</span>
                                    <span class="font-medium text-gray-800">6. {{ __('Обстановка на работе') }}</span>
                                </div>
                            </button>
                            <div class="prompt-content hidden bg-white p-4">
                                <textarea readonly class="w-full h-64 p-3 border border-gray-200 rounded bg-gray-50 text-sm font-mono text-gray-700" id="prompt-6">Ниже — мои дневные записи с акцентом на рабочую деятельность и профессиональный контекст.

Задача:
1. Проанализируй мою работоспособность и вовлечённость.
2. Отметь периоды с больший и меньшей продуктивностью.
3. Посмотри, что совпадает с:
   - стрессом и усталостью на работе
   - хорошим настроением и вдохновением
   - конфликтами или напряжением в коллективе.
4. Определи факторы, которые влияют на рабочую атмосферу.
5. Описывай наблюдения в профессиональном ключе, без обвинений.

[ЭКСПОРТИРОВАННЫЕ ДАННЫЕ]</textarea>
                                <button onclick="copyPrompt(6, this)" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                    📋 {{ __('Скопировать промт') }}
                                </button>
                            </div>
                        </div>
                    </div>
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

        function togglePrompt(promptNum) {
            const button = event.currentTarget;
            const content = button.nextElementSibling;
            const chevron = button.querySelector('.accordion-chevron');
            const isOpen = !content.classList.contains('hidden');

            // Close all other prompts
            document.querySelectorAll('.prompt-content').forEach(el => {
                if (el !== content) {
                    el.classList.add('hidden');
                    const otherChevron = el.previousElementSibling.querySelector('.accordion-chevron');
                    if (otherChevron) otherChevron.style.transform = 'rotate(0deg)';
                }
            });

            // Toggle current prompt
            if (isOpen) {
                content.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            } else {
                content.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            }
        }

        function copyPrompt(promptNum, buttonElement) {
            const textarea = document.getElementById('prompt-' + promptNum);
            if (!textarea) {
                alert('{{ __("Ошибка: промт не найден") }}');
                return;
            }

            const text = textarea.textContent;
            
            // Try modern clipboard API first
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    showCopySuccess(buttonElement);
                }).catch((err) => {
                    console.error('Clipboard error:', err);
                    fallbackCopy(text, buttonElement);
                });
            } else {
                // Fallback for older browsers
                fallbackCopy(text, buttonElement);
            }
        }

        function fallbackCopy(text, buttonElement) {
            try {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                const successful = document.execCommand('copy');
                document.body.removeChild(textarea);
                
                if (successful) {
                    showCopySuccess(buttonElement);
                } else {
                    alert('{{ __("Ошибка копирования") }}');
                }
            } catch (err) {
                console.error('Fallback copy error:', err);
                alert('{{ __("Ошибка копирования") }}');
            }
        }

        function showCopySuccess(buttonElement) {
            if (!buttonElement) return;
            
            const originalText = buttonElement.innerHTML;
            buttonElement.innerHTML = '{{ __("✓ Скопировано") }}';
            buttonElement.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            buttonElement.classList.add('bg-green-600', 'hover:bg-green-700');

            setTimeout(() => {
                buttonElement.innerHTML = originalText;
                buttonElement.classList.remove('bg-green-600', 'hover:bg-green-700');
                buttonElement.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        }
    </script>

    <style>
        .prompt-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
        }

        .prompt-content.hidden {
            max-height: 0;
            opacity: 0;
        }

        .prompt-content:not(.hidden) {
            max-height: 500px;
            opacity: 1;
        }

        .accordion-chevron {
            display: inline-block;
            transition: transform 0.3s ease;
        }
    </style>
</x-app-layout>
