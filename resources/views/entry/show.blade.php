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

                        <div class="space-y-4">
                            @foreach ($categoriesWithMetrics as $category)
                                @if ($category->metrics->count() > 0)
                                    <div class="accordion-item" data-category-id="{{ $category->id }}">
                                        <button type="button"
                                                class="accordion-header w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center justify-between"
                                                onclick="toggleAccordion('{{ $category->id }}')"
                                                data-category-id="{{ $category->id }}">
                                            <div class="flex items-center gap-2">
                                                <span class="accordion-chevron transition-transform duration-300">▼</span>
                                                <span>{{ $category->title }}</span>
                                            </div>
                                            <span class="accordion-counter text-sm bg-blue-700 px-2 py-1 rounded font-normal">0 / {{ $category->metrics->count() }}</span>
                                        </button>
                                        <div class="accordion-content bg-gray-50 rounded-b-lg overflow-hidden transition-all duration-300 max-h-0 opacity-0"
                                             data-category-id="{{ $category->id }}"
                                             style="max-height: 0;">
                                            <div class="p-6 space-y-6">
                                                @foreach ($category->metrics as $metric)
                                                    <div class="metric-item" data-metric-id="{{ $metric->id }}">
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
                                                                   class="mt-2"
                                                                   onchange="updateCounter('{{ $category->id }}')">
                                                        @else
                                                            <div class="mt-2 space-y-2">
                                                                <input type="range"
                                                                       id="metric_{{ $metric->id }}"
                                                                       name="metric_{{ $metric->id }}"
                                                                       min="{{ $metric->min_value }}"
                                                                       max="{{ $metric->max_value }}"
                                                                       value="{{ $metricValues[$metric->id] ?? ($metric->min_value + ($metric->max_value - $metric->min_value) / 2) }}"
                                                                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                                                                       oninput="updateSliderValue('metric_{{ $metric->id }}'); updateCounter('{{ $category->id }}')"
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

        function toggleAccordion(categoryId) {
            const header = document.querySelector(`button[data-category-id="${categoryId}"]`);
            const content = document.querySelector(`div.accordion-content[data-category-id="${categoryId}"]`);
            
            if (!content) return;

            const isOpen = content.style.maxHeight && parseInt(content.style.maxHeight) > 0;
            
            // Close all other accordions
            document.querySelectorAll('div.accordion-content').forEach(el => {
                if (el.getAttribute('data-category-id') !== categoryId) {
                    el.style.maxHeight = '0';
                    el.style.opacity = '0';
                    const otherHeader = document.querySelector(`button[data-category-id="${el.getAttribute('data-category-id')}"]`);
                    if (otherHeader) {
                        const chevron = otherHeader.querySelector('.accordion-chevron');
                        if (chevron) chevron.style.transform = 'rotate(0deg)';
                    }
                }
            });
            
            // Toggle current accordion
            if (isOpen) {
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                const chevron = header.querySelector('.accordion-chevron');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            } else {
                const scrollHeight = content.querySelector('div:last-child').scrollHeight;
                content.style.maxHeight = (scrollHeight + 48) + 'px'; // 48px for padding
                content.style.opacity = '1';
                const chevron = header.querySelector('.accordion-chevron');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
        }

        function updateCounter(categoryId) {
            const container = document.querySelector(`div.accordion-item[data-category-id="${categoryId}"]`);
            if (!container) return;
            
            const metrics = container.querySelectorAll('.metric-item');
            let filled = 0;
            
            metrics.forEach(metric => {
                const metricId = metric.getAttribute('data-metric-id');
                const checkbox = metric.querySelector(`input[type="checkbox"][id="metric_${metricId}"]`);
                const slider = metric.querySelector(`input[type="range"][id="metric_${metricId}"]`);
                
                if (checkbox && checkbox.checked) {
                    filled++;
                } else if (slider && slider.value !== null && slider.value !== undefined) {
                    filled++;
                }
            });
            
            const counter = container.querySelector('.accordion-counter');
            if (counter) {
                counter.textContent = `${filled} / ${metrics.length}`;
            }
        }

        function initializeAccordions() {
            const firstItem = document.querySelector('div.accordion-item');
            if (firstItem) {
                const firstCategoryId = firstItem.getAttribute('data-category-id');
                const content = firstItem.querySelector('div.accordion-content');
                if (content) {
                    const scrollHeight = content.querySelector('div:last-child').scrollHeight;
                    content.style.maxHeight = (scrollHeight + 48) + 'px';
                    content.style.opacity = '1';
                    const chevron = firstItem.querySelector('.accordion-chevron');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }
            }
            
            // Initialize all counters
            document.querySelectorAll('div.accordion-item').forEach(item => {
                const categoryId = item.getAttribute('data-category-id');
                updateCounter(categoryId);
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="range"].slider').forEach(slider => {
                updateSliderBackground(slider);
            });
            
            initializeAccordions();
        });
    </script>

    <style>
        /* Accordion Styles */
        .accordion-header {
            cursor: pointer;
        }
        
        .accordion-header:focus {
            outline: 2px solid #1e40af;
            outline-offset: 2px;
        }
        
        .accordion-chevron {
            display: inline-block;
            transition: transform 0.3s ease;
        }
        
        .accordion-content {
            transition: max-height 0.3s ease, opacity 0.3s ease;
        }
        
        .accordion-counter {
            white-space: nowrap;
        }

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
