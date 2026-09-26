<div class="h-[calc(100vh-4rem)] flex flex-col bg-white dark:bg-gray-900 overflow-hidden"
     x-data="{ 
        mobileChatOpen: false, 
        sidebarDetailsOpen: true,
        templateModalOpen: false,
        mediaDropdownOpen: false,
        activeAudio: null,
        playAudio(id) {
            this.activeAudio = (this.activeAudio === id) ? null : id;
        }
     }">

    <!-- ========================================== -->
    <!-- TOP SUB-NAVBAR: Omnichannel Filters       -->
    <!-- ========================================== -->
    <div class="h-12 px-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex items-center justify-between shrink-0 z-20">
        <!-- Channel Filters -->
        <div class="flex items-center gap-1 sm:gap-2 overflow-x-auto no-scrollbar py-1">
            <!-- All Channels -->
            <button wire:click="setChannelFilter('all')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $channelFilter === 'all' ? 'bg-primary text-white shadow-sm shadow-primary/25 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                <span>All Channels</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $channelFilter === 'all' ? 'bg-white/25 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                    {{ $channelCounts['all'] }}
                </span>
            </button>

            <!-- WhatsApp -->
            <button wire:click="setChannelFilter('whatsapp')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $channelFilter === 'whatsapp' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/25 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 hover:text-emerald-600' }}">
                <svg class="w-3.5 h-3.5 text-emerald-500 {{ $channelFilter === 'whatsapp' ? 'text-white' : '' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.086.087-.175.18-.075.352.1.173.444.733.953 1.186.656.585 1.21.766 1.383.852.173.086.275.072.376-.044.101-.115.433-.505.548-.678.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                <span>WhatsApp</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $channelFilter === 'whatsapp' ? 'bg-white/25 text-white' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' }}">
                    {{ $channelCounts['whatsapp'] }}
                </span>
            </button>

            <!-- Instagram -->
            <button wire:click="setChannelFilter('instagram')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $channelFilter === 'instagram' ? 'bg-pink-600 text-white shadow-sm shadow-pink-600/25 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-pink-50 dark:hover:bg-pink-950/30 hover:text-pink-600' }}">
                <svg class="w-3.5 h-3.5 text-pink-500 {{ $channelFilter === 'instagram' ? 'text-white' : '' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                <span>Instagram</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $channelFilter === 'instagram' ? 'bg-white/25 text-white' : 'bg-pink-100 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300' }}">
                    {{ $channelCounts['instagram'] }}
                </span>
            </button>

            <!-- Telegram -->
            <button wire:click="setChannelFilter('telegram')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $channelFilter === 'telegram' ? 'bg-sky-500 text-white shadow-sm shadow-sky-500/25 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-sky-50 dark:hover:bg-sky-950/30 hover:text-sky-500' }}">
                <svg class="w-3.5 h-3.5 text-sky-400 {{ $channelFilter === 'telegram' ? 'text-white' : '' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.34-.675.34l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.894z"/></svg>
                <span>Telegram</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $channelFilter === 'telegram' ? 'bg-white/25 text-white' : 'bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300' }}">
                    {{ $channelCounts['telegram'] }}
                </span>
            </button>

            <!-- Messenger -->
            <button wire:click="setChannelFilter('messenger')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $channelFilter === 'messenger' ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/25 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 hover:text-blue-600' }}">
                <svg class="w-3.5 h-3.5 text-blue-500 {{ $channelFilter === 'messenger' ? 'text-white' : '' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 4.975-12 11.111 0 3.497 1.745 6.616 4.472 8.652v4.237l4.086-2.242c1.09.301 2.246.464 3.442.464 6.627 0 12-4.974 12-11.111 0-6.136-5.373-11.111-12-11.111zm1.193 14.963l-3.056-3.259-5.963 3.259 6.559-6.963 3.13 3.259 5.889-3.259-6.559 6.963z"/></svg>
                <span>Messenger</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $channelFilter === 'messenger' ? 'bg-white/25 text-white' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300' }}">
                    {{ $channelCounts['messenger'] }}
                </span>
            </button>

            <!-- Unassigned -->
            <button wire:click="setChannelFilter('unassigned')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 transition-all {{ $channelFilter === 'unassigned' ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/25 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 hover:text-amber-600' }}">
                <svg class="w-3.5 h-3.5 text-amber-500 {{ $channelFilter === 'unassigned' ? 'text-white' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Unassigned</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $channelFilter === 'unassigned' ? 'bg-white/25 text-white' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' }}">
                    {{ $channelCounts['unassigned'] }}
                </span>
            </button>
        </div>

        <!-- Real-time Agent Status Pill -->
        <div class="hidden sm:flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live Sync (Online)</span>
            </span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MAIN 3-PANEL WORKSPACE CONTAINER           -->
    <!-- ========================================== -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- ========================================== -->
        <!-- PANEL 1: CONVERSATION LIST (320px / w-96)  -->
        <!-- ========================================== -->
        <div :class="mobileChatOpen ? 'hidden md:flex' : 'flex'"
             class="w-full md:w-80 lg:w-96 flex flex-col border-r border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/60 shrink-0">
            
            <!-- Search & Status Filter Header -->
            <div class="p-3.5 border-b border-gray-200 dark:border-gray-800 space-y-2.5 bg-white dark:bg-gray-900">
                <!-- Search Input with Prefix Icon -->
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search chats, phone, messages (⌘K)..."
                           class="w-full pl-9 pr-8 py-2 rounded-xl text-xs bg-gray-100 dark:bg-gray-800 border-none text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-primary/40 focus:bg-white dark:focus:bg-gray-900 transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if ($search)
                        <button wire:click="$set('search', '')" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>

                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800/80 p-1 rounded-xl">
                    @foreach (['open' => 'Open', 'unread' => 'Unread', 'closed' => 'Closed', 'all' => 'All'] as $key => $label)
                        <button type="button"
                                wire:click="$set('statusFilter', '{{ $key }}')"
                                class="flex-1 py-1 rounded-lg text-xs font-semibold text-center transition-all {{ $statusFilter === $key ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-2xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Conversations Scrollable List -->
            <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800/60">
                @forelse ($conversations as $conv)
                    <button wire:click="selectConversation({{ $conv->id }})"
                            @click="mobileChatOpen = true"
                            class="w-full text-left p-3.5 flex items-start gap-3 transition-colors {{ $selectedConversationId === $conv->id ? 'bg-primary/5 dark:bg-primary/10 border-l-4 border-primary' : 'hover:bg-gray-100/60 dark:hover:bg-gray-800/40' }}">
                        
                        <!-- Avatar with Channel Badge -->
                        <div class="relative shrink-0">
                            <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-primary/20 to-indigo-200 dark:from-primary/30 dark:to-indigo-900 flex items-center justify-center text-primary font-bold text-sm">
                                {{ substr($conv->sender_name ?? $conv->sender_mobile ?? 'W', 0, 1) }}
                            </div>

                            <!-- Channel Badge on Avatar -->
                            @if (in_array($conv->channel, ['whatsapp', 'whatsapp_cloud']))
                                <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center ring-2 ring-white dark:ring-gray-900" title="WhatsApp">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.086.087-.175.18-.075.352.1.173.444.733.953 1.186.656.585 1.21.766 1.383.852.173.086.275.072.376-.044.101-.115.433-.505.548-.678.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                                </span>
                            @elseif ($conv->channel === 'instagram')
                                <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full bg-pink-500 text-white flex items-center justify-center ring-2 ring-white dark:ring-gray-900" title="Instagram">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/></svg>
                                </span>
                            @elseif ($conv->channel === 'telegram')
                                <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full bg-sky-500 text-white flex items-center justify-center ring-2 ring-white dark:ring-gray-900" title="Telegram">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.34-.675.34l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.894z"/></svg>
                                </span>
                            @else
                                <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center ring-2 ring-white dark:ring-gray-900" title="Messenger">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 4.975-12 11.111 0 3.497 1.745 6.616 4.472 8.652v4.237l4.086-2.242c1.09.301 2.246.464 3.442.464 6.627 0 12-4.974 12-11.111 0-6.136-5.373-11.111-12-11.111zm1.193 14.963l-3.056-3.259-5.963 3.259 6.559-6.963 3.13 3.259 5.889-3.259-6.559 6.963z"/></svg>
                                </span>
                            @endif
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

                            <!-- Tags and Unread Badges -->
                            <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                @foreach ($conv->tags->take(2) as $tag)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold"
                                          style="background-color: {{ $tag->hex_color }}20; color: {{ $tag->hex_color }}">
                                        {{ $tag->title }}
                                    </span>
                                @endforeach

                                @if ($conv->kanban_stage)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                        {{ ucfirst($conv->kanban_stage) }}
                                    </span>
                                @endif

                                @if ($conv->unread_count > 0)
                                    <span class="ml-auto px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-primary text-white shadow-2xs">
                                        {{ $conv->unread_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="p-8 text-center text-gray-400 text-xs space-y-1">
                        <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <p class="font-medium">No conversations match your filter</p>
                        <p class="text-[11px] text-gray-400">Try switching tabs or clearing your search.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PANEL 2: ACTIVE CONVERSATION CANVAS        -->
        <!-- ========================================== -->
        <div :class="mobileChatOpen ? 'flex' : 'hidden md:flex'"
             class="flex-1 flex flex-col min-w-0 bg-white dark:bg-gray-900 relative">
            
            @if ($selectedConversation)
                <!-- Active Conversation Top Bar -->
                <div class="h-16 px-4 sm:px-6 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-white/90 dark:bg-gray-900/90 backdrop-blur-md z-10 shrink-0">
                    <!-- Contact Meta Info -->
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="mobileChatOpen = false" class="md:hidden p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-primary/20 to-indigo-200 dark:from-primary/30 dark:to-indigo-900 flex items-center justify-center text-primary font-bold text-sm">
                                {{ substr($selectedConversation->sender_name ?? $selectedConversation->sender_mobile ?? 'W', 0, 1) }}
                            </div>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-gray-900"></span>
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h2 class="font-bold text-sm sm:text-base text-gray-900 dark:text-white truncate">
                                    {{ $selectedConversation->sender_name ?? $selectedConversation->sender_mobile }}
                                </h2>
                                
                                <!-- Channel Pill -->
                                @if (in_array($selectedConversation->channel, ['whatsapp', 'whatsapp_cloud']))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.086.087-.175.18-.075.352.1.173.444.733.953 1.186.656.585 1.21.766 1.383.852.173.086.275.072.376-.044.101-.115.433-.505.548-.678.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                                        WhatsApp
                                    </span>
                                @elseif ($selectedConversation->channel === 'instagram')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400">
                                        Instagram DM
                                    </span>
                                @elseif ($selectedConversation->channel === 'telegram')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                                        Telegram
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                                        Messenger
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-mono">{{ $selectedConversation->sender_mobile }}</span>
                                <span>•</span>
                                <span>Assigned: <strong class="text-gray-700 dark:text-gray-200">{{ $selectedConversation->assignedMember?->user?->name ?? 'Unassigned' }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Top Actions: Status, Template Picker, Sidebar Toggle -->
                    <div class="flex items-center gap-2">
                        <!-- Quick Template Dispatcher Button -->
                        <button type="button"
                                @click="templateModalOpen = true"
                                class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-primary/30 bg-primary/5 hover:bg-primary/10 text-primary text-xs font-semibold transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Templates</span>
                        </button>

                        <!-- Status Dropdown -->
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

                        <!-- Toggle Sidebar Info Button -->
                        <button @click="sidebarDetailsOpen = !sidebarDetailsOpen" 
                                class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                :class="sidebarDetailsOpen ? 'text-primary dark:text-primary bg-primary/10' : ''"
                                title="Toggle Contact & CRM Details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Messages Thread Canvas -->
                <div id="messages-container"
                     x-init="$el.scrollTop = $el.scrollHeight"
                     @message-sent.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })"
                     class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 bg-gray-50/40 dark:bg-gray-950/30">
                    
                    <!-- Date Divider -->
                    <div class="flex items-center justify-center my-2">
                        <span class="px-3 py-1 rounded-full text-[11px] font-medium bg-gray-200/70 dark:bg-gray-800 text-gray-600 dark:text-gray-300 shadow-2xs">
                            Today
                        </span>
                    </div>

                    @forelse ($activeMessages as $message)
                        <div class="flex {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] sm:max-w-md lg:max-w-lg space-y-1">
                                
                                <!-- ========================================== -->
                                <!-- 1. VOICE NOTE / AUDIO MESSAGE BUBBLE       -->
                                <!-- ========================================== -->
                                @if ($message->type === 'audio')
                                    <div class="p-3.5 rounded-2xl shadow-2xs text-sm {{ $message->direction === 'outbound' ? 'bg-primary text-white rounded-tr-xs' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-tl-xs border border-gray-100 dark:border-gray-700/60' }}">
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                    @click="playAudio({{ $message->id }})"
                                                    class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $message->direction === 'outbound' ? 'bg-white/20 hover:bg-white/30 text-white' : 'bg-primary text-white hover:bg-primary-deep' }} transition-transform active:scale-95">
                                                <template x-if="activeAudio === {{ $message->id }}">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                                </template>
                                                <template x-if="activeAudio !== {{ $message->id }}">
                                                    <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </template>
                                            </button>

                                            <!-- Audio Waveform Visualizer Simulation -->
                                            <div class="flex-1 space-y-1.5">
                                                <div class="flex items-center gap-0.5 h-6">
                                                    @foreach ([30, 60, 45, 90, 75, 40, 85, 100, 65, 45, 80, 50, 70, 95, 40, 60, 85, 30, 50, 75, 90, 60, 40] as $h)
                                                        <span class="w-1 rounded-full transition-all duration-200 {{ $message->direction === 'outbound' ? 'bg-white/60' : 'bg-primary/40' }}"
                                                              :class="activeAudio === {{ $message->id }} ? 'animate-pulse' : ''"
                                                              style="height: {{ $h }}%;"></span>
                                                    @endforeach
                                                </div>
                                                <div class="flex items-center justify-between text-[11px] {{ $message->direction === 'outbound' ? 'text-white/80' : 'text-gray-500' }}">
                                                    <span>0:14</span>
                                                    <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-black/10">1.5x</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <!-- ========================================== -->
                                <!-- 2. WHATSAPP META TEMPLATE MESSAGE BUBBLE   -->
                                <!-- ========================================== -->
                                @elseif ($message->type === 'template')
                                    <div class="p-4 rounded-2xl shadow-2xs text-sm {{ $message->direction === 'outbound' ? 'bg-indigo-900 text-white rounded-tr-xs border border-indigo-700/50' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-tl-xs border border-gray-200 dark:border-gray-700' }}">
                                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-white/10 dark:border-gray-700">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500 text-white">
                                                Meta Template
                                            </span>
                                            <span class="text-xs font-semibold text-indigo-200 truncate">
                                                Verified Cloud Dispatch
                                            </span>
                                        </div>
                                        <p class="whitespace-pre-wrap break-words leading-relaxed text-xs">{{ $message->content }}</p>
                                    </div>

                                <!-- ========================================== -->
                                <!-- 3. IMAGE / DOCUMENT ATTACHMENT BUBBLE      -->
                                <!-- ========================================== -->
                                @elseif ($message->type === 'image' || $message->type === 'document')
                                    <div class="rounded-2xl overflow-hidden shadow-2xs {{ $message->direction === 'outbound' ? 'bg-primary text-white rounded-tr-xs' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-tl-xs border border-gray-100 dark:border-gray-700' }}">
                                        @if ($message->type === 'image')
                                            <div class="relative group">
                                                <img src="{{ $message->media_url }}" alt="Attachment" class="w-full max-h-60 object-cover">
                                            </div>
                                        @else
                                            <div class="p-3.5 flex items-center gap-3 bg-black/10">
                                                <div class="w-10 h-10 rounded-xl bg-red-500 text-white flex items-center justify-center shrink-0 shadow-sm font-bold text-xs">
                                                    PDF
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-xs truncate">{{ $message->content }}</p>
                                                    <p class="text-[10px] opacity-80">3.4 MB • Document</p>
                                                </div>
                                                <a href="#" class="p-2 rounded-lg bg-white/20 hover:bg-white/30 text-white shrink-0 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            </div>
                                        @endif

                                        @if ($message->caption)
                                            <div class="p-3 text-xs leading-relaxed">
                                                {{ $message->caption }}
                                            </div>
                                        @endif
                                    </div>

                                <!-- ========================================== -->
                                <!-- 4. STANDARD TEXT MESSAGE BUBBLE            -->
                                <!-- ========================================== -->
                                @else
                                    <div class="p-3.5 rounded-2xl shadow-2xs text-sm {{ $message->direction === 'outbound' ? 'bg-primary text-white rounded-tr-xs' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-tl-xs border border-gray-100 dark:border-gray-700/60' }}">
                                        <p class="whitespace-pre-wrap break-words leading-relaxed">{{ $message->content }}</p>
                                    </div>
                                @endif

                                <!-- Message Footer (Timestamp & Double Ticks) -->
                                <div class="flex items-center gap-1.5 text-[10px] text-gray-400 px-1 {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
                                    <span>{{ $message->created_at->format('h:i A') }}</span>

                                    @if ($message->direction === 'outbound')
                                        @if ($message->status === 'read')
                                            <!-- Blue Double Check -->
                                            <span title="Read by customer">
                                                <svg class="w-3.5 h-3.5 text-blue-400" viewBox="0 0 16 16" fill="currentColor"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4 0a.5.5 0 0 0-.708-.708L2 9.293l.646.647 5-5z"/></svg>
                                            </span>
                                        @elseif ($message->status === 'delivered')
                                            <!-- Gray Double Check -->
                                            <span title="Delivered to device">
                                                <svg class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 16 16" fill="currentColor"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4 0a.5.5 0 0 0-.708-.708L2 9.293l.646.647 5-5z"/></svg>
                                            </span>
                                        @elseif ($message->status === 'sent')
                                            <!-- Single Check -->
                                            <span title="Sent to server">
                                                <svg class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 16 16" fill="currentColor"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                                            </span>
                                        @else
                                            <!-- Clock / Sending -->
                                            <svg class="w-3 h-3 text-amber-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center text-gray-400 text-xs py-12">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="font-semibold text-gray-700 dark:text-gray-300">No messages in this chat yet</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Send a greeting or select an AI smart reply below.</p>
                        </div>
                    @endforelse
                </div>

                <!-- ========================================== -->
                <!-- AI SMART REPLIES SUGGESTIONS TOOLBAR       -->
                <!-- ========================================== -->
                <div class="px-4 py-2 bg-gradient-to-r from-primary/5 via-indigo-50/50 to-primary/5 dark:from-primary/10 dark:via-gray-900 dark:to-primary/10 border-t border-primary/15 dark:border-primary/20 flex items-center gap-2 overflow-x-auto no-scrollbar shrink-0">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-primary shrink-0">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                        AI Smart Replies:
                    </span>
                    @foreach ($smartReplies as $reply)
                        <button type="button"
                                wire:click="applySmartReply('{{ addslashes($reply) }}')"
                                class="px-3 py-1 rounded-full text-xs bg-white dark:bg-gray-800 border border-primary/20 hover:border-primary text-gray-700 dark:text-gray-200 hover:text-primary transition-all shadow-2xs truncate max-w-xs shrink-0 active:scale-95 text-left">
                            ⚡ {{ $reply }}
                        </button>
                    @endforeach
                </div>

                <!-- ========================================== -->
                <!-- MESSAGE COMPOSER CONTAINER                 -->
                <!-- ========================================== -->
                <div class="p-3 sm:p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shrink-0">
                    <form wire:submit.prevent="sendMessage" class="space-y-2">
                        
                        <!-- Top Action Buttons Bar (Media, Voice, Template, Emoji) -->
                        <div class="flex items-center justify-between gap-2 px-1">
                            <div class="flex items-center gap-1 sm:gap-2">
                                <!-- Media Attachment Dropdown -->
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                    <button type="button"
                                            @click="open = !open"
                                            class="p-2 rounded-xl text-gray-500 hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                            title="Attach Photo or File">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open"
                                         x-transition
                                         class="absolute bottom-full left-0 mb-2 w-48 rounded-2xl bg-white dark:bg-gray-800 p-1.5 shadow-2xl border border-gray-100 dark:border-gray-700 z-30"
                                         style="display: none;">
                                        <button type="button"
                                                wire:click="sendMediaAttachment('image')"
                                                @click="open = false"
                                                class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/70">
                                            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                📷
                                            </span>
                                            <span>Send Photo / Banner</span>
                                        </button>
                                        <button type="button"
                                                wire:click="sendMediaAttachment('document')"
                                                @click="open = false"
                                                class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/70">
                                            <span class="w-6 h-6 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                                                📄
                                            </span>
                                            <span>Send PDF Proposal</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Voice Note Record / Send Button -->
                                <button type="button"
                                        wire:click="sendVoiceNote"
                                        class="p-2 rounded-xl text-gray-500 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors"
                                        title="Send Voice Note (0:14)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                </button>

                                <!-- WhatsApp Template Modal Trigger -->
                                <button type="button"
                                        @click="templateModalOpen = true"
                                        class="p-2 rounded-xl text-gray-500 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors"
                                        title="Insert Meta WhatsApp Template">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </button>
                            </div>

                            <span class="text-[11px] text-gray-400 hidden sm:inline">
                                Press <strong>Enter ↵</strong> to send, <strong>Shift+Enter</strong> for newline
                            </span>
                        </div>

                        <!-- Textarea Composer -->
                        <div class="flex items-end gap-2 p-2 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                            <textarea wire:model="messageBody"
                                      @keydown.enter.exact.prevent="$wire.sendMessage()"
                                      placeholder="Type your message to {{ $selectedConversation->sender_name ?? $selectedConversation->sender_mobile }}..."
                                      rows="1"
                                      class="flex-1 bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-0 resize-none max-h-32 min-h-[42px] py-2 px-2"></textarea>

                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-deep text-white font-semibold text-xs transition-all flex items-center gap-1.5 shadow-md shadow-primary/25 shrink-0 active:scale-95">
                                <span wire:loading.remove>Send</span>
                                <span wire:loading class="flex items-center gap-1">
                                    <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
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
                    <div class="w-16 h-16 rounded-3xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 mb-3 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Unified Omnichannel Inbox</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm">
                        Select an active chat from the left panel to engage with customers across WhatsApp, Instagram, Telegram, and Messenger.
                    </p>
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- PANEL 3: CONTACT & CRM DETAILS SIDEBAR     -->
        <!-- ========================================== -->
        @if ($selectedConversation)
            <div x-show="sidebarDetailsOpen"
                 class="hidden lg:flex w-80 lg:w-96 flex-col border-l border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/60 shrink-0 overflow-y-auto">
                
                <!-- Contact Profile Card -->
                <div class="p-5 border-b border-gray-200 dark:border-gray-800 text-center space-y-3 bg-white dark:bg-gray-900">
                    <div class="relative w-16 h-16 mx-auto">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-primary to-indigo-600 flex items-center justify-center text-white text-xl font-bold shadow-md shadow-primary/25">
                            {{ substr($selectedConversation->sender_name ?? $selectedConversation->sender_mobile ?? 'W', 0, 1) }}
                        </div>
                        <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-gray-900"></span>
                    </div>

                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-white">
                            {{ $selectedConversation->sender_name ?? 'WhatsApp Contact' }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">
                            {{ $selectedConversation->sender_mobile }}
                        </p>
                    </div>

                    <!-- Quick Contact Actions -->
                    <div class="flex items-center justify-center gap-2 pt-1">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $selectedConversation->sender_mobile) }}" 
                           target="_blank"
                           class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 flex items-center gap-1.5 transition-colors">
                            <span>Open WhatsApp</span>
                        </a>
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ $selectedConversation->sender_mobile }}')"
                                class="p-2 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors"
                                title="Copy Number">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- CRM Kanban Stage & Deal Value Section -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-800 space-y-3 bg-white dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">🎯 CRM Kanban Stage</span>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">$5,000 USD</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] text-gray-400 font-medium">Pipeline Stage</label>
                        <x-select wire:change="updateKanbanStage($event.target.value)" prefixIcon="kanban" size="sm">
                            <option value="lead" @selected($selectedConversation->kanban_stage === 'lead')>🟢 Lead (New Inbound)</option>
                            <option value="contacted" @selected($selectedConversation->kanban_stage === 'contacted')>🔵 Contacted</option>
                            <option value="qualified" @selected($selectedConversation->kanban_stage === 'qualified')>🟣 Qualified</option>
                            <option value="negotiation" @selected($selectedConversation->kanban_stage === 'negotiation')>🟡 Proposal / Negotiation</option>
                            <option value="won" @selected($selectedConversation->kanban_stage === 'won')>🏆 Won / Closed Deal</option>
                            <option value="lost" @selected($selectedConversation->kanban_stage === 'lost')>🔴 Lost / Closed</option>
                        </x-select>
                    </div>
                </div>

                <!-- Assigned Agent Section -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-800 space-y-2 bg-white dark:bg-gray-900">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">👥 Assigned Team Member</span>
                    <x-select wire:change="assignAgent($event.target.value)" prefixIcon="user-circle" size="sm">
                        <option value="">👤 Unassigned</option>
                        @foreach ($teamMembers as $member)
                            <option value="{{ $member->id }}" @selected($selectedConversation->assigned_member_id == $member->id)>
                                {{ $member->user?->name ?? 'Agent' }} ({{ ucfirst($member->role ?? 'Agent') }})
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <!-- Tags & Labels Management Section -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-800 space-y-3 bg-white dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">🏷️ Labels & Tags</span>
                        
                        <!-- Tag Picker Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" 
                                    @click="open = !open" 
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[11px] font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Add Tag</span>
                            </button>

                            <div x-show="open" 
                                 x-transition
                                 class="absolute right-0 mt-1.5 w-48 rounded-2xl bg-white dark:bg-gray-900 p-1.5 shadow-2xl border border-gray-100 dark:border-gray-800 z-30 max-h-56 overflow-y-auto"
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
                                    <div class="px-3 py-2 text-xs text-gray-400 italic">No tags created yet</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        @forelse ($selectedConversation->tags as $tag)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold"
                                  style="background-color: {{ $tag->hex_color }}20; color: {{ $tag->hex_color }}">
                                <span>{{ $tag->title }}</span>
                                <button wire:click="detachTag({{ $tag->id }})" class="hover:opacity-75" title="Remove Tag">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 italic">No tags attached</span>
                        @endforelse
                    </div>
                </div>

                <!-- Internal Notes Collaboration Section -->
                <div class="p-4 space-y-3 flex-1 bg-white dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">🔒 Internal Team Notes</span>
                        <span class="text-[10px] text-gray-400">Agents Only</span>
                    </div>

                    <!-- Add Note Form -->
                    <form wire:submit.prevent="addNote" class="space-y-2">
                        <textarea wire:model="internalNoteBody"
                                  placeholder="Write a private note for other agents..." 
                                  rows="2" 
                                  class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                        <button type="submit" 
                                class="w-full py-1.5 px-3 rounded-xl bg-gray-900 dark:bg-gray-800 text-white font-semibold text-xs hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                            Add Note
                        </button>
                    </form>

                    <!-- Notes Thread -->
                    <div class="space-y-2.5 pt-1">
                        @forelse ($conversationNotes as $note)
                            <div class="p-3 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-800/30 text-xs space-y-1">
                                <div class="flex items-center justify-between text-[10px] text-gray-400">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $note->user->name ?? 'Agent' }}</span>
                                    <span>{{ $note->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $note->note }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-2 italic">No internal notes yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- PRE-APPROVED WHATSAPP TEMPLATES MODAL      -->
    <!-- ========================================== -->
    <div x-show="templateModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.outside="templateModalOpen = false"
             class="w-full max-w-xl rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Meta WhatsApp Template Selector</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Select an approved Meta template to dispatch instantly.</p>
                </div>
                <button type="button" @click="templateModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Templates List -->
            <div class="p-6 overflow-y-auto space-y-3 divide-y divide-gray-100 dark:divide-gray-800/60">
                @foreach ($templates as $tmpl)
                    <div class="pt-3 first:pt-0 flex items-start justify-between gap-4">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-white">{{ $tmpl['title'] }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-primary/10 text-primary">{{ $tmpl['category'] }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">{{ $tmpl['badge'] }}</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/60 p-3 rounded-xl border border-gray-100 dark:border-gray-700/60 font-mono">
                                {{ $tmpl['body'] }}
                            </p>
                        </div>
                        <button type="button"
                                wire:click="sendTemplateMessage('{{ $tmpl['name'] }}')"
                                @click="templateModalOpen = false"
                                class="mt-1 px-3 py-1.5 rounded-xl bg-primary hover:bg-primary-deep text-white text-xs font-semibold shrink-0 transition-colors shadow-sm">
                            Dispatch
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/60 flex justify-end">
                <button type="button" @click="templateModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
