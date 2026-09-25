@props([
    'size' => 'md',
    'type' => 'date',
    'disabled' => false,
    'invalid' => false,
    'placeholder' => null,
])

@php
    $sizeClasses = match($size) {
        'sm' => 'input-sm text-xs',
        'lg' => 'input-lg text-base',
        default => 'input-md text-sm',
    };

    $inputClasses = 'input ' . $sizeClasses . ' input-affix-left' . ($invalid ? ' input-invalid' : '') . ($disabled ? ' opacity-50 cursor-not-allowed' : '');
@endphp

<div class="input-wrapper">
    <div class="input-icon-prefix">
        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>

    <input 
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $inputClasses]) }}
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
    />
</div>
