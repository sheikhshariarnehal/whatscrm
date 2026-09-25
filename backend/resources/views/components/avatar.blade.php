@props([
    'src' => null,
    'name' => null,
    'icon' => null,
    'size' => 'md', // xs, sm, md, lg, xl
    'shape' => 'circle', // circle, round, square
    'status' => null, // online, offline, away, busy
    'statusClass' => '',
])

@php
    $sizeClass = match($size) {
        'xs' => 'avatar-xs',
        'sm' => 'avatar-sm',
        'lg' => 'avatar-lg',
        'xl' => 'avatar-xl',
        default => 'avatar-md',
    };

    $shapeClass = match($shape) {
        'round' => 'avatar-round',
        'square' => 'avatar-square',
        default => 'avatar-circle',
    };

    $statusDotClass = match($status) {
        'online' => 'bg-emerald-500',
        'busy' => 'bg-rose-500',
        'away' => 'bg-amber-500',
        'offline' => 'bg-gray-400',
        default => 'bg-emerald-500',
    };

    $statusDotSize = match($size) {
        'xs' => 'w-1.5 h-1.5',
        'sm' => 'w-2 h-2',
        'lg' => 'w-3.5 h-3.5',
        'xl' => 'w-4 h-4',
        default => 'w-2.5 h-2.5',
    };

    $initials = '';
    if ($name) {
        $words = preg_split("/\s+/", trim($name));
        if (count($words) >= 2) {
            $initials = mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1);
        } else {
            $initials = mb_substr($name, 0, 2);
        }
    }
@endphp

<div class="relative inline-flex shrink-0">
    <span {{ $attributes->merge(['class' => "avatar {$sizeClass} {$shapeClass}"]) }}>
        @if ($src)
            <img src="{{ $src }}" alt="{{ $name ?? 'Avatar' }}" class="avatar-img {{ $shapeClass }}" loading="lazy">
        @elseif ($icon)
            <x-nav-icon :name="$icon" class="w-1/2 h-1/2" />
        @elseif ($initials)
            <span class="avatar-string">{{ strtoupper($initials) }}</span>
        @else
            {{ $slot }}
        @endif
    </span>

    @if ($status)
        <span class="absolute bottom-0 right-0 {{ $statusDotSize }} rounded-full {{ $statusDotClass }} ring-2 ring-white dark:ring-gray-900 {{ $statusClass }}"></span>
    @endif
</div>
