<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Редактировать активность') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-0">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('activities.update', $activity) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Type (disabled for editing) -->
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Тип') }}
                            </label>
                            <input type="text" value="{{ $activity->getTypeDisplayAttribute() }}" class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100" disabled>
                            <input type="hidden" name="type" value="{{ $activity->type }}">
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Название') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title', $activity->title) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
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
                                <option value="cinema" {{ old('format', $activity->format) == 'cinema' ? 'selected' : '' }}>Кинотеатр</option>
                                <option value="streaming" {{ old('format', $activity->format) == 'streaming' ? 'selected' : '' }}>Стриминг</option>
                                <option value="paper" {{ old('format', $activity->format) == 'paper' ? 'selected' : '' }}>Бумажная</option>
                                <option value="electronic" {{ old('format', $activity->format) == 'electronic' ? 'selected' : '' }}>Электронная</option>
                                <option value="audio" {{ old('format', $activity->format) == 'audio' ? 'selected' : '' }}>Аудиокнига</option>
                                <option value="offline" {{ old('format', $activity->format) == 'offline' ? 'selected' : '' }}>Оффлайн</option>
                            </select>
                            @error('format')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date/Time fields -->
                        @if($activity->temporal_type === 'instant')
                            <div class="mb-4">
                                <label for="date_at" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Дата и время') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="date_at" name="date_at" value="{{ old('date_at', $activity->date_at->format('Y-m-d\TH:i')) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
                                @error('date_at')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div class="mb-4">
                                <label for="date_start" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Дата начала') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="date_start" name="date_start" value="{{ old('date_start', $activity->date_start->toDateString()) }}" class="w-full rounded-md border-gray-300 shadow-sm" required>
                                @error('date_start')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="date_end" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Дата завершения') }}
                                </label>
                                <input type="date" id="date_end" name="date_end" value="{{ old('date_end', $activity->date_end?->toDateString()) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                                <p class="text-gray-500 text-sm mt-1">Оставьте пустым, если ещё читаете</p>
                                @error('date_end')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Impressions -->
                        <div class="mb-4">
                            <label for="impressions" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Впечатления') }}
                            </label>
                            <textarea id="impressions" name="impressions" rows="4" placeholder="Ваши ощущения..." class="w-full rounded-md border-gray-300 shadow-sm">{{ old('impressions', $activity->impressions) }}</textarea>
                            @error('impressions')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rating -->
                        <div class="mb-6">
                            <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Оценка (0-10)') }}
                            </label>
                            <input type="number" id="rating" name="rating" min="0" max="10" step="0.5" value="{{ old('rating', $activity->rating) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                            @error('rating')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit buttons -->
                        <div class="flex gap-3 justify-end">
                            <a href="{{ route('activities.show', $activity) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                {{ __('Отмена') }}
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                {{ __('Сохранить') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
