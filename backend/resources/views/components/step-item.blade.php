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

    $titleStatusClass = match($status) {
        'complete' => 'step-item-title-complete',
        'in_progress' => 'step-item-title-active',
        'error' => 'step-item-title-error',
        default => '',
    };

    $connectClass = 'step-connect ' . 
        ($vertical ? 'step-connect-vertical ' : '') . 
        ($status === 'complete' ? 'step-connect-active ' : '');
@endphp

<div {{ $attributes->merge(['class' => 'step-item ' . ($vertical ? 'step-item-vertical' : '')]) }}>
    <div class="step-item-wrapper">
        <div class="step-item-icon {{ $iconClass }}">
            @if($status === 'complete')
                <x-ph-icon name="check" weight="bold" class="text-sm" />
            @elseif($status === 'error')
                <x-ph-icon name="x" weight="bold" class="text-sm" />
            @elseif($customIcon)
                <x-ph-icon :name="$customIcon" weight="bold" class="text-sm" />
            @else
                <span>{{ $step }}</span>
            @endif
        </div>

        @if($title || $description || (isset($slot) && $slot->isNotEmpty()))
            <div class="step-item-content">
                @if($title)
                    <span class="step-item-title {{ $titleStatusClass }}">
                        {{ $title }}
                    </span>
                @endif
                @if($description)
                    <span class="step-item-description">{{ $description }}</span>
                @endif
                {{ $slot ?? '' }}
            </div>
        @endif
    </div>

    @if(!$isLast)
        <div class="{{ $connectClass }}"></div>
    @endif
</div>
