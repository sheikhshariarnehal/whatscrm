@props([
    'vertical' => false,
])

<div {{ $attributes->merge(['class' => 'steps ' . ($vertical ? 'steps-vertical' : '')]) }}>
    {{ $slot }}
</div>
