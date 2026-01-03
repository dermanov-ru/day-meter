<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __("Введите данные за ") }}<span
                    class="font-bold text-blue-600">{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</span>
            </h2>
            <form method="GET" action="{{ route('entry.show') }}" class="flex items-center gap-2">
                или <input type="date"
                       name="date"
                       value="{{ $date }}"
                       class="rounded-md border-gray-300 shadow-sm"
                       required>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    {{ __('Перейти') }}
                </button>
            </form>
        </div>

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

                        <div class="space-y-8">
                            @foreach ($categoriesWithMetrics as $category)
                                @if ($category->metrics->count() > 0)
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                            {{ $category->title }}
                                        </h3>
                                        <div class="space-y-6">
                                            @foreach ($category->metrics as $metric)
                                                <div>
                                                    <label for="metric_{{ $metric->id }}" class="block text-sm font-medium text-gray-700">
                                                        {{ $metric->title }}
                                                    </label>

                                                    @if ($metric->type === 'boolean')
                                                        <input type="hidden" name="metric_{{ $metric->id }}" value="0">
                                                        <input type="checkbox"
                                                               id="metric_{{ $metric->id }}"
                                                               name="metric_{{ $metric->id }}"
                                                               value="1"
                                                               @if (isset($metricValues[$metric->id]) && $metricValues[$metric->id]) checked @endif
                                                               class="mt-2">
                                                    @else
                                                        <div class="mt-2 space-y-2">
                                                            <input type="range"
                                                                   id="metric_{{ $metric->id }}"
                                                                   name="metric_{{ $metric->id }}"
                                                                   min="{{ $metric->min_value }}"
                                                                   max="{{ $metric->max_value }}"
                                                                   value="{{ $metricValues[$metric->id] ?? ($metric->min_value + ($metric->max_value - $metric->min_value) / 2) }}"
                                                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                                                                   oninput="updateSliderValue('metric_{{ $metric->id }}')"
                                                                   required>
                                                            <div class="flex justify-between text-xs text-gray-500 px-1">
                                                                <span>{{ $metric->min_value }}</span>
                                                                <span id="value_metric_{{ $metric->id }}" class="font-semibold text-gray-700">{{ $metricValues[$metric->id] ?? ($metric->min_value + ($metric->max_value - $metric->min_value) / 2) }}</span>
                                                                <span>{{ $metric->max_value }}</span>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @error("metric_{{ $metric->id }}")
                                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                    @enderror

                                                    <!-- Comment Section -->
                                                    <div class="mt-3">
                                                        <textarea name="metric_{{ $metric->id }}_comment"
                                                                  id="metric_{{ $metric->id }}_comment"
                                                                  rows="2"
                                                                  placeholder="{{ __('Add optional explanation...') }}"
                                                                  class="block w-full rounded-md border-gray-300 shadow-sm text-sm">{{ $metricComments[$metric->id] ?? '' }}</textarea>
                                                        @error("metric_{{ $metric->id }}_comment")
                                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Day Note Section -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <label for="day_note" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Коротко о дне') }}
                            </label>
                            <textarea name="day_note"
                                      id="day_note"
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="{{ __('Что было важного / что запомнилось / почему так получилось') }}"
                                      class="block w-full rounded-md border-gray-300 shadow-sm">{{ $dayNote ?? '' }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Максимум 1000 символов') }}</p>
                            @error('day_note')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
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

    <script>
        function updateSliderValue(sliderId) {
            const slider = document.getElementById(sliderId);
            const valueDisplay = document.getElementById('value_' + sliderId);
            if (valueDisplay) {
                valueDisplay.textContent = slider.value;
            }
            updateSliderBackground(slider);
        }

        function updateSliderBackground(slider) {
            const value = (slider.value - slider.min) / (slider.max - slider.min) * 100;
            slider.style.background = `linear-gradient(to right, #3b82f6 0%, #3b82f6 ${value}%, #e5e7eb ${value}%, #e5e7eb 100%)`;
        }

        // Initialize sliders on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="range"].slider').forEach(slider => {
                updateSliderBackground(slider);
            });
        });
    </script>

    <style>
        /* Styling for range slider */
        input[type="range"].slider {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 8px;
            border-radius: 5px;
            outline: none;
        }

        input[type="range"].slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: background 0.15s ease-in-out;
        }

        input[type="range"].slider::-webkit-slider-thumb:hover {
            background: #2563eb;
        }

        input[type="range"].slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: background 0.15s ease-in-out;
        }

        input[type="range"].slider::-moz-range-thumb:hover {
            background: #2563eb;
        }

        input[type="range"].slider::-moz-range-track {
            background: transparent;
            border: none;
        }
    </style>
</x-app-layout>
