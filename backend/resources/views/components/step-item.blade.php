@props([
    'step' => 1,
    'status' => 'pending', // complete, in_progress, pending, error
    'title' => null,
    'description' => null,
    'isLast' => false,
    'vertical' => false,
    'customIcon' => null,
])

@php
    $iconClass = match($status) {
        'complete' => 'step-item-icon-complete',
        'in_progress' => 'step-item-icon-current',
        'error' => 'step-item-icon-error',
        default => 'step-item-icon-pending',
    };

    $connectClass = 'step-connect ' . 
        ($vertical ? 'step-connect-vertical ' : '') . 
        ($status === 'complete' ? 'step-connect-active ' : '');
@endphp

<div {{ $attributes->merge(['class' => 'step-item ' . ($vertical ? 'step-item-vertical' : '')]) }}>
    <div class="step-item-wrapper">
        <div class="step-item-icon {{ $iconClass }}">
            @if($status === 'complete')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            @elseif($status === 'error')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            @elseif($customIcon)
                <x-nav-icon :name="$customIcon" class="w-4 h-4" />
            @else
                <span>{{ $step }}</span>
            @endif
        </div>

        @if($title || $description || $slot->isNotEmpty())
            <div class="step-item-content">
                @if($title)
                    <span class="step-item-title {{ $status === 'in_progress' ? 'step-item-title-active' : '' }} {{ $status === 'error' ? 'step-item-title-error' : '' }}">
                        {{ $title }}
                    </span>
                @endif
                @if($description)
                    <span class="step-item-description">{{ $description }}</span>
                @endif
                {{ $slot }}
            </div>
        @endif
    </div>

    @if(!$isLast)
        <div class="{{ $connectClass }}"></div>
    @endif
</div>
