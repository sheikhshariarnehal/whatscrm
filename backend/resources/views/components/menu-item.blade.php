@props([
    'href' => null,
    'active' => false,
    'icon' => null,
    'disabled' => false,
    'badge' => null,
    'badgeColor' => 'primary',
])

@php
    $baseClass = 'menu-item menu-item-hoverable ' . 
        ($active ? 'menu-item-active ' : '') . 
        ($disabled ? 'menu-item-disabled ' : '');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => trim($baseClass)]) }}>
        @if($icon)
            <x-nav-icon :name="$icon" class="w-5 h-5 flex-shrink-0 text-xl text-current" />
        @endif
        <span class="truncate flex-1">{{ $slot }}</span>
        @if($badge !== null)
            <x-badge :content="$badge" :color="$badgeColor" />
        @endif
    </a>
@else
    <button type="button" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => trim($baseClass)]) }}>
        @if($icon)
            <x-nav-icon :name="$icon" class="w-5 h-5 flex-shrink-0 text-xl text-current" />
        @endif
        <span class="truncate flex-1 text-left">{{ $slot }}</span>
        @if($badge !== null)
            <x-badge :content="$badge" :color="$badgeColor" />
        @endif
    </button>
@endif
