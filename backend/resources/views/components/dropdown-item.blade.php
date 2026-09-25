@props([
    'href' => null,
    'active' => false,
    'disabled' => false,
    'icon' => null,
    'danger' => false,
])

@php
    $baseClass = 'dropdown-item ' . 
        ($active ? 'dropdown-item-active ' : '') . 
        ($disabled ? 'dropdown-item-disabled ' : '') .
        ($danger ? 'text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 ' : '');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => trim($baseClass)]) }}>
        @if($icon)
            <x-nav-icon :name="$icon" class="w-4 h-4 shrink-0" />
        @endif
        <span class="truncate">{{ $slot }}</span>
    </a>
@else
    <button type="button" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => trim($baseClass)]) }}>
        @if($icon)
            <x-nav-icon :name="$icon" class="w-4 h-4 shrink-0" />
        @endif
        <span class="truncate">{{ $slot }}</span>
    </button>
@endif
