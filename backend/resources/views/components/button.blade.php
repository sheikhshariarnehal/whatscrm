@props([
    'variant' => 'default', // solid, default, plain, danger, success
    'size' => 'md', // xs, sm, md, lg
    'shape' => 'round', // round, circle, none
    'block' => false,
    'icon' => null,
    'iconAlignment' => 'start', // start, end
    'as' => 'button', // button, a
    'href' => null,
    'type' => 'button',
    'loading' => false,
    'disabled' => false,
])

@php
    $variantClass = match($variant) {
        'solid' => 'btn-solid',
        'plain' => 'btn-plain',
        'danger' => 'btn-danger',
        'success' => 'btn-success',
        default => 'btn-default',
    };

    $sizeClass = match($size) {
        'xs' => 'btn-xs',
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => 'btn-md',
    };

    $shapeClass = match($shape) {
        'circle' => 'rounded-full',
        'none' => 'rounded-none',
        default => ($size === 'lg' ? 'rounded-2xl' : 'rounded-xl'),
    };

    $buttonClasses = "button button-press-feedback {$variantClass} {$sizeClass} {$shapeClass} " . 
        ($block ? 'w-full ' : '');
@endphp

@if ($as === 'a' || $href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => trim($buttonClasses)]) }}>
        @if ($loading)
            <svg class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif ($icon && $iconAlignment === 'start')
            <x-ph-icon :name="$icon" weight="bold" class="text-base shrink-0 mr-1.5" />
        @endif

        {{ $slot }}

        @if ($icon && $iconAlignment === 'end' && !$loading)
            <x-ph-icon :name="$icon" weight="bold" class="text-base shrink-0 ml-1.5" />
        @endif
    </a>
@else
    <button type="{{ $type }}" 
            @if ($disabled || $loading) disabled @endif
            {{ $attributes->merge(['class' => trim($buttonClasses)]) }}>
        @if ($loading)
            <svg class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif ($icon && $iconAlignment === 'start')
            <x-ph-icon :name="$icon" weight="bold" class="text-base shrink-0 mr-1.5" />
        @endif

        {{ $slot }}

        @if ($icon && $iconAlignment === 'end' && !$loading)
            <x-ph-icon :name="$icon" weight="bold" class="text-base shrink-0 ml-1.5" />
        @endif
    </button>
@endif
