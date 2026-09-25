@props([
    'size' => 'md', // sm, md, lg
    'type' => 'text',
    'textArea' => false,
    'rows' => 3,
    'invalid' => false,
    'prefixIcon' => null,
    'suffixIcon' => null,
    'disabled' => false,
])

@php
    $sizeClasses = match($size) {
        'sm' => 'input-sm text-xs',
        'lg' => 'input-lg text-base',
        default => 'input-md text-sm',
    };

    $hasPrefix = $prefixIcon || isset($prefix);
    $hasSuffix = $suffixIcon || isset($suffix);
    
    $affixClasses = '';
    if ($hasPrefix) $affixClasses .= ' input-affix-left';
    if ($hasSuffix) $affixClasses .= ' input-affix-right';

    $inputClasses = 'input ' . $sizeClasses . $affixClasses . ($invalid ? ' input-invalid' : '') . ($disabled ? ' opacity-50 cursor-not-allowed' : '');
@endphp

@if($textArea)
    <textarea 
        rows="{{ $rows }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $inputClasses . ' resize-y']) }}
    >{{ $slot }}</textarea>
@elseif($hasPrefix || $hasSuffix)
    <div class="input-wrapper">
        @if($hasPrefix)
            <div class="input-icon-prefix">
                @if(isset($prefix))
                    {{ $prefix }}
                @elseif($prefixIcon)
                    <x-nav-icon :name="$prefixIcon" class="w-4 h-4 text-gray-400" />
                @endif
            </div>
        @endif

        <input 
            type="{{ $type }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => $inputClasses]) }}
        />

        @if($hasSuffix)
            <div class="input-icon-suffix">
                @if(isset($suffix))
                    {{ $suffix }}
                @elseif($suffixIcon)
                    <x-nav-icon :name="$suffixIcon" class="w-4 h-4 text-gray-400" />
                @endif
            </div>
        @endif
    </div>
@else
    <input 
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $inputClasses]) }}
    />
@endif
