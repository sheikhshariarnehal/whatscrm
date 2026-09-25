@props([
    'header' => null,
    'headerExtra' => null,
    'footer' => null,
    'bordered' => true,
    'shadow' => false,
    'clickable' => false,
    'bodyClass' => '',
    'headerClass' => '',
    'footerClass' => '',
    'gutterless' => false,
])

@php
    $cardClasses = 'card ' . 
        ($bordered ? 'card-border ' : '') . 
        ($shadow ? 'card-shadow ' : '') . 
        ($clickable ? 'cursor-pointer select-none hover:shadow-md ' : '');
    
    $bodyClasses = 'card-body ' . 
        ($gutterless ? 'card-gutterless ' : '') . 
        $bodyClass;
@endphp

<div {{ $attributes->merge(['class' => trim($cardClasses)]) }}>
    @if ($header || isset($headerSlot))
        <div class="card-header card-header-border {{ ($headerExtra || isset($headerExtraSlot)) ? 'card-header-extra' : '' }} {{ $headerClass }}">
            <div>
                @if (isset($headerSlot))
                    {{ $headerSlot }}
                @else
                    <h3 class="font-bold text-base text-gray-900 dark:text-white tracking-tight">{{ $header }}</h3>
                @endif
            </div>

            @if (isset($headerExtraSlot))
                <div class="flex items-center gap-2">{{ $headerExtraSlot }}</div>
            @elseif ($headerExtra)
                <div class="flex items-center gap-2">{{ $headerExtra }}</div>
            @endif
        </div>
    @endif

    <div class="{{ trim($bodyClasses) }}">
        {{ $slot }}
    </div>

    @if ($footer || isset($footerSlot))
        <div class="card-footer card-footer-border {{ $footerClass }}">
            @if (isset($footerSlot))
                {{ $footerSlot }}
            @else
                {{ $footer }}
            @endif
        </div>
    @endif
</div>
