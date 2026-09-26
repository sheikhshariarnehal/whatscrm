@props([
    'placement' => 'bottom-end', // bottom-start, bottom-end, bottom-center, top-start, top-end, top-center
    'width' => 'w-52',
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

<div class="dropdown" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <div @click="open = ! open" class="cursor-pointer inline-flex items-center">
        @if(isset($trigger))
            {{ $trigger }}
        @else
            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors shadow-2xs">
                <span>Actions</span>
                <x-ph-icon name="caret-down" weight="bold" class="text-xs text-gray-400" />
            </button>
        @endif
    </div>

    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
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
