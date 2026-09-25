@props([
    'size' => 'md',
    'fullWidth' => false,
])

<div {{ $attributes->merge(['class' => 'segment ' . ($fullWidth ? 'w-full flex' : 'inline-flex')]) }}>
    {{ $slot }}
</div>
