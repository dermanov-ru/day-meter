@props(['icon' => null, 'label' => '', 'href' => null, 'active' => false])

@php
$classes = $active
    ? 'flex flex-col items-center justify-center text-blue-600 transition-colors duration-200'
    : 'flex flex-col items-center justify-center text-gray-500 hover:text-gray-700 transition-colors duration-200';

$isClickable = $href !== null;
@endphp

@if ($isClickable)
    <a href="{{ $href }}" title="{{ $label }}" {{ $attributes->merge(['class' => "$classes flex flex-col items-center justify-center py-2 px-2 rounded-lg transition-colors duration-200 hover:bg-gray-100"]) }}>
        @if ($icon)
            <div class="w-6 h-6 flex items-center justify-center mb-1">
                {!! $icon !!}
            </div>
        @endif
        @if ($label)
            <span class="text-center leading-tight text-[9px] max-w-[4rem] break-words whitespace-normal">{{ $label }}</span>
        @endif
    </a>
@else
    <div title="{{ $label }}" {{ $attributes->merge(['class' => "$classes flex flex-col items-center justify-center py-2 px-2 rounded-lg cursor-default"]) }}>
        @if ($icon)
            <div class="w-6 h-6 flex items-center justify-center mb-1">
                {!! $icon !!}
            </div>
        @endif
        @if ($label)
            <span class="text-center leading-tight text-[9px] max-w-[4rem] break-words whitespace-normal">{{ $label }}</span>
        @endif
    </div>
@endif
