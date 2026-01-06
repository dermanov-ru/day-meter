<x-app-layout>
    <x-slot name="header">
        <!-- Desktop: Show title and controls -->
        <div class="hidden sm:flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $disease->title }}
                </h2>
                @if ($disease->category)
                    <p class="text-sm text-gray-600 mt-1">{{ $disease->category }}</p>
                @endif
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('diseases.edit', $disease) }}" class="px-3 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                    {{ __('Изменить') }}
                </a>
                @if ($disease->isActive())
                    <form method="POST" action="{{ route('diseases.close', $disease) }}" style="display:inline;" onsubmit="return confirm('Завершить болезнь?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-3 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 text-sm">
                            {{ __('Завершить') }}
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('diseases.destroy', $disease) }}" style="display:inline;" onsubmit="return confirm('Удалить болезнь и все её записи?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                        {{ __('Удалить') }}
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Mobile: Show title and controls in nav -->
        <div class="sm:hidden w-full">
            <div class="pb-3">
                <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                    {{ $disease->title }}
                </h2>
                @if ($disease->category)
                    <p class="text-xs text-gray-600 mt-1">{{ $disease->category }}</p>
                @endif
            </div>
            <div class="flex gap-2 flex-wrap pt-2 border-t border-gray-200">
                <a href="{{ route('diseases.edit', $disease) }}" class="px-2 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-xs">
                    {{ __('Изменить') }}
                </a>
                @if ($disease->isActive())
                    <form method="POST" action="{{ route('diseases.close', $disease) }}" style="display:inline;" onsubmit="return confirm('Завершить болезнь?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-2 py-1 bg-orange-500 text-white rounded-md hover:bg-orange-600 text-xs">
                            {{ __('Завершить') }}
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('diseases.destroy', $disease) }}" style="display:inline;" onsubmit="return confirm('Удалить болезнь и все её записи?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 text-xs">
                        {{ __('Удалить') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>
    <div class="py-12 px-4 sm:px-0">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Status message -->
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Disease Meta Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Статус</p>
                            <p class="font-semibold text-lg">
                                @if ($disease->isActive())
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded">Активная</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-sm rounded">Завершена</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Начало</p>
                            <p class="font-semibold">{{ $disease->start_date->format('d.m.Y') }}</p>
                        </div>
                        @if ($disease->end_date)
                            <div>
                                <p class="text-xs text-gray-600 uppercase">Завершена</p>
                                <p class="font-semibold">{{ $disease->end_date->format('d.m.Y') }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Длительность</p>
                            <p class="font-semibold">{{ $disease->duration_days }} дн.</p>
                        </div>
                    </div>

                    @if ($disease->description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-700">{{ $disease->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Add Note Button -->
            <div class="mb-6 flex justify-center">
                <button onclick="document.getElementById('note-form').classList.toggle('hidden')" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    {{ __('+ Добавить запись') }}
                </button>
            </div>

            <!-- Add Note Form (Hidden by default) -->
            <div id="note-form" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 hidden">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">{{ __('Новая запись') }}</h3>
                    <form id="note-form-element" method="POST" action="{{ route('diseases.notes.store', $disease) }}">
                        @csrf

                        <!-- Date & Time -->
                        <div class="mb-4">
                            <label for="datetime" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Дата и время') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local"
                                   id="datetime"
                                   name="datetime"
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm"
                                   required>
                            @error('datetime')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Тип записи') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" class="w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="free">📝 Запись</option>
                                <option value="symptom">🤒 Симптом</option>
                                <option value="condition">📊 Состояние</option>
                                <option value="medication">💊 Лекарство</option>
                                <option value="treatment">💉 Лечение</option>
                                <option value="doctor">👨‍⚕️ У врача</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Text -->
                        <div class="mb-4">
                            <label for="text" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Запись') }}
                            </label>
                            <textarea id="text"
                                      name="text"
                                      rows="3"
                                      placeholder="Описание..."
                                      class="w-full rounded-md border-gray-300 shadow-sm">{{ old('text') }}</textarea>
                            @error('text')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Temperature -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Температура (°C)') }}
                                </label>
                                <input type="number"
                                       id="temperature"
                                       name="temperature"
                                       step="0.1"
                                       min="35"
                                       max="42"
                                       value="{{ old('temperature') }}"
                                       placeholder="36.6"
                                       class="w-full rounded-md border-gray-300 shadow-sm">
                                @error('temperature')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Pain Level -->
                            <div>
                                <label for="pain_level" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Уровень боли (0-10)') }}
                                </label>
                                <input type="number"
                                       id="pain_level"
                                       name="pain_level"
                                       min="0"
                                       max="10"
                                       value="{{ old('pain_level') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm">
                                @error('pain_level')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- State Flag -->
                        <div class="mb-6">
                            <label for="state_flag" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Динамика') }}
                            </label>
                            <select id="state_flag" name="state_flag" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">— нет</option>
                                <option value="worse">📉 Ухудшение</option>
                                <option value="better">📈 Улучшение</option>
                            </select>
                            @error('state_flag')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit buttons -->
                        <div class="flex gap-3 justify-end">
                            <button type="button" 
                                    onclick="document.getElementById('note-form').classList.toggle('hidden')"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                {{ __('Отмена') }}
                            </button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                {{ __('Сохранить') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notes Timeline -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($notes->count() > 0)
                        <h3 class="font-semibold text-lg mb-6">{{ __('Хронология') }}</h3>
                        <div class="space-y-4">
                            @foreach ($notes as $note)
                                <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <span class="text-2xl mr-2">{{ $note->type_emoji }}</span>
                                            <span class="font-semibold">{{ $note->type_display }}</span>
                                            <span class="text-xs text-gray-600 ml-2">
                                                {{ $note->formatted_date }} {{ $note->formatted_time }}
                                            </span>
                                        </div>
                                        <form method="POST" action="{{ route('diseases.notes.destroy', [$disease, $note]) }}" style="display:inline;" onsubmit="return confirm('Удалить запись?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm cursor-pointer">×</button>
                                        </form>
                                    </div>

                                    @if ($note->text)
                                        <p class="text-gray-900 mb-3">{{ $note->text }}</p>
                                    @endif

                                    <div class="flex gap-4 text-xs text-gray-600">
                                        @if ($note->temperature)
                                            <span>🌡️ {{ $note->temperature }}°C</span>
                                        @endif
                                        @if ($note->pain_level !== null)
                                            <span>💔 Боль: {{ $note->pain_level }}/10</span>
                                        @endif
                                        @if ($note->state_flag)
                                            <span>
                                                @if ($note->state_flag === 'better')
                                                    📈 Улучшение
                                                @else
                                                    📉 Ухудшение
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-8">{{ __('Записей нет') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Reset form after successful submission
        document.addEventListener('DOMContentLoaded', function() {
            // If there's a status message, the form was submitted successfully
            const statusMessage = document.querySelector('[class*="bg-green"]');
            if (statusMessage) {
                // Reset the note form
                const form = document.getElementById('note-form-element');
                if (form) {
                    form.reset();
                    // Update datetime to current time
                    const datetimeInput = document.getElementById('datetime');
                    if (datetimeInput) {
                        const now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        datetimeInput.value = now.toISOString().slice(0, 16);
                    }
                }
                // Hide the form
                const noteFormContainer = document.getElementById('note-form');
                if (noteFormContainer && !noteFormContainer.classList.contains('hidden')) {
                    noteFormContainer.classList.add('hidden');
                }
            }
        });
    </script>
</x-app-layout>
