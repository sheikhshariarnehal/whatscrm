@props([
    'size' => 'md',
    'invalid' => false,
    'disabled' => false,
    'placeholder' => null,
    'options' => [],
    'prefixIcon' => null,
])

@php
    $sizeClasses = match($size) {
        'sm' => 'select-sm text-xs',
        default => 'select-md text-sm',
    };

    $hasPrefix = $prefixIcon || isset($prefix);
    $affixClasses = $hasPrefix ? ' input-affix-left' : '';

    $selectClasses = 'select ' . $sizeClasses . $affixClasses . ($invalid ? ' border-rose-500 focus:ring-rose-500' : '') . ($disabled ? ' opacity-50 cursor-not-allowed' : '');
@endphp

@if($hasPrefix)
    <div class="input-wrapper">
        <div class="input-icon-prefix">
            @if(isset($prefix))
                {{ $prefix }}
            @elseif($prefixIcon)
                <x-nav-icon :name="$prefixIcon" class="w-4 h-4 text-gray-400" />
            @endif
        </div>
        <select {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $selectClasses]) }}>
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @if(!empty($options))
                @foreach($options as $val => $text)
                    <option value="{{ $val }}">{{ $text }}</option>
                @endforeach
            @endif
            {{ $slot }}
        </select>
    </div>
@else
    <select {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $selectClasses]) }}>
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @if(!empty($options))
            @foreach($options as $val => $text)
                <option value="{{ $val }}">{{ $text }}</option>
            @endforeach
        @endif
        {{ $slot }}
    </select>
@endif
