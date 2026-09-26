@props([
    'href' => null,
    'active' => false,
    'disabled' => false,
    'icon' => null,
    'iconWeight' => 'duotone',
    'danger' => false,
])

@php
    $iconName = match($icon) {
        'settings' => 'gear',
        'developer' => 'code',
        'logout' => 'sign-out',
        default => $icon,
    };

    $baseClass = 'dropdown-item ' . 
        ($active ? 'dropdown-item-active ' : '') . 
        ($disabled ? 'dropdown-item-disabled ' : '') .
        ($danger ? '!text-rose-600 dark:!text-rose-400 hover:!bg-rose-50 dark:hover:!bg-rose-950/40 ' : '');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => trim($baseClass)]) }}>
        @if($iconName)
            <x-ph-icon :name="$iconName" :weight="$iconWeight" class="text-base shrink-0 {{ $danger ? 'text-rose-500' : 'text-gray-400 dark:text-gray-500' }}" />
        @endif
        <span class="truncate">{{ $slot }}</span>
    </a>
@else
    <button type="button" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => trim($baseClass)]) }}>
        @if($iconName)
            <x-ph-icon :name="$iconName" :weight="$iconWeight" class="text-base shrink-0 {{ $danger ? 'text-rose-500' : 'text-gray-400 dark:text-gray-500' }}" />
        @endif
        <span class="truncate">{{ $slot }}</span>
    </button>
@endif
