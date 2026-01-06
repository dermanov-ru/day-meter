<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-col sm:flex-row gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Журнал болезней') }}
            </h2>
            <a href="{{ route('diseases.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm whitespace-nowrap">
                {{ __('+ Болезнь') }}
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

            <!-- Active Diseases -->
            <div class="mb-12">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('Активные болезни') }}
                </h3>

                @if ($activeDiseases->count() > 0)
                    <div class="space-y-3">
                        @foreach ($activeDiseases as $disease)
                            <a href="{{ route('diseases.show', $disease) }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-red-500">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $disease->title }}</h4>
                                        @if ($disease->category)
                                            <p class="text-sm text-gray-600">{{ $disease->category }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-2">
                                            Начало: {{ $disease->start_date->format('d.m.Y') }}
                                            <span class="ml-4 font-medium">{{ $disease->duration_days }} дн.</span>
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                        Активная
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">{{ __('Нет активных болезней') }}</p>
                @endif
            </div>

            <!-- Closed Diseases -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('Завершённые болезни') }}
                </h3>

                @if ($closedDiseases->count() > 0)
                    <div class="space-y-3">
                        @foreach ($closedDiseases as $disease)
                            <a href="{{ route('diseases.show', $disease) }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-gray-400 opacity-75">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $disease->title }}</h4>
                                        @if ($disease->category)
                                            <p class="text-sm text-gray-600">{{ $disease->category }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-2">
                                            {{ $disease->start_date->format('d.m.Y') }} – {{ $disease->end_date->format('d.m.Y') }}
                                            <span class="ml-4 font-medium">{{ $disease->duration_days }} дн.</span>
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                        Завершена
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">{{ __('Нет завершённых болезней') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
