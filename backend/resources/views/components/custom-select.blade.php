@props([
    'placeholder' => 'Select an option...',
    'options' => [],
    'selected' => null,
    'size' => 'md',
    'prefixIcon' => null,
    'disabled' => false,
    'placement' => 'bottom-start',
])

@php
    $sizeClasses = match($size) {
        'sm' => 'py-1.5 px-3 text-xs',
        'xs' => 'py-1 px-2.5 text-[11px]',
        default => 'py-2 px-3.5 text-xs',
    };
@endphp

<div class="relative inline-block text-left" 
     x-data="{ 
         open: false, 
         selectedVal: @js($selected),
         selectedLabel: '',
         init() {
             this.updateLabel();
         },
         updateLabel() {
             const opts = @js($options);
             if (Array.isArray(opts)) {
                 const found = opts.find(o => (typeof o === 'object' ? (o.id == this.selectedVal || o.value == this.selectedVal) : o == this.selectedVal));
                 if (found) {
                     this.selectedLabel = typeof found === 'object' ? (found.label || found.name || found.title) : found;
                     return;
                 }
             } else if (typeof opts === 'object') {
                 if (opts[this.selectedVal]) {
                     this.selectedLabel = opts[this.selectedVal];
                     return;
                 }
             }
             this.selectedLabel = @js($placeholder);
         },
         select(val, label) {
             this.selectedVal = val;
             this.selectedLabel = label;
             this.open = false;
             $dispatch('input', val);
             $dispatch('change', val);
         }
     }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

    <!-- Trigger Button -->
    <button type="button" 
            @click="open = !open" 
            {{ $disabled ? 'disabled' : '' }}
            class="inline-flex items-center justify-between gap-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750 transition-all shadow-xs {{ $sizeClasses }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
        <div class="flex items-center gap-2 truncate">
            @if($prefixIcon)
                <x-ph-icon :name="$prefixIcon" weight="duotone" class="text-base text-gray-400 shrink-0" />
            @endif
            <span x-text="selectedLabel || '{{ $placeholder }}'" class="truncate"></span>
        </div>
        <x-ph-icon name="caret-down" weight="bold" class="text-xs text-gray-400 transition-transform duration-150 shrink-0" ::class="open ? 'rotate-180' : ''" />
    </button>

    <!-- Floating Dropdown Menu -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
         class="absolute z-50 mt-1.5 min-w-[12rem] max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-gray-900 p-1.5 shadow-xl border border-gray-100 dark:border-gray-800 focus:outline-none"
         style="display: none;">
        {{ $slot }}
    </div>
</div>
