@props([
    'color' => 'gray', // primary, success, error, warning, info, emerald, amber, blue, purple, rose, cyan, gray
    'prefix' => false,
    'suffix' => false,
    'prefixClass' => '',
    'suffixClass' => '',
])

@php
    $colorClasses = match($color) {
        'primary', 'blue' => 'bg-blue-50 dark:bg-blue-950/40 border-blue-200/80 dark:border-blue-900/60 text-blue-700 dark:text-blue-300',
        'success', 'emerald' => 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200/80 dark:border-emerald-900/60 text-emerald-700 dark:text-emerald-300',
        'error', 'rose' => 'bg-rose-50 dark:bg-rose-950/40 border-rose-200/80 dark:border-rose-900/60 text-rose-700 dark:text-rose-300',
        'warning', 'amber' => 'bg-amber-50 dark:bg-amber-950/40 border-amber-200/80 dark:border-amber-900/60 text-amber-700 dark:text-amber-300',
        'purple' => 'bg-purple-50 dark:bg-purple-950/40 border-purple-200/80 dark:border-purple-900/60 text-purple-700 dark:text-purple-300',
        'cyan' => 'bg-cyan-50 dark:bg-cyan-950/40 border-cyan-200/80 dark:border-cyan-900/60 text-cyan-700 dark:text-cyan-300',
        default => 'bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200',
    };

    $affixColor = match($color) {
        'primary', 'blue' => 'bg-blue-500',
        'success', 'emerald' => 'bg-emerald-500',
        'error', 'rose' => 'bg-rose-500',
        'warning', 'amber' => 'bg-amber-500',
        'purple' => 'bg-purple-500',
        'cyan' => 'bg-cyan-500',
        default => 'bg-gray-400',
    };
@endphp

<span {{ $attributes->merge(['class' => "tag {$colorClasses}"]) }}>
    @if ($prefix === true)
        <span class="tag-affix tag-prefix {{ $affixColor }} {{ $prefixClass }}"></span>
    @elseif ($prefix)
        <span class="tag-prefix {{ $prefixClass }}">{{ $prefix }}</span>
    @endif

    <span>{{ $slot }}</span>

    @if ($suffix === true)
        <span class="tag-affix tag-suffix {{ $affixColor }} {{ $suffixClass }}"></span>
    @elseif ($suffix)
        <span class="tag-suffix {{ $suffixClass }}">{{ $suffix }}</span>
    @endif
</span>
