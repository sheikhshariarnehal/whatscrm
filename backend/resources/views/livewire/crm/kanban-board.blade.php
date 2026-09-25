<div class="h-full flex-1 flex flex-col bg-gray-50/60 dark:bg-gray-950/40 overflow-hidden"
     x-data="{ showModal: false }">

    <!-- Kanban Top Toolbar -->
    <div class="p-4 sm:px-6 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 z-10 shrink-0">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">CRM Deals & Lead Pipeline</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Track and advance WhatsApp leads across sales stages</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Search -->
            <div class="relative w-64">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Filter leads..." 
                       class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            <!-- New Stage Button -->
            <button @click="showModal = true" 
                    class="px-3.5 py-1.5 rounded-xl bg-primary hover:bg-primary-deep text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm shadow-primary/20 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Stage</span>
            </button>
        </div>
    </div>

    <!-- Kanban Board Columns (Horizontal Scrolling) -->
    <div class="flex-1 overflow-x-auto p-4 sm:p-6 flex gap-5 items-start">
        
        <!-- Column: Uncategorized / New Inbound -->
        <div class="w-80 shrink-0 flex flex-col max-h-full rounded-2xl bg-gray-100/80 dark:bg-gray-900/80 border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="p-3.5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
                    <h2 class="font-bold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-wider">New Inbound</h2>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    {{ $uncategorized->count() }}
                </span>
            </div>

            <!-- Cards Container -->
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                @forelse ($uncategorized as $conv)
                    <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-sm hover:shadow-md transition-all space-y-2.5 group">
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-bold text-sm text-gray-900 dark:text-white truncate">
                                {{ $conv->sender_name ?? $conv->sender_mobile }}
                            </span>
                            <span class="text-[10px] text-gray-400 shrink-0">{{ $conv->updated_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ $conv->last_message ?? 'No messages' }}
                        </p>
                        
                        <!-- Card Footer -->
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs">
                            <a href="{{ route('inbox') }}" class="text-primary hover:text-primary-deep font-semibold text-[11px] flex items-center gap-1">
                                <span>Chat</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <!-- Move to Stage Dropdown -->
                            <select wire:change="moveConversation({{ $conv->id }}, $event.target.value)" 
                                    class="text-[10px] font-semibold py-0.5 px-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                                <option value="">Move To...</option>
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-gray-400">
                        No unassigned chats.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Dynamic Stage Columns -->
        @foreach ($stages as $stage)
            @php $cards = $stageConversations[$stage->id] ?? collect(); @endphp
            <div class="w-80 shrink-0 flex flex-col max-h-full rounded-2xl bg-gray-100/80 dark:bg-gray-900/80 border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
                <!-- Stage Header -->
                <div class="p-3.5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $stage->hex_color }}"></span>
                        <h2 class="font-bold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-wider">{{ $stage->title }}</h2>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                          style="background-color: {{ $stage->hex_color }}20; color: {{ $stage->hex_color }}">
                        {{ $cards->count() }}
                    </span>
                </div>

                <!-- Cards Container -->
                <div class="flex-1 overflow-y-auto p-3 space-y-3">
                    @forelse ($cards as $conv)
                        <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-sm hover:shadow-md transition-all space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-white truncate">
                                    {{ $conv->sender_name ?? $conv->sender_mobile }}
                                </span>
                                <span class="text-[10px] text-gray-400 shrink-0">{{ $conv->updated_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                {{ $conv->last_message ?? 'No messages' }}
                            </p>

                            <!-- Card Footer -->
                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs">
                                <a href="{{ route('inbox') }}" class="text-primary hover:text-primary-deep font-semibold text-[11px] flex items-center gap-1">
                                    <span>Chat</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                                <!-- Move to Stage Dropdown -->
                                <select wire:change="moveConversation({{ $conv->id }}, $event.target.value)" 
                                        class="text-[10px] font-semibold py-0.5 px-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                                    <option value="">Move To...</option>
                                    @foreach ($stages as $s)
                                        @if ($s->id !== $stage->id)
                                            <option value="{{ $s->id }}">{{ $s->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-gray-400">
                            No deals in this stage.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create Stage Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.outside="showModal = false" 
             class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
            
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-base text-gray-900 dark:text-white">Create New Pipeline Stage</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="createStage" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Stage Title</label>
                    <input wire:model="newTagTitle" 
                           type="text" 
                           placeholder="e.g. Contract Negotiation"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    @error('newTagTitle') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Color Accent</label>
                    <div class="flex items-center gap-2">
                        <input wire:model="newTagColor" type="color" class="w-10 h-10 rounded-lg cursor-pointer border border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $newTagColor }}</span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-primary hover:bg-primary-deep text-white shadow-sm shadow-primary/20">
                        Create Stage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
