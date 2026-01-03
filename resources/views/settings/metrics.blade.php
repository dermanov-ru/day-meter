<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Управление метриками') }}
            </h2>
            <a href="{{ route('settings.categories') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                {{ __('Категории') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Add New Metric Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        {{ __('Добавить новую метрику') }}
                    </h3>

                    <form method="POST" action="{{ route('settings.metrics.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="key" class="block text-sm font-medium text-gray-700">
                                {{ __('Ключ (Key) - автозаполнение') }}
                            </label>
                            <input type="text"
                                   id="key"
                                   name="key"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   required
                                   @error('key') is-invalid @enderror
                                   value="{{ old('key') }}">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Будет автоматически заполнен исходя из названия') }}</p>
                            @error('key')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                {{ __('Название') }}
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   required
                                   @error('title') is-invalid @enderror
                                   value="{{ old('title') }}"
                                   onchange="autoGenerateKey('title', 'key')">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                {{ __('Тип') }}
                            </label>
                            <select id="type"
                                    name="type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                    @error('type') is-invalid @enderror
                                    onchange="toggleScaleFields(this.value)">
                                <option value="">{{ __('Выберите тип') }}</option>
                                <option value="boolean" @selected(old('type') === 'boolean')>{{ __('Да/Нет') }}</option>
                                <option value="scale" @selected(old('type') === 'scale')>{{ __('Шкала') }}</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="scale-fields" class="hidden space-y-4">
                            <div>
                                <label for="min_value" class="block text-sm font-medium text-gray-700">
                                    {{ __('Минимум') }}
                                </label>
                                <input type="number"
                                       id="min_value"
                                       name="min_value"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                       @error('min_value') is-invalid @enderror
                                       value="{{ old('min_value') }}">
                                @error('min_value')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_value" class="block text-sm font-medium text-gray-700">
                                    {{ __('Максимум') }}
                                </label>
                                <input type="number"
                                       id="max_value"
                                       name="max_value"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                       @error('max_value') is-invalid @enderror
                                       value="{{ old('max_value') }}">
                                @error('max_value')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="metric_category_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Категория') }}
                            </label>
                            <select id="metric_category_id"
                                    name="metric_category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                    @error('metric_category_id') is-invalid @enderror>
                                <option value="">{{ __('Выберите категорию') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('metric_category_id') == $category->id)>
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('metric_category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700">
                                {{ __('Порядок сортировки') }}
                            </label>
                            <input type="number"
                                   id="sort_order"
                                   name="sort_order"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   @error('sort_order') is-invalid @enderror
                                   value="{{ old('sort_order', $nextSort ?? 10) }}">
                            @error('sort_order')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ __('Добавить метрику') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Metrics List by Category -->
            <div class="space-y-6">
                @foreach ($categoriesWithMetrics as $category)
                    @if ($category->metrics->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <div class="flex items-center gap-3 mb-4 pb-2 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $category->title }}
                                    </h3>
                                    @if ($category->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                                            {{ __('Активна') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
                                            {{ __('Неактивна') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="space-y-4">
                                    @foreach ($category->metrics as $metric)
                                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <h4 class="font-medium text-gray-800">{{ $metric->title }}</h4>
                                                    @if ($metric->is_active)
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                                                            {{ __('Активна') }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
                                                            {{ __('Неактивна') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-500 mt-2">
                                                    {{ __('Ключ: ') }}<code>{{ $metric->key }}</code>
                                                </p>
                                                @if ($metric->type === 'scale')
                                                    <p class="text-sm text-gray-500">
                                                        {{ __('Шкала: ') }}{{ $metric->min_value }}-{{ $metric->max_value }}
                                                    </p>
                                                @else
                                                    <p class="text-sm text-gray-500">
                                                        {{ __('Тип: Да/Нет') }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="ml-4">
                                                <button onclick="showEditForm({{ $metric->id }})"
                                                        class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                                    {{ __('Редактировать') }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Edit Form (Hidden by default) -->
                                        <div id="edit-form-{{ $metric->id }}" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <form method="POST" action="{{ route('settings.metrics.update', $metric) }}" class="space-y-4">
                                                @csrf
                                                @method('PATCH')

                                                <div>
                                                    <label for="edit_title_{{ $metric->id }}" class="block text-sm font-medium text-gray-700">
                                                        {{ __('Название') }}
                                                    </label>
                                                    <input type="text"
                                                           id="edit_title_{{ $metric->id }}"
                                                           name="title"
                                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                           value="{{ $metric->title }}"
                                                           required>
                                                </div>

                                                <div>
                                                    <label for="edit_category_{{ $metric->id }}" class="block text-sm font-medium text-gray-700">
                                                        {{ __('Категория') }}
                                                    </label>
                                                    <select id="edit_category_{{ $metric->id }}"
                                                            name="metric_category_id"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                            required>
                                                        @foreach ($categories as $cat)
                                                            <option value="{{ $cat->id }}" @selected($metric->metric_category_id === $cat->id)>
                                                                {{ $cat->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                </div>

                                <div>
                                    <label for="edit_sort_order_{{ $metric->id }}" class="block text-sm font-medium text-gray-700">
                                        {{ __('Порядок сортировки') }}
                                    </label>
                                    <input type="number"
                                           id="edit_sort_order_{{ $metric->id }}"
                                           name="sort_order"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                           value="{{ $metric->sort_order }}">
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox"
                                           id="edit_active_{{ $metric->id }}"
                                           name="is_active"
                                           value="1"
                                           @checked($metric->is_active)
                                           class="rounded">
                                    <label for="edit_active_{{ $metric->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ __('Активна') }}
                                    </label>
                                </div>

                                <div class="flex gap-2">
                                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                                        {{ __('Сохранить') }}
                                                    </button>
                                                    <button type="button"
                                                            onclick="hideEditForm({{ $metric->id }})"
                                                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                                        {{ __('Отмена') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Cyrillic to Latin transliteration map
        const cyrillicMap = {
            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo',
            'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',
            'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
            'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'sch',
            'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya'
        };

        function transliterate(text) {
            return text.toLowerCase().split('').map(char => cyrillicMap[char] || char).join('');
        }

        function autoGenerateKey(titleId, keyId) {
            const titleInput = document.getElementById(titleId);
            const keyInput = document.getElementById(keyId);
            if (titleInput && keyInput && titleInput.value) {
                const slug = transliterate(titleInput.value)
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '')
                    .toLowerCase();
                keyInput.value = slug;
            }
        }

        function toggleScaleFields(type) {
            const scaleFields = document.getElementById('scale-fields');
            if (type === 'scale') {
                scaleFields.classList.remove('hidden');
            } else {
                scaleFields.classList.add('hidden');
            }
        }

        function showEditForm(metricId) {
            document.getElementById('edit-form-' + metricId).classList.remove('hidden');
        }

        function hideEditForm(metricId) {
            document.getElementById('edit-form-' + metricId).classList.add('hidden');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            if (typeSelect && typeSelect.value) {
                toggleScaleFields(typeSelect.value);
            }
        });
    </script>
</x-app-layout>
