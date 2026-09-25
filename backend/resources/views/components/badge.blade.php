@props([
    'content' => null,
    'dot' => false,
    'max' => 99,
    'color' => 'primary', // primary, success, error, warning, info, amber, emerald, blue, rose, purple, gray
    'innerClass' => '',
])

@php
    $colorClass = match($color) {
        'success', 'emerald' => 'bg-emerald-500 text-white',
        'error', 'rose' => 'bg-rose-500 text-white',
        'warning', 'amber' => 'bg-amber-500 text-white',
        'info', 'blue' => 'bg-blue-500 text-white',
        'purple' => 'bg-purple-500 text-white',
        'gray' => 'bg-gray-500 text-white',
        default => 'bg-primary text-white',
    };

    $dotColorClass = match($color) {
        'success', 'emerald' => 'bg-emerald-500',
        'error', 'rose' => 'bg-rose-500',
        'warning', 'amber' => 'bg-amber-500',
        'info', 'blue' => 'bg-blue-500',
        'purple' => 'bg-purple-500',
        'gray' => 'bg-gray-400',
        default => 'bg-primary',
    };

    $displayContent = is_numeric($content) && $content > $max ? "{$max}+" : $content;
@endphp

@if ($slot->isNotEmpty())
    <span {{ $attributes->merge(['class' => 'badge-wrapper']) }}>
        {{ $slot }}
        @if ($dot)
            <span class="badge-dot badge-inner {{ $dotColorClass }} {{ $innerClass }}"></span>
        @elseif ($content !== null)
            <span class="badge badge-inner {{ $colorClass }} {{ $innerClass }}">
                {{ $displayContent }}
            </span>
        @endif
    </span>
@else
    @if ($dot)
        <span {{ $attributes->merge(['class' => "badge-dot inline-block {$dotColorClass}"]) }}></span>
    @else
        <span {{ $attributes->merge(['class' => "badge {$colorClass}"]) }}>
            {{ $displayContent }}
        </span>
    @endif
@endif
