@props([
    'placeholder' => 'Select an option...',
    'options' => [],
    'size' => 'md',
    'prefixIcon' => null,
    'disabled' => false,
    'invalid' => false,
    'searchable' => null, // auto-detected if >= 6 options
    'clearable' => false,
])

@php
    $sizeClasses = match($size) {
        'sm' => 'py-1.5 px-3 text-xs h-9',
        'lg' => 'py-2.5 px-4 text-sm h-11',
        default => 'py-2 px-3.5 text-xs h-10',
    };

    $wireModel = $attributes->wire('model')->value();
@endphp

<div {{ $attributes->only(['class', 'style'])->merge(['class' => 'select-wrapper select-none']) }}
     x-data="{
         open: false,
         value: @if($wireModel) @entangle($attributes->wire('model')) @else '' @endif,
         search: '',
         optionsList: [],
         selectedLabel: '',
         placeholder: '{{ $placeholder }}',
         disabled: {{ $disabled ? 'true' : 'false' }},

         init() {
             this.syncOptions();
             this.$nextTick(() => {
                 this.syncSelectedLabel();
             });
             this.$watch('value', () => {
                 this.syncSelectedLabel();
             });

             // Observe child <option> changes from Livewire re-renders
             if (this.$refs.nativeSelect) {
                 const observer = new MutationObserver(() => {
                     this.syncOptions();
                     this.syncSelectedLabel();
                 });
                 observer.observe(this.$refs.nativeSelect, { childList: true, subtree: true });
             }
         },

         syncOptions() {
             const passedOpts = @js($options);
             let list = [];

             if (Array.isArray(passedOpts) && passedOpts.length > 0) {
                 list = passedOpts.map(o => {
                     if (typeof o === 'object' && o !== null) {
                         return {
                             value: String(o.id ?? o.value ?? ''),
                             label: String(o.label ?? o.name ?? o.title ?? o.value ?? ''),
                             disabled: Boolean(o.disabled ?? false),
                         };
                     }
                     return { value: String(o), label: String(o), disabled: false };
                 });
             } else if (typeof passedOpts === 'object' && passedOpts !== null && Object.keys(passedOpts).length > 0) {
                 list = Object.entries(passedOpts).map(([val, lbl]) => ({
                     value: String(val),
                     label: String(lbl),
                     disabled: false,
                 }));
             } else if (this.$refs.nativeSelect) {
                 const nativeOpts = Array.from(this.$refs.nativeSelect.options);
                 list = nativeOpts.map(opt => ({
                     value: opt.value,
                     label: opt.text.trim(),
                     disabled: opt.disabled,
                 }));
             }

             this.optionsList = list;
         },

         syncSelectedLabel() {
             if (this.value === null || this.value === undefined || this.value === '') {
                 // Check if there is an empty-value placeholder option
                 const emptyOpt = this.optionsList.find(o => o.value === '');
                 this.selectedLabel = emptyOpt ? emptyOpt.label : this.placeholder;
                 return;
             }

             const found = this.optionsList.find(o => String(o.value) === String(this.value));
             if (found) {
                 this.selectedLabel = found.label;
             } else {
                 this.selectedLabel = this.placeholder;
             }
         },

         get filteredOptions() {
             if (!this.search || !this.search.trim()) {
                 return this.optionsList;
             }
             const q = this.search.toLowerCase().trim();
             return this.optionsList.filter(o => o.label.toLowerCase().includes(q));
         },

         selectOption(opt) {
             if (opt.disabled || this.disabled) return;

             this.value = opt.value;
             this.selectedLabel = opt.label;
             this.open = false;
             this.search = '';

             if (this.$refs.nativeSelect) {
                 this.$refs.nativeSelect.value = opt.value;
                 this.$refs.nativeSelect.dispatchEvent(new Event('input', { bubbles: true }));
                 this.$refs.nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
             }

             this.$dispatch('input', opt.value);
             this.$dispatch('change', opt.value);
         },

         isSelected(opt) {
             return String(this.value) === String(opt.value);
         },

         get showSearch() {
             return {{ $searchable === true ? 'true' : ($searchable === false ? 'false' : 'this.optionsList.length >= 6') }};
         }
     }"
     @click.outside="open = false; search = ''"
     @keydown.escape.window="open = false; search = ''">

    <!-- Hidden Native Select for Livewire / Form Compatibility -->
    <select x-ref="nativeSelect" 
            class="hidden" 
            tabindex="-1" 
            aria-hidden="true"
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->whereDoesntStartWith(['class']) }}>
        @if(!empty($placeholder))
            <option value="">{{ $placeholder }}</option>
        @endif
        @if(!empty($options))
            @foreach($options as $val => $text)
                <option value="{{ $val }}">{{ $text }}</option>
            @endforeach
        @endif
        {{ $slot }}
    </select>

    <!-- Custom Select Trigger Button matching Starter Design System -->
    <div @click="if(!disabled) { open = !open; if(open && showSearch) $nextTick(() => $refs.searchInput?.focus()); }"
         :class="{
             'focused': open,
             'invalid': {{ $invalid ? 'true' : 'false' }},
             'disabled': disabled
         }"
         class="select-trigger {{ $sizeClasses }}">
        
        <div class="flex items-center gap-2.5 truncate flex-1 min-w-0">
            @if($prefixIcon)
                <x-ph-icon :name="$prefixIcon" weight="duotone" class="text-base text-gray-400 shrink-0" />
            @endif
            <span x-text="selectedLabel || placeholder" 
                  :class="(value === '' || value === null || value === undefined) ? 'text-gray-400 font-normal' : 'text-gray-900 dark:text-gray-100 font-semibold'"
                  class="truncate text-xs"></span>
        </div>

        <div class="flex items-center gap-1.5 shrink-0 ml-1.5">
            <x-ph-icon name="caret-down" weight="bold" class="text-xs text-gray-400 transition-transform duration-150" ::class="open ? 'rotate-180 text-primary' : ''" />
        </div>
    </div>

    <!-- Custom Floating Select Dropdown Menu -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
         class="select-menu"
         style="display: none;">
        
        <!-- Optional Search Filter -->
        <template x-if="showSearch">
            <div class="p-1 pb-1.5 mb-1 border-b border-gray-100 dark:border-gray-800">
                <div class="relative flex items-center">
                    <x-ph-icon name="magnifying-glass" weight="bold" class="absolute left-2.5 text-xs text-gray-400" />
                    <input type="text"
                           x-ref="searchInput"
                           x-model="search"
                           @click.stop
                           placeholder="Search..."
                           class="w-full pl-7 pr-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </template>

        <!-- Options List -->
        <div class="max-h-52 overflow-y-auto space-y-0.5">
            <template x-for="(opt, idx) in filteredOptions" :key="idx">
                <div @click.stop="selectOption(opt)"
                     :class="{
                         'selected': isSelected(opt),
                         'disabled': opt.disabled
                     }"
                     class="select-option">
                    <span x-text="opt.label" class="truncate"></span>
                    <template x-if="isSelected(opt)">
                        <x-ph-icon name="check" weight="bold" class="text-sm text-primary shrink-0 ml-2" />
                    </template>
                </div>
            </template>

            <template x-if="filteredOptions.length === 0">
                <div class="py-3 px-2 text-center text-xs text-gray-400 dark:text-gray-500">
                    No results found
                </div>
            </template>
        </div>
    </div>
</div>
