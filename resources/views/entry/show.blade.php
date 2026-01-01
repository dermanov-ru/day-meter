<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Entry for ') }}<span class="font-bold text-blue-600">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('entry.store') }}">
                        @csrf

                        <input type="hidden" name="date" value="{{ $date }}">

                        <div class="space-y-6">
                            @foreach ($metrics as $metric)
                                <div>
                                    <label for="metric_{{ $metric->id }}" class="block text-sm font-medium text-gray-700">
                                        {{ $metric->title }}
                                    </label>

                                    @if ($metric->type === 'boolean')
                                        <input type="checkbox"
                                               id="metric_{{ $metric->id }}"
                                               name="metric_{{ $metric->id }}"
                                               value="1"
                                               @if (isset($metricValues[$metric->id]) && $metricValues[$metric->id]) checked @endif
                                               class="mt-2">
                                    @else
                                        <input type="number"
                                               id="metric_{{ $metric->id }}"
                                               name="metric_{{ $metric->id }}"
                                               min="{{ $metric->min_value }}"
                                               max="{{ $metric->max_value }}"
                                               value="{{ $metricValues[$metric->id] ?? '' }}"
                                               class="mt-2 block w-full rounded-md border-gray-300 shadow-sm">
                                    @endif

                                    @error("metric_{{ $metric->id }}")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
