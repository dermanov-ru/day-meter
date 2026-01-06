<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Фото-хроника') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Month Selector -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('photos.chronicle.index') }}" class="flex items-center gap-4">
                        <label for="month" class="block text-sm font-medium text-gray-700">
                            {{ __('Выберите месяц') }}:
                        </label>
                        <input type="month"
                               id="month"
                               name="month"
                               value="{{ $monthString }}"
                               class="rounded-md border-gray-300 shadow-sm">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ __('Показать') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Photos Timeline -->
            @if($dayEntries->count() > 0)
                <div class="space-y-8">
                    @foreach($dayEntries as $dayEntry)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <!-- Date Header -->
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($dayEntry->date)->format('d.m.Y') }}
                                    <span class="text-sm font-normal text-gray-500">
                                        ({{ \Carbon\Carbon::parse($dayEntry->date)->translatedFormat('l') }})
                                    </span>
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $dayEntry->photos->count() }} {{ trans_choice('фото|фото|фото', $dayEntry->photos->count()) }}
                                </p>
                            </div>

                            <!-- Photos List -->
                            <div class="divide-y divide-gray-200">
                                @foreach($dayEntry->photos as $photo)
                                    <div class="p-6">
                                        <!-- Photo Preview -->
                                        <div class="mb-4">
                                            <img src="{{ $photo->getUrl() }}"
                                                 alt="Photo"
                                                 class="max-w-2xl w-full h-auto rounded-lg shadow-md"
                                                 loading="lazy">
                                        </div>

                                        <!-- Comment -->
                                        @if($photo->comment)
                                            <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <p class="text-gray-700 leading-relaxed">{{ $photo->comment }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="text-gray-500 text-center py-8">
                            {{ __('В этом месяце нет фотографий. Загружайте фото при вводе данных за день.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Navigation to previous/next month -->
            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('photos.chronicle.index', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    ← {{ __('Предыдущий месяц') }}
                </a>
                <span class="text-gray-700 font-medium">
                    {{ $month->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('photos.chronicle.index', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    {{ __('Следующий месяц') }} →
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
