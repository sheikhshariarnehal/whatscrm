@props([
    'label' => null,
    'description' => null,
    'checked' => false,
    'disabled' => false,
    'name' => null,
    'id' => null,
])

@php
    $id = $id ?? ($name ?? 'switcher-' . md5($label . uniqid()));
@endphp

<label for="{{ $id }}" {{ $attributes->only('class')->merge(['class' => 'inline-flex items-center gap-3 cursor-pointer select-none group ' . ($disabled ? 'opacity-50 cursor-not-allowed' : '')]) }}>
    <div class="relative inline-flex items-center">
        <input 
            type="checkbox" 
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $checked ? 'checked' : '' }}
            {{ $attributes->except('class')->merge(['class' => 'sr-only peer']) }}
        />
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary shadow-inner transition-colors duration-200"></div>
    </div>
    @if($label || $description || $slot->isNotEmpty())
        <div class="flex flex-col">
            @if($label)
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
            @endif
            @if($description)
                <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $description }}</span>
            @endif
            {{ $slot }}
        </div>
    @endif
</label>
