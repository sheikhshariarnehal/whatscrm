@props([
    'hoverable' => true,
    'compact' => false,
    'bordered' => false,
    'overflow' => true,
])

@php
    $tableClasses = 'table-default ' . 
        ($hoverable ? 'table-hover ' : '') . 
        ($compact ? 'table-compact ' : '') . 
        ($bordered ? 'table-border ' : '');
@endphp

@if ($overflow)
    <div class="overflow-x-auto w-full">
        <table {{ $attributes->merge(['class' => trim($tableClasses)]) }}>
            {{ $slot }}
        </table>
    </div>
@else
    <table {{ $attributes->merge(['class' => trim($tableClasses)]) }}>
        {{ $slot }}
    </table>
@endif
