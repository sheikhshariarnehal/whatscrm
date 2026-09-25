@props([
    'label' => null,
    'error' => null,
    'required' => false,
    'layout' => 'vertical', // vertical or horizontal
    'hint' => null,
    'for' => null,
])

<div {{ $attributes->merge(['class' => 'form-item ' . ($layout === 'horizontal' ? 'horizontal' : 'vertical')]) }}>
    @if($label)
        <label @if($for) for="{{ $for }}" @endif class="form-label {{ $error ? 'invalid' : '' }}">
            <span>{{ $label }}</span>
            @if($required)
                <span class="text-rose-500 ml-1 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="w-full">
        {{ $slot }}

        @if($hint && !$error)
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">{{ $hint }}</p>
        @endif

        @if($error)
            <span class="form-explain">{{ $error }}</span>
        @endif
    </div>
</div>
