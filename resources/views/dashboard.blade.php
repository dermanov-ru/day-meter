<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('dashboard', ['period' => 'week']) }}"
                   class="px-3 py-2 text-sm rounded @if($period === 'week') bg-blue-600 text-white @else bg-gray-200 text-gray-800 @endif">
                    Week
                </a>
                <a href="{{ route('dashboard', ['period' => 'month']) }}"
                   class="px-3 py-2 text-sm rounded @if($period === 'month') bg-blue-600 text-white @else bg-gray-200 text-gray-800 @endif">
                    Month
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 text-sm text-gray-600">
                        Period: {{ $startDate }} to {{ $endDate }} ({{ $dayEntriesCount }} days with data)
                    </div>

                    @if(count($report) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Metric</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Value</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Days with Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report as $item)
                                        <tr>
                                            <td class="border border-gray-300 px-4 py-2">
                                                {{ $item['metric']->title }}
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2">
                                                @if ($item['metric']->type === 'scale')
                                                    @if ($item['avg'] !== null)
                                                        {{ $item['avg'] }}
                                                    @else
                                                        -
                                                    @endif
                                                @else
                                                    @if ($item['percentage'] !== null)
                                                        {{ $item['percentage'] }}%
                                                    @else
                                                        -
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2">
                                                {{ $item['daysCount'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No data available for this period.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
