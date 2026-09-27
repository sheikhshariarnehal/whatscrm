@props([
    'active' => false,
    'variant' => 'folder', // folder, underline, pill
    'icon' => null,
    'badge' => null,
    'badgeColor' => 'primary',
    'disabled' => false,
])

@php
    $variantClass = match($variant) {
        'pill' => 'tab-nav-pill ',
        'underline' => 'tab-nav-underline ',
        default => 'tab-nav-folder ',
    };
    $baseClass = 'tab-nav outline-none focus:outline-none focus-visible:outline-none focus:ring-0 focus-visible:ring-0 select-none ' . 
        $variantClass . 
        ($active ? 'tab-nav-active ' : '') . 
        ($disabled ? 'tab-nav-disabled ' : '');
    $tag = $attributes->has('href') ? 'a' : 'button';
@endphp

<{{ $tag }} 
    @if ($tag === 'button') type="button" @endif
    @if ($disabled) disabled @endif
    style="outline: none !important;"
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

