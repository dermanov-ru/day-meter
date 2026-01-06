<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-col sm:flex-row gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Культурная жизнь') }}
            </h2>
            <a href="{{ route('activities.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm whitespace-nowrap">
                {{ __('+ Активность') }}
            </a>
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

            <!-- In Progress (Duration Activities) -->
            <div class="mb-12">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('📖 В процессе') }}
                </h3>

                @if ($inProgress->count() > 0)
                    <div class="space-y-3">
                        @foreach ($inProgress as $activity)
                            <a href="{{ route('activities.show', $activity) }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-blue-500">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $activity->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $activity->type_display }}</p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            Начало: {{ $activity->date_start->format('d.m.Y') }}
                                            <span class="ml-4">{{ $activity->duration_days }} дн.</span>
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                        В процессе
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">{{ __('Нет активностей в процессе') }}</p>
                @endif
            </div>

            <!-- Recent Instant Activities -->
            <div class="mb-12">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('🎭 Недавние события') }}
                </h3>

                @if ($recentInstant->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentInstant as $activity)
                            <a href="{{ route('activities.show', $activity) }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-purple-500">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $activity->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $activity->type_display }}</p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            {{ $activity->date_at->format('d.m.Y H:i') }}
                                            @if ($activity->rating)
                                                <span class="ml-4">⭐ {{ $activity->rating }}/10</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">{{ __('Нет недавних событий') }}</p>
                @endif
            </div>

            <!-- Finished Activities -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('✅ Завершённые') }}
                </h3>

                @if ($finished->count() > 0)
                    <div class="space-y-3">
                        @foreach ($finished as $activity)
                            <a href="{{ route('activities.show', $activity) }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-gray-400 opacity-75">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $activity->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $activity->type_display }}</p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            @if ($activity->temporal_type === 'duration')
                                                {{ $activity->date_start->format('d.m.Y') }} – {{ $activity->date_end->format('d.m.Y') }}
                                                <span class="ml-4">{{ $activity->duration_days }} дн.</span>
                                            @else
                                                {{ $activity->date_at->format('d.m.Y') }}
                                            @endif
                                            @if ($activity->rating)
                                                <span class="ml-4">⭐ {{ $activity->rating }}/10</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">{{ __('Нет завершённых активностей') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
