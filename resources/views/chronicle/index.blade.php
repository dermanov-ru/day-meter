<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Летопись') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Month Selector -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('chronicle.index') }}" class="flex items-center gap-4">
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
            @if($dayEntries->count() > 0)
                <div class="space-y-6">
                    @foreach($dayEntries as $dayEntry)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <!-- Date Header with Russian day name -->
                                <div class="mb-4 pb-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($dayEntry->date)->format('d.m.Y') }}
                                        <span class="text-sm font-normal text-gray-500">
                                            ({{ \Carbon\Carbon::parse($dayEntry->date)->translatedFormat('l') }})
                                        </span>
                                    </h3>
                                </div>

                                <!-- Day Note and Metrics -->
                                @if($dayEntry->day_note)
                                    <!-- Has day note: show note and metrics -->
                                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                                            {{ $dayEntry->day_note }}
                                        </p>
                                    </div>

                                    <!-- Metrics Summary - Compact -->
                                    @if($dayEntry->values->count() > 0)
                                        <div class="text-sm text-gray-700">
                                            @foreach($dayEntry->values as $index => $value)
                                                <span class="inline-block">
                                                    <strong>{{ $value->metric->title }}:</strong>
                                                    @if($value->metric->type === 'scale')
                                                        {{ $value->value_int ?? '-' }}
                                                    @else
                                                        {{ $value->value_bool ? __('да') : __('нет') }}
                                                    @endif{{ $index < $dayEntry->values->count() - 1 ? ' · ' : '' }}
                                                </span>
                                            @endforeach
                                        </div>
                                        @if($dayEntry->values->whereNotNull('comment')->count() > 0)
                                            <div class="mt-2 text-xs text-gray-600 space-y-1">
                                                @foreach($dayEntry->values as $value)
                                                    @if($value->comment)
                                                        <div class="italic"><strong>{{ $value->metric->title }}:</strong> {{ $value->comment }}</div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <!-- No day note: show "нет данных" -->
                                    <p class="text-gray-500 italic text-sm">
                                        {{ __('Нет данных') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="text-gray-500 text-center py-8">
                            {{ __('Нет записей. Начните вводить данные со страницы ввода.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Navigation to previous/next month -->
            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('chronicle.index', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    ← {{ __('Предыдущий месяц') }}
                </a>
                <span class="text-gray-700 font-medium">
                    {{ $month->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('chronicle.index', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    {{ __('Следующий месяц') }} →
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
