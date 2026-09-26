@props([
    'percent' => 0,
    'size' => 'sm',
    'color' => 'bg-primary',
    'showInfo' => true,
])

@php
    $clampedPercent = min(100, max(0, (float)$percent));
    $barHeight = match($size) {
        'xs' => 'h-1',
        'md' => 'h-2.5',
        'lg' => 'h-3',
        default => 'h-1.5',
    };
@endphp

<div {{ $attributes->merge(['class' => 'progress line w-full flex items-center gap-2']) }}>
    <div class="progress-wrapper flex-1">
        <div class="progress-inner {{ $barHeight }} bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="progress-bg {{ $color }} h-full rounded-full transition-all duration-300" style="width: {{ $clampedPercent }}%"></div>
        </div>
    </div>
    @if($showInfo)
        <span class="progress-info text-xs font-mono font-semibold text-gray-600 dark:text-gray-300 shrink-0">
            {{ $slot->isNotEmpty() ? $slot : round($clampedPercent) . '%' }}
        </span>
    @endif
</div>
