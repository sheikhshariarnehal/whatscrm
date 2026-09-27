@props([
    'size' => 'md', // sm, md, lg
    'type' => 'text',
    'textArea' => false,
    'rows' => 3,
    'invalid' => false,
    'prefixIcon' => null,
    'suffixIcon' => null,
    'disabled' => false,
    'clearable' => false,
])

@php
    $wireModel = $attributes->wire('model')->value();
    $hasPrefix = !empty($prefixIcon) || isset($prefix);
    $hasSuffix = !empty($suffixIcon) || isset($suffix) || $clearable;

    $sizeClasses = match($size) {
        'sm' => 'input-sm text-xs',
        'lg' => 'input-lg text-sm',
        default => 'input-md text-xs',
    };

    $affixClasses = '';
    if ($hasPrefix) $affixClasses .= ' input-affix-left';
    if ($hasSuffix) $affixClasses .= ' input-affix-right';

    $inputClasses = 'input ' . $sizeClasses . $affixClasses . ($invalid ? ' input-invalid' : '') . ($disabled ? ' opacity-50 cursor-not-allowed' : '');

    // Map common icon aliases to Phosphor icon names
    $normalizeIcon = function($icon) {
        if (!$icon) return null;
        return match($icon) {
            'search' => 'magnifying-glass',
            'contacts' => 'address-book',
            'settings' => 'gear',
            'team' => 'users',
            'inbox' => 'chat',
            default => $icon,
        };
    };

    $resolvedPrefixIcon = $normalizeIcon($prefixIcon);
    $resolvedSuffixIcon = $normalizeIcon($suffixIcon);
@endphp

@if($textArea)
    <textarea 
        rows="{{ $rows }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $inputClasses . ' resize-y']) }}
    >{{ $slot }}</textarea>
@elseif($hasPrefix || $hasSuffix)
    <div {{ $attributes->only(['class'])->merge(['class' => 'input-wrapper']) }}
         @if($clearable)
             x-data="{
                 val: @if($wireModel) @entangle($attributes->wire('model')) @else '{{ addslashes($attributes->get('value', '')) }}' @endif,
                 clear() {
                     this.val = '';
                     if (this.$refs.inputField) {
                         this.$refs.inputField.value = '';
                         this.$refs.inputField.dispatchEvent(new Event('input', { bubbles: true }));
                         this.$refs.inputField.dispatchEvent(new Event('change', { bubbles: true }));
                     }
                     @if($wireModel)
                         $wire.set('{{ $wireModel }}', '');
                     @endif
                 }
             }"
         @endif>
        @if($hasPrefix)
            <div class="input-icon-prefix">
                @if(isset($prefix))
                    {{ $prefix }}
                @elseif($resolvedPrefixIcon)
                    <x-ph-icon :name="$resolvedPrefixIcon" weight="bold" class="text-sm text-gray-400" />
                @endif
            </div>
        @endif

        <input 
            type="{{ $type }}"
            {{ $disabled ? 'disabled' : '' }}
            @if($clearable)
                x-ref="inputField"
                @input="val = $event.target.value"
            @endif
            {{ $attributes->whereDoesntStartWith(['class'])->merge(['class' => $inputClasses]) }}
        />

        @if($clearable)
            <button type="button" 
                    x-show="val && String(val).length > 0"
                    @click.stop="clear()" 
                    class="input-icon-suffix cursor-pointer text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1"
                    title="Clear"
                    style="display: none;">
                <x-ph-icon name="x" weight="bold" class="w-3.5 h-3.5" />
            </button>
        @endif

        @if($hasSuffix && ($resolvedSuffixIcon || isset($suffix)))
            <div class="input-icon-suffix" @if($clearable) x-show="!val || String(val).length === 0" @endif>
                @if(isset($suffix))
                    {{ $suffix }}
                @elseif($resolvedSuffixIcon)
                    <x-ph-icon :name="$resolvedSuffixIcon" weight="bold" class="text-sm text-gray-400" />
                @endif
            </div>
        @endif
    </div>
@else
    <input 
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $inputClasses]) }}
    />
@endif
