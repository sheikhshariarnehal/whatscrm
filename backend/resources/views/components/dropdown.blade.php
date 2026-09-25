@props([
    'placement' => 'bottom-end', // bottom-start, bottom-end, bottom-center, top-start, top-end, top-center
    'width' => null,
    'contentClasses' => '',
])

@php
    $placementClass = match($placement) {
        'bottom-start' => 'bottom-start',
        'bottom-center' => 'bottom-center',
        'top-start' => 'top-start',
        'top-end' => 'top-end',
        'top-center' => 'top-center',
        default => 'bottom-end',
    };
@endphp

<div class="dropdown" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open" class="cursor-pointer">
        {{ $trigger ?? $slot }}
    </div>

    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="dropdown-menu absolute {{ $placementClass }} {{ $width }} {{ $contentClasses }}"
        style="display: none;"
        @click="open = false"
    >
        @if(isset($content))
            {{ $content }}
        @else
            {{ $slot }}
        @endif
    </div>
</div>
