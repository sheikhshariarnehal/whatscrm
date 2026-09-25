@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'checked' => false,
    'disabled' => false,
    'value' => null,
])

@php
    $id = $id ?? ($name ? $name . '_' . uniqid() : 'chk_' . uniqid());
@endphp

<label for="{{ $id }}" class="checkbox-label {{ $disabled ? 'disabled' : '' }}">
    <span class="relative flex items-center justify-center">
        <input 
            type="checkbox" 
            id="{{ $id }}"
            @if ($name) name="{{ $name }}" @endif
            @if ($value !== null) value="{{ $value }}" @endif
            @if ($checked) checked @endif
            @if ($disabled) disabled @endif
            {{ $attributes->merge(['class' => 'checkbox peer text-primary focus:ring-0']) }}
        />
    </span>
    @if ($label || $slot->isNotEmpty())
        <span>{{ $label ?? $slot }}</span>
    @endif
</label>
