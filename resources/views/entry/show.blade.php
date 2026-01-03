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

                    <form method="POST" action="{{ route('entry.store') }}" id="entry-form">
                        @csrf

                        <input type="hidden" name="date" value="{{ $date }}">

                        <!-- Quick Delta Input Section -->
                        <div class="mb-8 pb-6 border-b border-gray-200">
                            <label for="delta_note" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Быстрое добавление') }}
                            </label>
                            <textarea id="delta_note"
                                      rows="2"
                                      placeholder="{{ __('Запишите мысль...') }}"
                                      class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            <div class="flex gap-2 mt-2 justify-end">
                                <button type="button"
                                        id="voice-input-btn"
                                        onclick="toggleVoiceInput()"
                                        class="px-3 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200"
                                        style="display: none;"
                                        title="Голосовой ввод">
                                    🎤
                                </button>
                                <button type="button"
                                        onclick="addDelta()"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    {{ __('Добавить') }}
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ __('Быстрая фиксация мысли с временной отметкой') }}</p>
                        </div>

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

                        <!-- Main Day Note Section -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <label for="day_note" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Заметка за день') }}
                            </label>
                            <textarea name="day_note"
                                      id="day_note"
                                      rows="10"
                                      placeholder="{{ __('Что было важного / что запомнилось / почему так получилось') }}"
                                      class="block w-full rounded-md border-gray-300 shadow-sm font-mono text-sm">{{ $dayNote ?? '' }}</textarea>
                            @error('day_note')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Static Save Button at Bottom -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <button type="button" onclick="submitForm(document.getElementById('entry-form'))" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                                {{ __('Сохранить') }}
                            </button>
                        </div>

                        <!-- Mobile Floating Save Button -->
                        <button type="button" onclick="submitForm(document.getElementById('entry-form'))" class="fixed bottom-8 right-8 sm:hidden w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 hover:scale-110">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V3"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast Notification -->
    <div id="success-toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg opacity-0 transition-opacity duration-300 pointer-events-none z-50">
        ✓ Данные сохранены
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
            // Initialize all counters
            document.querySelectorAll('div.accordion-item').forEach(item => {
                const categoryId = item.getAttribute('data-category-id');
                updateCounter(categoryId);
            });
        }

        function addDelta() {
            const deltaInput = document.getElementById('delta_note');
            const mainNote = document.getElementById('day_note');
            const text = deltaInput.value.trim();

            if (!text) return;

            // Get current time
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const timestamp = `${hours}:${minutes}`;

            // Format: — HH:MM\n<text>
            const delta = `— ${timestamp}\n${text}`;

            // Save current scroll position
            const scrollBefore = mainNote.scrollTop;

            // Add delta to main note
            if (mainNote.value) {
                mainNote.value += '\n\n' + delta;
            } else {
                mainNote.value = delta;
            }

            // Restore scroll position and clear delta input
            mainNote.scrollTop = scrollBefore;
            deltaInput.value = '';
            deltaInput.focus();

            // Async form submission
            submitForm(document.getElementById('entry-form'));
        }

        function showSuccessToast() {
            const toast = document.getElementById('success-toast');
            if (!toast) return;

            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');

            setTimeout(() => {
                toast.classList.add('opacity-0');
                toast.classList.remove('opacity-100');
            }, 1000);
        }

        function submitFormAsync(form) {
            const formData = new FormData(form);
            const csrfTokenInput = document.querySelector('input[name="_token"]');
            const csrfToken = csrfTokenInput ? csrfTokenInput.value : '';
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Ошибка: CSRF токен не найден. Перезагрузите страницу.');
                return;
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ status: response.status, ok: response.ok, data })))
            .then(({ status, ok, data }) => {
                if (!ok) {
                    // Server returned error with validation details
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join('\n');
                        throw new Error(`Ошибка валидации (${status}):\n${errorMessages}`);
                    } else if (data.message) {
                        throw new Error(`${data.message} (${status})`);
                    } else {
                        throw new Error(`HTTP error! status: ${status}`);
                    }
                }
                console.log('Save response:', data);
                if (data.success) {
                    showSuccessToast();
                } else {
                    console.warn('Response not marked as success:', data);
                }
            })
            .catch(error => {
                console.error('Error saving:', error);
                alert('Ошибка при сохранении: ' + error.message);
            });
        }

        function submitForm(form) {
            submitFormAsync(form);
        }

        // Web Speech API for voice input
        let recognition = null;
        let isListening = false;
        let finalTranscript = '';
        let interimTranscript = '';

        function initVoiceInput() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            
            if (!SpeechRecognition) {
                console.log('Web Speech API не поддерживается');
                return;
            }
            
            const voiceBtn = document.getElementById('voice-input-btn');
            if (voiceBtn) {
                voiceBtn.style.display = 'block';
            }
            
            recognition = new SpeechRecognition();
            
            // Detect mobile device
            const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
            
            recognition.continuous = !isMobile;  // Disable continuous on mobile
            recognition.interimResults = true;
            recognition.lang = 'ru-RU';
            
            recognition.onstart = function() {
                isListening = true;
                updateVoiceButtonState();
            };
            
            recognition.onresult = function(event) {
                // Reset interim transcript each time
                interimTranscript = '';
                
                // Process results correctly
                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const transcript = event.results[i][0].transcript;
                    
                    // Only add final results to finalTranscript (don't duplicate)
                    if (event.results[i].isFinal) {
                        finalTranscript += transcript + ' ';
                    } else {
                        // Interim results are temporary
                        interimTranscript += transcript;
                    }
                }
                
                // Update field with final + interim (this prevents repetition)
                const deltaInput = document.getElementById('delta_note');
                if (deltaInput) {
                    deltaInput.value = finalTranscript + interimTranscript;
                }
            };
            
            recognition.onerror = function(event) {
                console.error('Speech recognition error:', event.error);
                isListening = false;
                updateVoiceButtonState();
            };
            
            recognition.onend = function() {
                isListening = false;
                updateVoiceButtonState();
            };
        }
        
        function toggleVoiceInput() {
            if (!recognition) {
                initVoiceInput();
            }
            
            if (isListening) {
                recognition.stop();
                // Save current value as final when stopping
                finalTranscript = document.getElementById('delta_note').value;
                interimTranscript = '';
                isListening = false;
            } else {
                // Start fresh with current text as base
                finalTranscript = document.getElementById('delta_note').value;
                interimTranscript = '';
                recognition.start();
            }
            
            updateVoiceButtonState();
        }
        
        function updateVoiceButtonState() {
            const voiceBtn = document.getElementById('voice-input-btn');
            if (!voiceBtn) return;
            
            if (isListening) {
                voiceBtn.classList.remove('bg-gray-300', 'text-gray-700', 'hover:bg-gray-400');
                voiceBtn.classList.add('bg-red-500', 'text-white', 'hover:bg-red-600', 'animate-pulse');
            } else {
                voiceBtn.classList.remove('bg-red-500', 'text-white', 'hover:bg-red-600', 'animate-pulse');
                voiceBtn.classList.add('bg-gray-300', 'text-gray-700', 'hover:bg-gray-400');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sliders
            document.querySelectorAll('input[type="range"].slider').forEach(slider => {
                updateSliderBackground(slider);
            });
            
            initializeAccordions();
            
            // Initialize voice input
            initVoiceInput();
            
            // Allow Enter+Ctrl/Cmd to quickly add delta
            const deltaInput = document.getElementById('delta_note');
            if (deltaInput) {
                deltaInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                        e.preventDefault();
                        addDelta();
                    }
                });
            }
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
