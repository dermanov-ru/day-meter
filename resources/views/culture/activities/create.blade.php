<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Добавить активность') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-0">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('activities.store') }}">
                        @csrf

                        <!-- Type -->
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Тип') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" class="w-full rounded-md border-gray-300 shadow-sm" required onchange="updateFormatOptions()">
                                <option value="">-- Выберите тип --</option>
                                <option value="movie" {{ old('type') == 'movie' ? 'selected' : '' }}>🎬 Фильм</option>
                                <option value="book" {{ old('type') == 'book' ? 'selected' : '' }}>📚 Книга</option>
                                <option value="theater" {{ old('type') == 'theater' ? 'selected' : '' }}>🎭 Театр</option>
                                <option value="series" {{ old('type') == 'series' ? 'selected' : '' }}>📺 Сериал</option>
                                <option value="concert" {{ old('type') == 'concert' ? 'selected' : '' }}>🎵 Концерт</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Название') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Format -->
                        <div class="mb-4">
                            <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Формат') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="format" name="format" class="w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">-- Выберите формат --</option>
                                <option value="cinema" {{ old('format') == 'cinema' ? 'selected' : '' }}>Кинотеатр</option>
                                <option value="streaming" {{ old('format') == 'streaming' ? 'selected' : '' }}>Стриминг</option>
                                <option value="paper" {{ old('format') == 'paper' ? 'selected' : '' }}>Бумажная</option>
                                <option value="electronic" {{ old('format') == 'electronic' ? 'selected' : '' }}>Электронная</option>
                                <option value="audio" {{ old('format') == 'audio' ? 'selected' : '' }}>Аудиокнига</option>
                                <option value="offline" {{ old('format') == 'offline' ? 'selected' : '' }}>Оффлайн</option>
                            </select>
                            @error('format')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date/Time (shown based on type) -->
                        <div id="date-instant" class="mb-4 hidden">
                            <label for="date_at" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Дата и время') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="date_at" name="date_at" value="{{ old('date_at', now()->format('Y-m-d\TH:i')) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                            @error('date_at')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="date-duration" class="mb-4 hidden">
                            <label for="date_start" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Дата начала') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="date_start" name="date_start" value="{{ old('date_start', now()->toDateString()) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                            @error('date_start')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Impressions -->
                        <div class="mb-4">
                            <label for="impressions" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Впечатления') }}
                            </label>
                            <textarea id="impressions" name="impressions" rows="4" placeholder="Ваши ощущения..." class="w-full rounded-md border-gray-300 shadow-sm">{{ old('impressions') }}</textarea>
                            @error('impressions')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rating -->
                        <div class="mb-6">
                            <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Оценка (0-10)') }}
                            </label>
                            <input type="number" id="rating" name="rating" min="0" max="10" step="0.5" value="{{ old('rating') }}" class="w-full rounded-md border-gray-300 shadow-sm">
                            @error('rating')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit buttons -->
                        <div class="flex gap-3 justify-end">
                            <a href="{{ route('activities.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                {{ __('Отмена') }}
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                {{ __('Создать') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFormatOptions() {
            const type = document.getElementById('type').value;
            const dateInstant = document.getElementById('date-instant');
            const dateDuration = document.getElementById('date-duration');
            const formatSelect = document.getElementById('format');

            // Determine if instant or duration
            const instantTypes = ['movie', 'theater', 'concert'];
            const isInstant = instantTypes.includes(type);

            // Show/hide date fields
            if (isInstant) {
                dateInstant.classList.remove('hidden');
                dateDuration.classList.add('hidden');
            } else {
                dateInstant.classList.add('hidden');
                dateDuration.classList.remove('hidden');
            }

            // Update format options based on type
            let options = [];
            switch(type) {
                case 'movie':
                    options = ['cinema', 'streaming'];
                    break;
                case 'book':
                    options = ['paper', 'electronic', 'audio'];
                    break;
                case 'theater':
                    options = ['offline'];
                    break;
                case 'series':
                    options = ['streaming', 'paper', 'electronic', 'audio'];
                    break;
                case 'concert':
                    options = ['offline'];
                    break;
            }

            // Update format options
            Array.from(formatSelect.options).forEach(option => {
                if (option.value === '') {
                    option.hidden = false;
                } else {
                    option.hidden = !options.includes(option.value);
                }
            });

            // Reset format if current is not available
            if (!options.includes(formatSelect.value)) {
                formatSelect.value = '';
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', updateFormatOptions);
    </script>
</x-app-layout>
