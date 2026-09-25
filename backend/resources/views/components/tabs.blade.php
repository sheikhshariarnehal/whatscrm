@props([
    'variant' => 'underline', // underline, pill
])

@php
    $listClass = match($variant) {
        'pill' => 'tab-list tab-list-pill',
        default => 'tab-list tab-list-underline',
    };
@endphp

<div {{ $attributes->merge(['class' => $listClass]) }}>
    {{ $slot }}
</div>
