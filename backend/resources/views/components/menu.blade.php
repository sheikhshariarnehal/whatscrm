@props([
    'variant' => 'transparent', // light, dark, transparent
])

@php
    $menuVariant = match($variant) {
        'light' => 'menu-light',
        'dark' => 'menu-dark',
        default => 'menu-transparent',
    };
@endphp

<nav {{ $attributes->merge(['class' => 'menu ' . $menuVariant]) }}>
    {{ $slot }}
</nav>
