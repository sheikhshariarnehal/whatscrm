@props([
    'size' => 'md',
    'type' => 'datetime-local', // 'datetime-local', 'date', 'time'
    'disabled' => false,
    'invalid' => false,
    'placeholder' => null,
    'clearable' => true,
])

@php
    $isDateTime = str_contains($type, 'datetime');
    $isDateOnly = $type === 'date';
    $isTimeOnly = $type === 'time';
    
    $defaultPlaceholder = $placeholder ?? ($isDateTime ? 'Select date & time...' : ($isDateOnly ? 'Select date...' : 'Select time...'));

    $sizeClasses = match($size) {
        'sm' => 'py-1.5 px-3 text-xs h-9',
        'lg' => 'py-2.5 px-4 text-sm h-11',
        default => 'py-2 px-3.5 text-xs h-10',
    };
@endphp

<div class="relative w-full text-left select-none"
     x-data="{
         open: false,
         value: @entangle($attributes->wire('model')),
         type: '{{ $type }}',
         isDateTime: {{ $isDateTime ? 'true' : 'false' }},
         isDateOnly: {{ $isDateOnly ? 'true' : 'false' }},
         viewYear: new Date().getFullYear(),
         viewMonth: new Date().getMonth(),
         selectedYear: null,
         selectedMonth: null,
         selectedDay: null,
         hours: '10',
         minutes: '00',
         ampm: 'AM',
         monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
         monthShortNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
         dayNames: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],

         init() {
             this.parseValue(this.value);
             this.$watch('value', (val) => this.parseValue(val));
         },

         parseValue(val) {
             if (!val) {
                 this.selectedYear = null;
                 this.selectedMonth = null;
                 this.selectedDay = null;
                 return;
             }
             try {
                 const cleanVal = val.replace(' ', 'T');
                 const d = new Date(cleanVal);
                 if (!isNaN(d.getTime())) {
                     this.selectedYear = d.getFullYear();
                     this.selectedMonth = d.getMonth();
                     this.selectedDay = d.getDate();
                     this.viewYear = this.selectedYear;
                     this.viewMonth = this.selectedMonth;

                     let h = d.getHours();
                     this.ampm = h >= 12 ? 'PM' : 'AM';
                     h = h % 12;
                     h = h ? h : 12;
                     this.hours = String(h).padStart(2, '0');
                     this.minutes = String(d.getMinutes()).padStart(2, '0');
                 }
             } catch (e) {
                 console.error('DatePicker parse error', e);
             }
         },

         get formattedDisplay() {
             if (!this.selectedYear || this.selectedMonth === null || !this.selectedDay) {
                 return '';
             }
             const mName = this.monthShortNames[this.selectedMonth];
             const dayStr = String(this.selectedDay).padStart(2, '0');
             const yr = this.selectedYear;

             if (this.isDateOnly) {
                 return `${mName} ${dayStr}, ${yr}`;
             }
             return `${mName} ${dayStr}, ${yr} • ${this.hours}:${this.minutes} ${this.ampm}`;
         },

         get calendarDays() {
             const days = [];
             const firstDayIndex = new Date(this.viewYear, this.viewMonth, 1).getDay();
             const lastDayCurrentMonth = new Date(this.viewYear, this.viewMonth + 1, 0).getDate();
             const lastDayPrevMonth = new Date(this.viewYear, this.viewMonth, 0).getDate();

             // Prev month days
             for (let i = firstDayIndex - 1; i >= 0; i--) {
                 days.push({
                     day: lastDayPrevMonth - i,
                     month: this.viewMonth - 1,
                     year: this.viewMonth === 0 ? this.viewYear - 1 : this.viewYear,
                     isCurrentMonth: false,
                 });
             }

             // Current month days
             for (let i = 1; i <= lastDayCurrentMonth; i++) {
                 days.push({
                     day: i,
                     month: this.viewMonth,
                     year: this.viewYear,
                     isCurrentMonth: true,
                 });
             }

             // Next month days to complete 42 cells (6 rows) or 35 cells
             const totalCells = days.length > 35 ? 42 : 35;
             const remaining = totalCells - days.length;
             for (let i = 1; i <= remaining; i++) {
                 days.push({
                     day: i,
                     month: this.viewMonth + 1,
                     year: this.viewMonth === 11 ? this.viewYear + 1 : this.viewYear,
                     isCurrentMonth: false,
                 });
             }

             return days;
         },

         prevMonth() {
             if (this.viewMonth === 0) {
                 this.viewMonth = 11;
                 this.viewYear--;
             } else {
                 this.viewMonth--;
             }
         },

         nextMonth() {
             if (this.viewMonth === 11) {
                 this.viewMonth = 0;
                 this.viewYear++;
             } else {
                 this.viewMonth++;
             }
         },

         selectDay(cell) {
             this.selectedYear = cell.year;
             this.selectedMonth = cell.month;
             if (this.selectedMonth < 0) {
                 this.selectedMonth = 11;
                 this.selectedYear--;
             } else if (this.selectedMonth > 11) {
                 this.selectedMonth = 0;
                 this.selectedYear++;
             }
             this.selectedDay = cell.day;
             this.viewYear = this.selectedYear;
             this.viewMonth = this.selectedMonth;

             if (this.isDateOnly) {
                 this.applyValue();
                 this.open = false;
             }
         },

         isSelected(cell) {
             return this.selectedYear === cell.year &&
                    this.selectedMonth === cell.month &&
                    this.selectedDay === cell.day;
         },

         isToday(cell) {
             const now = new Date();
             return now.getFullYear() === cell.year &&
                    now.getMonth() === cell.month &&
                    now.getDate() === cell.day;
         },

         setNow() {
             const now = new Date();
             this.selectedYear = now.getFullYear();
             this.selectedMonth = now.getMonth();
             this.selectedDay = now.getDate();
             this.viewYear = this.selectedYear;
             this.viewMonth = this.selectedMonth;

             let h = now.getHours();
             this.ampm = h >= 12 ? 'PM' : 'AM';
             h = h % 12;
             h = h ? h : 12;
             this.hours = String(h).padStart(2, '0');
             this.minutes = String(Math.floor(now.getMinutes() / 5) * 5).padStart(2, '0');

             this.applyValue();
             this.open = false;
         },

         clearValue() {
             this.selectedYear = null;
             this.selectedMonth = null;
             this.selectedDay = null;
             this.value = '';
             this.$dispatch('input', '');
             this.$dispatch('change', '');
             this.open = false;
         },

         applyValue() {
             if (!this.selectedYear || this.selectedMonth === null || !this.selectedDay) {
                 return;
             }
             const m = String(this.selectedMonth + 1).padStart(2, '0');
             const d = String(this.selectedDay).padStart(2, '0');

             if (this.isDateOnly) {
                 this.value = `${this.selectedYear}-${m}-${d}`;
             } else {
                 let h = parseInt(this.hours, 10);
                 if (this.ampm === 'PM' && h < 12) h += 12;
                 if (this.ampm === 'AM' && h === 12) h = 0;
                 const hStr = String(h).padStart(2, '0');
                 const minStr = String(this.minutes).padStart(2, '0');
                 this.value = `${this.selectedYear}-${m}-${d}T${hStr}:${minStr}`;
             }

             this.$dispatch('input', this.value);
             this.$dispatch('change', this.value);
             this.open = false;
         }
     }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

    <!-- Trigger Input Box matching Elstar Starter Design -->
    <div @click="if(!{{ $disabled ? 'true' : 'false' }}) open = !open"
         class="flex items-center justify-between border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl transition-all cursor-pointer shadow-xs {{ $sizeClasses }} {{ $invalid ? '!border-rose-500 ring-1 ring-rose-500/20' : 'focus-within:border-primary focus-within:ring-1 focus-within:ring-primary' }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'hover:border-gray-300 dark:hover:border-gray-600' }}">
        
        <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <x-ph-icon name="calendar-blank" weight="duotone" class="text-base text-gray-400 shrink-0" />
            
            <template x-if="formattedDisplay">
                <span x-text="formattedDisplay" class="font-semibold text-gray-900 dark:text-gray-100 truncate text-xs"></span>
            </template>
            <template x-if="!formattedDisplay">
                <span class="text-gray-400 font-normal truncate text-xs">{{ $defaultPlaceholder }}</span>
            </template>
        </div>

        <div class="flex items-center gap-1.5 shrink-0 ml-2">
            <template x-if="formattedDisplay && !{{ $disabled ? 'true' : 'false' }}">
                <button type="button" 
                        @click.stop="clearValue()" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-0.5 rounded-full transition-colors" 
                        title="Clear date">
                    <x-ph-icon name="x-circle" weight="fill" class="text-sm" />
                </button>
            </template>
            <x-ph-icon name="caret-down" weight="bold" class="text-xs text-gray-400 transition-transform duration-150" ::class="open ? 'rotate-180 text-primary' : ''" />
        </div>
    </div>

    <!-- Hidden Native Input for standard Form Submissions -->
    <input type="hidden" :value="value" {{ $attributes->whereDoesntStartWith('wire:model') }}>

    <!-- Custom Floating Calendar & Timepicker Popover -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
         class="absolute z-50 mt-2 left-0 w-72 sm:w-80 rounded-2xl bg-white dark:bg-gray-900 p-4 shadow-2xl border border-gray-100 dark:border-gray-800 focus:outline-none"
         style="display: none;">
        
        <!-- Calendar Header: Month/Year + Navigation Buttons -->
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
            <div class="font-bold text-xs text-gray-900 dark:text-white">
                <span x-text="monthNames[viewMonth]"></span>
                <span x-text="viewYear" class="text-primary font-mono ml-0.5"></span>
            </div>

            <div class="flex items-center gap-1">
                <button type="button" 
                        @click.stop="prevMonth()" 
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ph-icon name="caret-left" weight="bold" class="text-xs" />
                </button>
                <button type="button" 
                        @click.stop="nextMonth()" 
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ph-icon name="caret-right" weight="bold" class="text-xs" />
                </button>
            </div>
        </div>

        <!-- Days of Week Header -->
        <div class="grid grid-cols-7 gap-1 text-center mt-2 mb-1">
            <template x-for="day in dayNames" :key="day">
                <span x-text="day" class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider py-1"></span>
            </template>
        </div>

        <!-- Days Grid -->
        <div class="grid grid-cols-7 gap-1 text-center">
            <template x-for="(cell, idx) in calendarDays" :key="idx">
                <button type="button"
                        @click.stop="selectDay(cell)"
                        :class="{
                            'bg-primary text-white font-bold shadow-xs hover:bg-primary-deep': isSelected(cell),
                            'border border-primary text-primary font-bold': isToday(cell) && !isSelected(cell),
                            'text-gray-300 dark:text-gray-600': !cell.isCurrentMonth && !isSelected(cell),
                            'text-gray-700 dark:text-gray-200 hover:bg-primary-subtle hover:text-primary': cell.isCurrentMonth && !isSelected(cell),
                        }"
                        class="h-7 w-7 sm:h-8 sm:w-8 mx-auto text-xs font-semibold rounded-xl flex items-center justify-center transition-all cursor-pointer">
                    <span x-text="cell.day"></span>
                </button>
            </template>
        </div>

        <!-- Optional Time Picker Section -->
        <template x-if="isDateTime">
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <x-ph-icon name="clock" weight="duotone" class="text-sm text-primary" />
                        <span>Time</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <!-- Hours -->
                        <select x-model="hours" class="select select-sm !py-1 !pl-2 !pr-6 text-xs h-7 !rounded-lg border-gray-200 dark:border-gray-700 font-mono font-bold">
                            @foreach(range(1, 12) as $h)
                                <option value="{{ sprintf('%02d', $h) }}">{{ sprintf('%02d', $h) }}</option>
                            @endforeach
                        </select>

                        <span class="font-bold text-gray-400 text-xs">:</span>

                        <!-- Minutes -->
                        <select x-model="minutes" class="select select-sm !py-1 !pl-2 !pr-6 text-xs h-7 !rounded-lg border-gray-200 dark:border-gray-700 font-mono font-bold">
                            @for($m = 0; $m < 60; $m += 5)
                                <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                            @endfor
                        </select>

                        <!-- AM / PM Pill Switcher -->
                        <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 p-0.5 bg-gray-100 dark:bg-gray-800">
                            <button type="button" 
                                    @click="ampm = 'AM'" 
                                    :class="ampm === 'AM' ? 'bg-white dark:bg-gray-700 text-primary font-bold shadow-2xs' : 'text-gray-400'"
                                    class="px-1.5 py-0.5 text-[10px] rounded-md transition-colors">AM</button>
                            <button type="button" 
                                    @click="ampm = 'PM'" 
                                    :class="ampm === 'PM' ? 'bg-white dark:bg-gray-700 text-primary font-bold shadow-2xs' : 'text-gray-400'"
                                    class="px-1.5 py-0.5 text-[10px] rounded-md transition-colors">PM</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Footer Actions Bar -->
        <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-100 dark:border-gray-800">
            <button type="button" 
                    @click.stop="setNow()" 
                    class="text-[11px] font-bold text-primary hover:text-primary-deep transition-colors">
                Now / Today
            </button>

            <div class="flex items-center gap-1.5">
                <button type="button" 
                        @click.stop="clearValue()" 
                        class="px-2.5 py-1 text-xs font-semibold rounded-lg text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    Clear
                </button>
                <button type="button" 
                        @click.stop="applyValue()" 
                        class="px-3 py-1 text-xs font-bold rounded-lg bg-primary text-white hover:bg-primary-deep transition-colors shadow-xs">
                    Apply
                </button>
            </div>
        </div>
    </div>
</div>
