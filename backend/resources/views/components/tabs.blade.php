@props([
    'variant' => 'folder', // folder, underline, pill
])

@php
    $listClass = match($variant) {
        'pill' => 'tab-list tab-list-pill',
        'underline' => 'tab-list tab-list-underline',
        default => 'tab-list tab-list-folder',
    };
@endphp

<div {{ $attributes->merge(['class' => $listClass]) }}>
    {{ $slot }}
</div>
