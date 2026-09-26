@props([
    'name',
    'weight' => 'regular', // regular, duotone, bold, fill
    'class' => '',
])

@php
    $weightPrefix = match($weight) {
        'duotone' => 'ph-duotone',
        'bold' => 'ph-bold',
        'fill' => 'ph-fill',
        default => 'ph',
    };
@endphp

<i {{ $attributes->merge(['class' => "{$weightPrefix} ph-{$name} {$class} inline-flex items-center justify-center shrink-0 leading-none align-middle"]) }}></i>
