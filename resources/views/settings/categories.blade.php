<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Управление категориями метрик') }}
            </h2>
            <a href="{{ route('settings.metrics') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                {{ __('Метрики') }}
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

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Add New Category Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        {{ __('Добавить новую категорию') }}
                    </h3>

                    <form method="POST" action="{{ route('settings.categories.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="key" class="block text-sm font-medium text-gray-700">
                                {{ __('Ключ (Key)') }}
                            </label>
                            <input type="text"
                                   id="key"
                                   name="key"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   required
                                   @error('key') is-invalid @enderror
                                   value="{{ old('key') }}">
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
                                   value="{{ old('title') }}">
                            @error('title')
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
                                   value="{{ old('sort_order', 0) }}">
                            @error('sort_order')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ __('Добавить категорию') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">
                        {{ __('Категории метрик') }}
                    </h3>

                    <div class="space-y-4">
                        @forelse ($categories as $category)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <h4 class="font-medium text-gray-800">{{ $category->title }}</h4>
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
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ __('Ключ: ') }}<code>{{ $category->key }}</code>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ __('Метрик в категории: ') }}<strong>{{ $category->metrics->count() }}</strong>
                                    </p>
                                    @if (!$category->is_user_defined)
                                        <p class="text-sm text-blue-600 mt-1">
                                            {{ __('(системная категория)') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-purple-600 mt-1">
                                            {{ __('(пользовательская категория)') }}
                                        </p>
                                    @endif
                                </div>

                                <div class="ml-4 flex items-center gap-2">
                                    @if ($category->is_user_defined)
                                        <button onclick="showEditForm({{ $category->id }})"
                                                class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                            {{ __('Редактировать') }}
                                        </button>

                                        @if ($category->metrics->count() === 0)
                                            <form method="POST" action="{{ route('settings.categories.delete', $category) }}" class="inline" onsubmit="return confirm('Вы уверены?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                                    {{ __('Удалить') }}
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('settings.categories.update', $category) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')

                                            <div class="flex items-center">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox"
                                                       id="is_active_{{ $category->id }}"
                                                       name="is_active"
                                                       value="1"
                                                       @checked($category->is_active)
                                                       onchange="this.form.submit()"
                                                       class="rounded">
                                                <label for="is_active_{{ $category->id }}" class="ml-2 text-sm text-gray-700">
                                                    @if ($category->is_active)
                                                        <span class="text-green-600 font-medium">{{ __('Активна') }}</span>
                                                    @else
                                                        <span class="text-red-600 font-medium">{{ __('Неактивна') }}</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            @if ($category->is_user_defined)
                                <!-- Edit Form (Hidden by default) -->
                                <div id="edit-form-{{ $category->id }}" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200 ml-4">
                                    <form method="POST" action="{{ route('settings.categories.update', $category) }}" class="space-y-4">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label for="edit_title_{{ $category->id }}" class="block text-sm font-medium text-gray-700">
                                                {{ __('Название') }}
                                            </label>
                                            <input type="text"
                                                   id="edit_title_{{ $category->id }}"
                                                   name="title"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                   value="{{ $category->title }}"
                                                   required>
                                        </div>

                                        <div>
                                            <label for="edit_sort_{{ $category->id }}" class="block text-sm font-medium text-gray-700">
                                                {{ __('Порядок сортировки') }}
                                            </label>
                                            <input type="number"
                                                   id="edit_sort_{{ $category->id }}"
                                                   name="sort_order"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                                   value="{{ $category->sort_order }}">
                                        </div>

                                        <div class="flex items-center">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox"
                                                   id="edit_active_{{ $category->id }}"
                                                   name="is_active"
                                                   value="1"
                                                   @checked($category->is_active)
                                                   class="rounded">
                                            <label for="edit_active_{{ $category->id }}" class="ml-2 text-sm text-gray-700">
                                                {{ __('Активна') }}
                                            </label>
                                        </div>

                                        <div class="flex gap-2">
                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                                {{ __('Сохранить') }}
                                            </button>
                                            <button type="button"
                                                    onclick="hideEditForm({{ $category->id }})"
                                                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                                {{ __('Отмена') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @empty
                            <p class="text-gray-500 text-center py-8">
                                {{ __('Нет категорий') }}
                            </p>
                        @endforelse
                    </div>

                    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">{{ __('Информация') }}</h4>
                        <div class="text-sm text-blue-800 space-y-3">
                            <div>
                                <h5 class="font-medium mb-1">{{ __('Пользовательские категории:') }}</h5>
                                <ul class="space-y-1 ml-2">
                                    <li>✓ {{ __('добавлять новые') }}</li>
                                    <li>✓ {{ __('редактировать название') }}</li>
                                    <li>✓ {{ __('изменять активность') }}</li>
                                    <li>✓ {{ __('менять порядок сортировки') }}</li>
                                    <li>✓ {{ __('удалять (если нет метрик)') }}</li>
                                </ul>
                            </div>
                            <div>
                                <h5 class="font-medium mb-1">{{ __('Системные категории:') }}</h5>
                                <ul class="space-y-1 ml-2">
                                    <li>✓ {{ __('менять активность') }}</li>
                                    <li>✗ {{ __('редактировать название') }}</li>
                                    <li>✗ {{ __('удалять') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showEditForm(categoryId) {
            document.getElementById('edit-form-' + categoryId).classList.remove('hidden');
        }

        function hideEditForm(categoryId) {
            document.getElementById('edit-form-' + categoryId).classList.add('hidden');
        }
    </script>
</x-app-layout>
