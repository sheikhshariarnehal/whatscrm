<div class="h-full flex-1 flex bg-white dark:bg-gray-900 overflow-hidden"
     x-data="{ mobileChatOpen: false, sidebarDetailsOpen: true }">

    <!-- ========================================== -->
    <!-- COLUMN 1: Conversation List (320px / w-80) -->
    <!-- ========================================== -->
    <div :class="mobileChatOpen ? 'hidden md:flex' : 'flex'"
         class="w-full md:w-80 lg:w-96 flex flex-col border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/60 shrink-0">
        
        <!-- Search & Filter Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-800 space-y-3 bg-white dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h1 class="font-bold text-base text-gray-900 dark:text-white tracking-tight">Conversations</h1>
                <x-tag color="primary" class="font-semibold text-[11px]">
                    {{ $conversations->count() }} Active
                </x-tag>
            </div>

            <!-- Search input -->
            <x-input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search name, phone, messages..." 
                prefix-icon="chat"
                size="sm"
            />

            <!-- Filter tabs -->
            <x-tabs variant="pill" class="w-full">
                @foreach (['open' => 'Open', 'unread' => 'Unread', 'closed' => 'Closed', 'all' => 'All'] as $key => $label)
                    <x-tab-item 
                        wire:click="$set('statusFilter', '{{ $key }}')" 
                        variant="pill" 
                        :active="$statusFilter === $key"
                        class="flex-1 text-center"
                    >
                        {{ $label }}
                    </x-tab-item>
                @endforeach
            </x-tabs>
        </div>

        <!-- Conversations Scrollable List -->
        <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800/60">
            @forelse ($conversations as $conv)
                <button wire:click="selectConversation({{ $conv->id }})"
                        @click="mobileChatOpen = true"
                        class="w-full text-left p-3.5 flex items-start gap-3 transition-colors {{ $selectedConversationId === $conv->id ? 'bg-primary/5 dark:bg-primary/10 border-l-4 border-primary' : 'hover:bg-gray-100/60 dark:hover:bg-gray-800/40' }}">
                    
                    <!-- Avatar -->
                    <div class="relative shrink-0">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-primary/20 to-blue-200 dark:from-primary/30 dark:to-blue-900 flex items-center justify-center text-primary font-bold text-sm">
                            {{ substr($conv->sender_name ?? $conv->sender_mobile ?? 'W', 0, 1) }}
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white dark:border-gray-900"></span>
                    </div>

                    <!-- Meta & Preview -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <span class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate">
                                {{ $conv->sender_name ?? $conv->sender_mobile }}
                            </span>
                            <span class="text-[11px] text-gray-400 shrink-0">
                                {{ $conv->updated_at->shortRelativeDiffForHumans() }}
                            </span>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5 {{ $conv->unread_count > 0 ? 'font-semibold text-gray-900 dark:text-white' : '' }}">
                            {{ $conv->last_message ?? 'No messages' }}
                        </p>

                        <!-- Tags and Badges -->
                        <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                            @foreach ($conv->tags->take(2) as $tag)
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold"
                                      style="background-color: {{ $tag->hex_color }}20; color: {{ $tag->hex_color }}">
                                    {{ $tag->title }}
                                </span>
                            @endforeach
                            @if ($conv->unread_count > 0)
                                <span class="ml-auto px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-primary text-white">
                                    {{ $conv->unread_count }}
                                </span>
                            @endif
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-gray-400 text-xs">
                    No conversations match your filter.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================== -->
    <!-- COLUMN 2: Active Chat Area & Composer     -->
    <!-- ========================================== -->
    <div :class="mobileChatOpen ? 'flex' : 'hidden md:flex'"
         class="flex-1 flex flex-col min-w-0 bg-white dark:bg-gray-900">
        
        @if ($selectedConversation)
            <!-- Active Conversation Top Bar -->
            <div class="h-16 px-4 sm:px-6 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-white/80 dark:bg-gray-900/80 backdrop-blur-md z-10 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="mobileChatOpen = false" class="md:hidden p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-primary/20 to-blue-200 dark:from-primary/30 dark:to-blue-900 flex items-center justify-center text-primary font-bold text-sm shrink-0">
                        {{ substr($selectedConversation->sender_name ?? $selectedConversation->sender_mobile ?? 'W', 0, 1) }}
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h2 class="font-bold text-sm sm:text-base text-gray-900 dark:text-white truncate">
                                {{ $selectedConversation->sender_name ?? $selectedConversation->sender_mobile }}
                            </h2>
                            <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                                WhatsApp
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono truncate">
                            {{ $selectedConversation->sender_mobile }}
                        </p>
                    </div>
                </div>

                <!-- Status Selector & Sidebar Toggle -->
                <div class="flex items-center gap-2">
                    <!-- Custom Status Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" 
                                @click="open = !open" 
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/80 transition-all shadow-2xs">
                            @if ($selectedConversation->status === 'open')
                                <span class="w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-emerald-500/20"></span>
                                <span>Open</span>
                            @elseif ($selectedConversation->status === 'pending')
                                <span class="w-2 h-2 rounded-full bg-amber-500 ring-2 ring-amber-500/20"></span>
                                <span>Pending</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-gray-400 ring-2 ring-gray-400/20"></span>
                                <span>Closed</span>
                            @endif
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-150 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-1.5 w-36 rounded-2xl bg-white dark:bg-gray-900 p-1.5 shadow-2xl border border-gray-100 dark:border-gray-800 z-30 focus:outline-none"
                             style="display: none;">
                            <button type="button" 
                                    wire:click="updateStatus('open')" 
                                    @click="open = false" 
                                    class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center justify-between transition-colors {{ $selectedConversation->status === 'open' ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 font-bold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Open</span>
                                </div>
                                @if ($selectedConversation->status === 'open')
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @endif
                            </button>

                            <button type="button" 
                                    wire:click="updateStatus('pending')" 
                                    @click="open = false" 
                                    class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center justify-between transition-colors {{ $selectedConversation->status === 'pending' ? 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 font-bold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span>Pending</span>
                                </div>
                                @if ($selectedConversation->status === 'pending')
                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @endif
                            </button>

                            <button type="button" 
                                    wire:click="updateStatus('closed')" 
                                    @click="open = false" 
                                    class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center justify-between transition-colors {{ $selectedConversation->status === 'closed' ? 'text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 font-bold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    <span>Closed</span>
                                </div>
                                @if ($selectedConversation->status === 'closed')
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @endif
                            </button>
                        </div>
                    </div>

                    <button @click="sidebarDetailsOpen = !sidebarDetailsOpen" 
                            class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                            title="Toggle Contact Details">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                </div>
            </div>

            <!-- Messages Scrollable Thread -->
            <div id="messages-container"
                 x-init="$el.scrollTop = $el.scrollHeight"
                 @message-sent.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })"
                 class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 bg-gray-50/30 dark:bg-gray-950/20">
                
                @forelse ($activeMessages as $message)
                    <div class="flex {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] sm:max-w-md lg:max-w-lg space-y-1">
                            <!-- Message Bubble -->
                            <div class="p-3.5 rounded-2xl shadow-sm text-sm {{ $message->direction === 'outbound' ? 'bg-primary text-white rounded-tr-xs' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-tl-xs border border-gray-100 dark:border-gray-700/60' }}">
                                <p class="whitespace-pre-wrap break-words leading-relaxed">{{ $message->content }}</p>
                            </div>

                            <!-- Message Footer (Timestamp & Delivery Status) -->
                            <div class="flex items-center gap-1.5 text-[10px] text-gray-400 px-1 {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
                                <span>{{ $message->created_at->format('h:i A') }}</span>

                                @if ($message->direction === 'outbound')
                                    @if ($message->status === 'read')
                                        <!-- Blue Double Check -->
                                        <svg class="w-3.5 h-3.5 text-blue-400" viewBox="0 0 16 16" fill="currentColor"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4 0a.5.5 0 0 0-.708-.708L2 9.293l.646.647 5-5z"/></svg>
                                    @elseif ($message->status === 'delivered')
                                        <!-- Gray Double Check -->
                                        <svg class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 16 16" fill="currentColor"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4 0a.5.5 0 0 0-.708-.708L2 9.293l.646.647 5-5z"/></svg>
                                    @elseif ($message->status === 'sent')
                                        <!-- Single Check -->
                                        <svg class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 16 16" fill="currentColor"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                                    @else
                                        <!-- Clock -->
                                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex items-center justify-center text-center text-gray-400 text-xs">
                        No messages in this conversation yet. Send the first reply below.
                    </div>
                @endforelse
            </div>

            <!-- Message Composer Box -->
            <div class="p-3 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shrink-0">
                <form wire:submit.prevent="sendMessage" class="space-y-2">
                    <div class="flex items-end gap-2 p-2 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                        <textarea wire:model="messageBody"
                                  @keydown.enter.exact.prevent="$wire.sendMessage()"
                                  placeholder="Type a message (Press Enter to send, Shift+Enter for newline)..."
                                  rows="1"
                                  class="flex-1 bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-0 resize-none max-h-32 min-h-[38px] py-1 px-2"></textarea>

                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-deep text-white font-semibold text-xs transition-colors flex items-center gap-1.5 shadow-sm shadow-primary/20 shrink-0">
                            <span wire:loading.remove>Send</span>
                            <span wire:loading class="flex items-center gap-1">
                                <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                Sending
                            </span>
                            <svg wire:loading.remove class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Empty state when no conversation is selected -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center text-gray-400">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="font-bold text-base text-gray-900 dark:text-white">No Conversation Selected</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm">
                    Select a conversation from the left to start chatting with your customer.
                </p>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- COLUMN 3: Customer CRM Sidebar (300px)    -->
    <!-- ========================================== -->
    @if ($selectedConversation)
        <div x-show="sidebarDetailsOpen"
             class="hidden lg:flex w-80 flex-col border-l border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/60 shrink-0 overflow-y-auto">
            
            <!-- Contact Profile Card -->
            <div class="p-5 border-b border-gray-200 dark:border-gray-800 text-center space-y-2">
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-primary to-indigo-500 flex items-center justify-center text-white text-xl font-bold mx-auto shadow-md shadow-primary/20">
                    {{ substr($selectedConversation->sender_name ?? $selectedConversation->sender_mobile ?? 'W', 0, 1) }}
                </div>
                <div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">
                        {{ $selectedConversation->sender_name ?? 'WhatsApp Contact' }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                        {{ $selectedConversation->sender_mobile }}
                    </p>
                </div>
            </div>

            <!-- Tags Management Section -->
            <div class="p-5 border-b border-gray-200 dark:border-gray-800 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tags / Pipeline</span>
                    
                    <!-- Tag Picker Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" 
                                @click="open = !open" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/80 transition-all shadow-2xs">
                            <svg class="w-3 h-3 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Tag</span>
                            <svg class="w-3 h-3 text-gray-400 transition-transform duration-150 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-1.5 w-48 rounded-2xl bg-white dark:bg-gray-900 p-1.5 shadow-2xl border border-gray-100 dark:border-gray-800 z-30 max-h-56 overflow-y-auto focus:outline-none"
                             style="display: none;">
                            @forelse ($availableTags as $tag)
                                <button type="button" 
                                        wire:click="attachTag({{ $tag->id }})" 
                                        @click="open = false" 
                                        class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $tag->hex_color }}"></span>
                                    <span class="truncate">{{ $tag->title }}</span>
                                </button>
                            @empty
                                <div class="px-3 py-2 text-xs text-gray-400 italic">No tags created</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    @forelse ($selectedConversation->tags as $tag)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold"
                              style="background-color: {{ $tag->hex_color }}20; color: {{ $tag->hex_color }}">
                            <span>{{ $tag->title }}</span>
                            <button wire:click="detachTag({{ $tag->id }})" class="hover:opacity-75">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </span>
                    @empty
                        <span class="text-xs text-gray-400">No tags attached</span>
                    @endforelse
                </div>
            </div>

            <!-- Internal Notes Section -->
            <div class="p-5 space-y-4 flex-1">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Internal Agent Notes</span>

                <!-- Add Note Form -->
                <form wire:submit.prevent="addNote" class="space-y-2">
                    <textarea wire:model="internalNoteBody"
                              placeholder="Write a private note for agents..." 
                              rows="2" 
                              class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                    <button type="submit" 
                            class="w-full py-1.5 px-3 rounded-lg bg-gray-900 dark:bg-gray-800 text-white font-semibold text-xs hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                        Add Note
                    </button>
                </form>

                <!-- Notes Thread -->
                <div class="space-y-2.5">
                    @forelse ($conversationNotes as $note)
                        <div class="p-3 rounded-xl bg-white dark:bg-gray-800/80 border border-gray-100 dark:border-gray-700 text-xs space-y-1">
                            <div class="flex items-center justify-between text-[10px] text-gray-400">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $note->user->name ?? 'Agent' }}</span>
                                <span>{{ $note->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $note->note }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-2">No internal notes yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
