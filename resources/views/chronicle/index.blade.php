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

                                <!-- Day Note -->
                                @if($dayEntry->day_note)
                                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <pre class="text-gray-700 leading-relaxed whitespace-pre-wrap break-words font-sans">{{ $dayEntry->day_note }}</pre>
                                    </div>
                                @endif

                                <!-- Daily Insight -->
                                @php
                                    $dateString = is_string($dayEntry->date) ? $dayEntry->date : $dayEntry->date->toDateString();
                                    $insight = $dailyInsights[$dateString] ?? null;
                                @endphp
                                @if($insight && $insight->text)
                                    <div class="mb-4 p-4 bg-amber-50 rounded-lg border border-amber-200">
                                        <div class="text-sm font-semibold text-amber-900 mb-2">📌 {{ __('Вывод дня') }}:</div>
                                        <p class="text-gray-700 leading-relaxed">{{ $insight->text }}</p>
                                    </div>
                                @endif

                                <!-- Photos -->
                                @if($dayEntry->photos->count() > 0)
                                    <div class="mb-4">
                                        <div class="text-sm font-semibold text-gray-700 mb-3">📷 {{ __('Фото дня') }}:</div>
                                        <div class="space-y-4">
                                            @foreach($dayEntry->photos as $photo)
                                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                    <div class="mb-3">
                                                        <img src="{{ $photo->getUrl() }}"
                                                             alt="Photo"
                                                             class="max-w-full h-auto rounded">
                                                    </div>
                                                    @if($photo->comment)
                                                        <p class="text-gray-700 text-sm leading-relaxed">{{ $photo->comment }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Metric Comments Grouped by Category -->
                                @php
                                    $commentsWithMetrics = $dayEntry->values->filter(fn($v) => $v->comment)->values();
                                    // Group comments by category
                                    $commentsByCategory = [];
                                    foreach ($commentsWithMetrics as $value) {
                                        if ($value->metric && $value->metric->category) {
                                            $catId = $value->metric->category->id;
                                            if (!isset($commentsByCategory[$catId])) {
                                                $commentsByCategory[$catId] = [
                                                    'category' => $value->metric->category,
                                                    'values' => []
                                                ];
                                            }
                                            $commentsByCategory[$catId]['values'][] = $value;
                                        }
                                    }
                                @endphp
                                @if($commentsWithMetrics->count() > 0)
                                    <div class="text-sm text-gray-600 space-y-4">
                                        @foreach($commentsByCategory as $categoryGroup)
                                            <div>
                                                <h4 class="text-xs font-semibold text-gray-700 mb-2">{{ $categoryGroup['category']->title }}</h4>
                                                <div class="space-y-2">
                                                    @foreach($categoryGroup['values'] as $value)
                                                        <div class="pl-3 border-l-2 border-gray-300">
                                                            <div class="font-medium text-gray-700">{{ $value->metric->title }}</div>
                                                            <div class="italic text-gray-600">{{ $value->comment }}</div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(!$dayEntry->day_note)
                                    <!-- No day note and no comments: show "нет данных" -->
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
