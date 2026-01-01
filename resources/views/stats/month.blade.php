<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monthly Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Month Selector -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('stats.month') }}" class="flex items-center gap-4">
                        <label for="month" class="block text-sm font-medium text-gray-700">
                            {{ __('Select Month') }}:
                        </label>
                        <input type="month"
                               id="month"
                               name="month"
                               value="{{ $monthString }}"
                               class="rounded-md border-gray-300 shadow-sm">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ __('Go') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistics Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ __('Statistics for') }} {{ $month->format('F Y') }}
                    </h3>

                    @if(count($stats) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">{{ __('Metric') }}</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">{{ __('Type') }}</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">{{ __('Value') }}</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">{{ __('Count') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stats as $stat)
                                        <tr>
                                            <td class="border border-gray-300 px-4 py-2">
                                                <strong>{{ $stat['metric']->title }}</strong>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2">
                                                @if ($stat['type'] === 'scale')
                                                    {{ __('Scale') }}
                                                @else
                                                    {{ __('Boolean') }}
                                                @endif
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2">
                                                @if ($stat['type'] === 'scale')
                                                    <div class="text-sm">
                                                        @if ($stat['avg'] !== null)
                                                            <div>{{ __('Average') }}: <strong>{{ $stat['avg'] }}</strong></div>
                                                            <div class="text-gray-600">
                                                                {{ __('Min') }}: {{ $stat['min'] ?? '-' }} |
                                                                {{ __('Max') }}: {{ $stat['max'] ?? '-' }}
                                                            </div>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    @if ($stat['percentage'] !== null)
                                                        <strong>{{ $stat['percentage'] }}%</strong>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2">
                                                @if ($stat['count'] > 0)
                                                    {{ $stat['count'] }}
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            {{ __('No data available for this month.') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
