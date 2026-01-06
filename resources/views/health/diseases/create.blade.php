<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Добавить болезнь') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('diseases.store') }}">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Название болезни') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   placeholder="Например: ОРВИ, Грипп, Ангина"
                                   class="w-full rounded-md border-gray-300 shadow-sm"
                                   required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Категория') }}
                            </label>
                            <select id="category"
                                    name="category"
                                    class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">-- Выберите категорию --</option>
                                <option value="ОРВИ" {{ old('category') == 'ОРВИ' ? 'selected' : '' }}>🤧 ОРВИ</option>
                                <option value="ЖКТ" {{ old('category') == 'ЖКТ' ? 'selected' : '' }}>🤤 ЖКТ (желудочно-кишечное)</option>
                                <option value="ЛОР" {{ old('category') == 'ЛОР' ? 'selected' : '' }}>👂 ЛОР (уши, горло, нос)</option>
                                <option value="Аллергия" {{ old('category') == 'Аллергия' ? 'selected' : '' }}>😤 Аллергия</option>
                                <option value="Грипп" {{ old('category') == 'Грипп' ? 'selected' : '' }}>🦠 Грипп</option>
                                <option value="Воспаление" {{ old('category') == 'Воспаление' ? 'selected' : '' }}>🔥 Воспаление</option>
                                <option value="Травма" {{ old('category') == 'Травма' ? 'selected' : '' }}>🩹 Травма</option>
                                <option value="Обострение" {{ old('category') == 'Обострение' ? 'selected' : '' }}>⚠️ Обострение (хронического)</option>
                                <option value="Стресс" {{ old('category') == 'Стресс' ? 'selected' : '' }}>😰 Стресс-факторы</option>
                                <option value="Другое" {{ old('category') == 'Другое' ? 'selected' : '' }}>❓ Другое</option>
                            </select>
                            @error('category')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div class="mb-4">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Дата начала') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   id="start_date"
                                   name="start_date"
                                   value="{{ old('start_date', now()->toDateString()) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm"
                                   required>
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Описание') }}
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Краткое описание эпизода болезни"
                                      class="w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit buttons -->
                        <div class="flex gap-3 justify-end">
                            <a href="{{ route('diseases.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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
</x-app-layout>
