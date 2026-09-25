@props([
    'draggable' => true,
    'disabled' => false,
    'accept' => null,
    'title' => 'Click or drag file to this area to upload',
    'description' => 'Support for single or bulk upload',
    'icon' => null,
    'name' => null,
    'id' => null,
])

@php
    $id = $id ?? ($name ?? 'upload-' . uniqid());
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'upload ' . ($disabled ? 'disabled' : '')]) }}>
    @if($draggable)
        <label for="{{ $id }}" class="upload-draggable group">
            <input 
                type="file" 
                id="{{ $id }}"
                name="{{ $name }}"
                {{ $accept ? 'accept=' . $accept : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->except('class')->merge(['class' => 'sr-only']) }}
            />
            
            <div class="flex flex-col items-center justify-center pointer-events-none">
                @if($icon)
                    <div class="mb-3 text-gray-400 group-hover:text-primary transition-colors">
                        <x-nav-icon :name="$icon" class="w-10 h-10" />
                    </div>
                @else
                    <div class="w-12 h-12 mb-3 rounded-2xl bg-primary-subtle text-primary flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                @endif
                
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 group-hover:text-primary transition-colors">{{ $title }}</span>
                @if($description)
                    <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{ $description }}</span>
                @endif

                {{ $slot }}
            </div>
        </label>
    @else
        <label for="{{ $id }}" class="inline-flex cursor-pointer">
            <input 
                type="file" 
                id="{{ $id }}"
                name="{{ $name }}"
                {{ $accept ? 'accept=' . $accept : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->except('class')->merge(['class' => 'sr-only']) }}
            />
            {{ $slot }}
        </label>
    @endif
</div>
