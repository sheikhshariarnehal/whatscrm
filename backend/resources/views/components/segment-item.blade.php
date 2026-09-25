@props([
    'active' => false,
    'disabled' => false,
])

<button 
    type="button" 
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => 'segment-item ' . ($active ? 'segment-item-active' : '') . ($disabled ? 'segment-item-disabled' : '')]) }}
>
    {{ $slot }}
</button>
