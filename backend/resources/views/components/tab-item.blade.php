@props([
    'active' => false,
    'variant' => 'underline', // underline, pill
    'icon' => null,
    'badge' => null,
    'badgeColor' => 'primary',
    'disabled' => false,
])

@php
    $baseClass = 'tab-nav ' . 
        ($variant === 'pill' ? 'tab-nav-pill ' : 'tab-nav-underline ') . 
        ($active ? 'tab-nav-active ' : '') . 
        ($disabled ? 'tab-nav-disabled ' : '');
@endphp

<button type="button" 
        @if ($disabled) disabled @endif
        {{ $attributes->merge(['class' => trim($baseClass)]) }}>
    @if ($icon)
        <x-nav-icon :name="$icon" class="tab-nav-icon w-4 h-4" />
    @endif

    <span>{{ $slot }}</span>

    @if ($badge !== null)
        <span class="ml-2">
            <x-badge :content="$badge" :color="$badgeColor" />
        </span>
    @endif
</button>
