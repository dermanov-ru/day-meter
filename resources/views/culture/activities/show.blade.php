<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $activity->title }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-0">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Activity Header -->
                    <div class="mb-6 pb-6 border-b">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-3xl font-bold mb-2">{{ $activity->title }}</div>
                                <div class="text-gray-600">
                                    <span class="font-medium">{{ $activity->getTypeDisplayAttribute() }}</span>
                                    <span class="mx-2">•</span>
                                    <span class="font-medium">{{ $activity->getFormatDisplayAttribute() }}</span>
                                </div>
                            </div>
                            @if($activity->rating)
                                <div class="text-right">
                                    <div class="text-4xl font-bold text-yellow-500">{{ $activity->rating }}</div>
                                    <div class="text-sm text-gray-500">из 10</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Date Information -->
                    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($activity->temporal_type === 'instant')
                            <div>
                                <div class="text-sm font-medium text-gray-500 uppercase">Дата и время</div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $activity->date_at->format('d.m.Y H:i') }}
                                </div>
                            </div>
                        @else
                            <div>
                                <div class="text-sm font-medium text-gray-500 uppercase">Начало</div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $activity->date_start->format('d.m.Y') }}
                                </div>
                            </div>
                            @if($activity->date_end)
                                <div>
                                    <div class="text-sm font-medium text-gray-500 uppercase">Завершение</div>
                                    <div class="text-lg font-semibold text-gray-900">
                                        {{ $activity->date_end->format('d.m.Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ({{ $activity->getDurationDays() }} дн.)
                                    </div>
                                </div>
                            @else
                                <div>
                                    <div class="text-sm font-medium text-gray-500 uppercase">Длительность</div>
                                    <div class="text-lg font-semibold text-gray-900">
                                        В процессе
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ({{ $activity->getDurationDays() }} дн. с начала)
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Impressions -->
                    @if($activity->impressions)
                        <div class="mb-6 pb-6 border-b">
                            <div class="text-sm font-medium text-gray-500 uppercase mb-2">Впечатления</div>
                            <div class="text-gray-700 whitespace-pre-wrap">{{ $activity->impressions }}</div>
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="mb-6 flex items-center gap-2">
                        @if($activity->status === 'finished')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">✅ Завершено</span>
                        @else
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">📖 В процессе</span>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 justify-end mt-8">
                        <a href="{{ route('activities.edit', $activity) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ __('Редактировать') }}
                        </a>

                        <form method="POST" action="{{ route('activities.destroy', $activity) }}" style="display:inline;" onsubmit="return confirm('Вы уверены?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                {{ __('Удалить') }}
                            </button>
                        </form>

                        <a href="{{ route('activities.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            {{ __('Назад') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
