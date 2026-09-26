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
    $tag = $attributes->has('href') ? 'a' : 'button';
@endphp

<{{ $tag }} 
    @if ($tag === 'button') type="button" @endif
    @if ($disabled) disabled @endif
    {{ $attributes->merge(['class' => trim($baseClass)]) }}>
    @if ($icon)
        <x-nav-icon :name="$icon" class="tab-nav-icon w-4 h-4 shrink-0" />
    @endif

    <span class="inline-flex items-center gap-2">
        {{ $slot }}
    </span>

    @if ($badge !== null)
        <span class="ml-2 shrink-0">
            <x-badge :content="$badge" :color="$badgeColor" />
        </span>
    @endif
</{{ $tag }}>

