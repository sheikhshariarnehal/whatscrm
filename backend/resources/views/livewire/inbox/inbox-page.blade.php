<div class="h-[calc(100vh-4rem)] p-2 sm:p-2.5 bg-[#f0f2f5] dark:bg-[#0c1317] flex font-sans overflow-hidden"
     wire:poll.10s.visible="refreshMessages"
     x-data="{ 
        mobileChatOpen: false, 
        sidebarDetailsOpen: window.innerWidth >= 1200,
        templateModalOpen: @entangle('templateModalOpen'),
        newChatModalOpen: @entangle('newChatModalOpen'),
        activeAudio: null,
        playAudio(id) {
            this.activeAudio = (this.activeAudio === id) ? null : id;
        }
     }">

    <!-- Main WhatsApp Card Container with rounded corners matching WhatsApp desktop app -->
    <div class="flex-1 flex overflow-hidden rounded-2xl border border-[#d1d7db] dark:border-[#222e35] shadow-xs bg-white dark:bg-[#111b21] relative">

        <!-- ============================================================== -->
        <!-- LEFT PANEL: CHATS LIST (WhatsApp Web Side Panel)               -->
        <!-- ============================================================== -->
        <div :class="mobileChatOpen ? 'hidden md:flex' : 'flex'"
             class="wa-sidebar-width flex flex-col border-r border-[#d1d7db] dark:border-[#222e35] bg-white dark:bg-[#111b21] shrink-0 z-20">
        
        <!-- 1. Header: 'Chats' title + 3-dots + New Chat Button -->
        <div class="h-16 px-4 bg-white dark:bg-[#111b21] flex items-center justify-between shrink-0">
            <h1 class="text-2xl font-bold text-[#111b21] dark:text-[#e9edef] tracking-tight">Chats</h1>
            
            <div class="flex items-center gap-2">
                <!-- 3-Dots Menu -->
                <div class="relative" x-data="{ menuOpen: false }" @click.outside="menuOpen = false">
                    <button type="button" 
                            @click="menuOpen = !menuOpen" 
                            class="p-2 text-[#54656f] dark:text-[#aebac1] hover:bg-black/5 dark:hover:bg-white/5 rounded-full transition-colors cursor-pointer"
                            title="Menu">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 7a2 2 0 100-4 2 2 0 000 4zm0 7a2 2 0 100-4 2 2 0 000 4zm0 7a2 2 0 100-4 2 2 0 000 4z"/></svg>
                    </button>
                    
                    <div x-show="menuOpen" 
                         x-transition
                         class="absolute right-0 mt-1 w-48 rounded-2xl bg-white dark:bg-[#233138] py-2 shadow-xl border border-gray-100 dark:border-gray-800 z-50 text-xs font-medium text-[#111b21] dark:text-[#d1d7db]"
                         style="display: none;">
                        <button type="button" @click="newChatModalOpen = true; menuOpen = false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-[#182229]">New Chat</button>
                        <button type="button" wire:click="$set('statusFilter', 'unread')" @click="menuOpen = false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-[#182229]">Unread Chats</button>
                        <button type="button" wire:click="$set('statusFilter', 'all')" @click="menuOpen = false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-[#182229]">Select All Chats</button>
                    </div>
                </div>

                <!-- Green Circle '+ New Chat Message Bubble' Button -->
                <button type="button"
                        @click="newChatModalOpen = true"
                        class="w-9 h-9 rounded-full bg-[#00a884] hover:bg-[#008f72] text-white flex items-center justify-center shadow-xs transition-transform active:scale-95 cursor-pointer ml-1"
                        title="New Chat">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.005 3.175H4.674C3.642 3.175 2.8 4.017 2.8 5.049v10.518c0 1.032.842 1.874 1.874 1.874h2.15v3.428a.5.5 0 0 0 .848.361l3.968-3.789h7.365c1.032 0 1.874-.842 1.874-1.874V5.049c0-1.032-.842-1.874-1.874-1.874zm-3.155 7.65h-3.07v3.07a.9.9 0 1 1-1.8 0v-3.07H7.91a.9.9 0 0 1 0-1.8h3.07V5.955a.9.9 0 1 1 1.8 0v3.07h3.07a.9.9 0 0 1 0 1.8z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- 2. Search Bar (Full Rounded Capsule) -->
        <div class="px-3 py-1 bg-white dark:bg-[#111b21]">
            <div class="relative bg-[#f0f2f5] dark:bg-[#202c33] rounded-full flex items-center px-4 py-2 gap-3">
                <svg class="w-4 h-4 text-[#54656f] dark:text-[#aebac1] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search or start a new chat"
                       class="bg-transparent text-[14px] text-[#111b21] dark:text-[#e9edef] placeholder-[#667781] dark:placeholder-[#8696a0] outline-none w-full border-none p-0 focus:ring-0">
                @if ($search)
                    <button wire:click="$set('search', '')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>
        </div>

        <!-- 3. Filter Chips Row (All, Unread 10, Favourites, ▾ Dropdown) -->
        <div class="px-3 py-2 bg-white dark:bg-[#111b21] flex items-center gap-1.5 overflow-visible relative z-30">
            <!-- All Filter -->
            <button type="button"
                    wire:click="setChannelFilter('all')"
                    class="px-3.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap transition-all select-none cursor-pointer bg-[#d9fdd3] dark:bg-[#005c4b]/50 text-[#008069] dark:text-[#25d366] border border-[#d9fdd3] dark:border-[#005c4b]">
                <span>All</span>
            </button>

            <!-- Unread Filter -->
            <button type="button"
                    wire:click="$set('statusFilter', '{{ $statusFilter === 'unread' ? 'all' : 'unread' }}')"
                    class="px-3.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap transition-all select-none cursor-pointer {{ $statusFilter === 'unread' ? 'bg-[#d9fdd3] dark:bg-[#005c4b]/50 text-[#008069] dark:text-[#25d366] border border-[#d9fdd3]' : 'bg-white dark:bg-[#202c33] text-[#54656f] dark:text-[#8696a0] border border-[#e9edef] dark:border-gray-700 hover:bg-gray-50' }}">
                <span>Unread 10</span>
            </button>

            <!-- Favourites Filter -->
            <button type="button"
                    class="px-3.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap transition-all select-none cursor-pointer bg-white dark:bg-[#202c33] text-[#54656f] dark:text-[#8696a0] border border-[#e9edef] dark:border-gray-700 hover:bg-gray-50">
                <span>Favourites</span>
            </button>

            <!-- Filter Dropdown ▾ -->
            <div class="relative" x-data="{ filterDropdownOpen: false }" @click.outside="filterDropdownOpen = false">
                <button type="button"
                        @click="filterDropdownOpen = !filterDropdownOpen"
                        class="w-7 h-7 rounded-full bg-white dark:bg-[#202c33] border border-[#e9edef] dark:border-gray-700 flex items-center justify-center text-[#54656f] dark:text-[#8696a0] hover:bg-gray-50 transition-colors cursor-pointer"
                        title="Filter options">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="filterDropdownOpen"
                     x-transition
                     class="absolute right-0 sm:left-0 top-full mt-2 w-44 rounded-2xl bg-white dark:bg-[#202c33] p-1.5 shadow-[0_4px_24px_rgba(0,0,0,0.12)] border border-gray-100 dark:border-gray-700 z-50 text-sm font-normal text-[#111b21] dark:text-[#d1d7db]"
                     style="display: none;">
                    <button type="button" @click="filterDropdownOpen = false" class="w-full text-left px-3 py-2 rounded-xl hover:bg-gray-50 dark:hover:bg-[#182229] flex items-center justify-between transition-colors">
                        <span class="text-[#111b21] dark:text-[#e9edef] font-medium text-sm">Groups</span>
                        <span class="text-xs text-[#54656f] dark:text-[#8696a0]">3</span>
                    </button>
                    <div class="h-px bg-gray-100 dark:bg-gray-700 my-1"></div>
                    <button type="button" @click="newChatModalOpen = true; filterDropdownOpen = false" class="w-full text-left px-3 py-2 rounded-xl hover:bg-gray-50 dark:hover:bg-[#182229] flex items-center gap-2.5 transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#111b21] dark:text-[#e9edef]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span class="text-[#111b21] dark:text-[#e9edef] font-medium text-sm">New list</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4. Conversations List (with rounded highlight cards) -->
        <div class="flex-1 overflow-y-auto p-1.5 space-y-1 bg-white dark:bg-[#111b21]">
            @forelse ($conversations as $conv)
                <button wire:key="conv-card-{{ $conv->id }}"
                        wire:click="selectConversation({{ $conv->id }})"
                        @click="mobileChatOpen = true; if (window.innerWidth >= 1200) sidebarDetailsOpen = true;"
                        class="w-full text-left px-3 py-2.5 rounded-2xl flex items-center gap-3 transition-colors duration-150 cursor-pointer select-none {{ $selectedConversationId === $conv->id ? 'bg-[#f0f2f5] dark:bg-[#2a3942]' : 'hover:bg-[#f5f6f6] dark:hover:bg-[#202c33]' }}">
                    
                    <!-- Circular Avatar with channel indicator -->
                    <div class="relative shrink-0">
                        @if ($conv->sender_name === 'Interakt')
                            <div class="w-12 h-12 rounded-full bg-white dark:bg-[#202c33] border border-gray-100 dark:border-gray-700 flex items-center justify-center shadow-2xs">
                                <svg class="w-7 h-7 text-[#005c4b]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12zm-7-8c-1.66 0-3-1.34-3-3H7c0 2.76 2.24 5 5 5s5-2.24 5-5h-2c0 1.66-1.34 3-3 3z"/>
                                </svg>
                            </div>
                        @elseif ($conv->sender_name === 'LeminAi')
                            <div class="w-12 h-12 rounded-full bg-amber-50 dark:bg-amber-950 flex items-center justify-center text-amber-500 font-bold text-2xl shadow-2xs border border-amber-100 dark:border-amber-800">
                                🍋
                            </div>
                        @elseif (str_contains($conv->sender_name ?? '', 'SRC 365'))
                            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-950 border border-rose-200 dark:border-rose-800 flex items-center justify-center text-rose-600 dark:text-rose-400 font-bold text-[11px] shadow-2xs">
                                SRC 365
                            </div>
                        @elseif (str_contains($conv->sender_name ?? '', 'Sandesh'))
                            <div class="w-12 h-12 rounded-full bg-white dark:bg-[#202c33] border border-gray-200 dark:border-gray-700 flex items-center justify-center text-[#00a884] font-bold text-[10px] shadow-2xs">
                                Sandesh
                            </div>
                        @elseif (str_contains($conv->sender_name ?? '', 'Business'))
                            <div class="w-12 h-12 rounded-full bg-[#25d366]/10 flex items-center justify-center text-[#25d366] font-bold text-xl shadow-2xs border border-[#25d366]/20">
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.086.087-.175.18-.075.352.1.173.444.733.953 1.186.656.585 1.21.766 1.383.852.173.086.275.072.376-.044.101-.115.433-.505.548-.678.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                            </div>
                        @elseif ($conv->avatar_url)
                            <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-200 dark:border-gray-700 shadow-2xs">
                                <img src="{{ $conv->avatar_url }}" alt="{{ $conv->sender_name }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>

                    <!-- Meta info & Preview text -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <span class="font-semibold text-sm text-[#111b21] dark:text-[#e9edef] truncate {{ $conv->unread_count > 0 ? 'font-bold' : '' }}">
                                {{ $conv->sender_name ?? $conv->sender_mobile }}
                            </span>
                            <span class="text-[11px] shrink-0 font-normal {{ $conv->unread_count > 0 ? 'text-[#00a884] dark:text-[#25d366] font-bold' : 'text-[#667781] dark:text-[#8696a0]' }}">
                                @if ($conv->updated_at->isToday())
                                    {{ $conv->updated_at->format('g:i a') }}
                                @elseif ($conv->updated_at->isYesterday())
                                    Yesterday
                                @elseif ($conv->updated_at->greaterThan(now()->subDays(6)))
                                    {{ $conv->updated_at->format('l') }}
                                @else
                                    {{ $conv->updated_at->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-1 mt-0.5">
                            <p class="text-xs text-[#667781] dark:text-[#8696a0] truncate {{ $conv->unread_count > 0 ? 'font-bold text-[#111b21] dark:text-[#e9edef]' : '' }}">
                                @if (str_contains($conv->sender_name ?? '', 'Jiad'))
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-[#8696a0]" viewBox="0 0 16 16" fill="currentColor"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4 0a.5.5 0 0 0-.708-.708L2 9.293l.646.647 5-5z"/></svg>
                                        <span>📄 Dhaka Mobile Shop Leads-2026...</span>
                                    </span>
                                @elseif (str_contains($conv->sender_name ?? '', '1629'))
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-[#8696a0]" viewBox="0 0 16 16" fill="currentColor"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4 0a.5.5 0 0 0-.708-.708L2 9.293l.646.647 5-5z"/></svg>
                                        <span>HI</span>
                                    </span>
                                @elseif ($conv->last_message)
                                    <span>{{ $conv->last_message }}</span>
                                @else
                                    <span>No messages yet</span>
                                @endif
                            </p>

                            @if ($conv->unread_count > 0)
                                <span class="w-5 h-5 rounded-full bg-[#25d366] text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                    {{ $conv->unread_count }}
                                </span>
                            @endif
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-gray-400 text-xs space-y-2">
                    <p class="font-bold text-gray-700 dark:text-gray-300">No chats found</p>
                    <button type="button" @click="newChatModalOpen = true" class="text-xs text-[#00a884] font-bold hover:underline">
                        + Start a new chat
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- CENTER PANEL: MAIN CHAT AREA (WhatsApp Web Wallpaper & Bubbles)-->
    <!-- ============================================================== -->
    <div :class="mobileChatOpen ? 'flex' : 'hidden md:flex'"
         class="flex-1 flex flex-col min-w-0 bg-[#efeae2] dark:bg-[#0b141a] relative">
        
        @if ($selectedConversation)
            <!-- 1. WhatsApp Web Chat Header -->
            <div class="h-16 px-4 bg-[#f0f2f5] dark:bg-[#202c33] border-b border-[#d1d7db] dark:border-[#222e35] flex items-center justify-between z-10 shrink-0">
                <!-- Left: Contact Profile & Subtitle (Clickable to open Contact Info) -->
                <div class="flex items-center gap-3 min-w-0 flex-1 cursor-pointer p-1.5 -ml-1.5 rounded-xl hover:bg-black/5 dark:hover:bg-white/5 transition-colors group" 
                     @click="sidebarDetailsOpen = !sidebarDetailsOpen"
                     title="Click to view Contact Profile & Details">
                    <button @click.stop="mobileChatOpen = false" class="md:hidden p-1 text-gray-500 hover:bg-gray-200 rounded-lg">
                        <x-ph-icon name="arrow-left" weight="bold" class="text-base" />
                    </button>

                    <div class="relative shrink-0">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200 dark:border-gray-700 shadow-2xs ring-2 ring-emerald-500/20">
                            <img src="{{ $selectedConversation->avatar_url }}" alt="{{ $selectedConversation->sender_name }}" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <h2 class="font-semibold text-base text-[#111b21] dark:text-[#e9edef] truncate group-hover:text-[#008069] dark:group-hover:text-[#00a884] transition-colors">
                                {{ $selectedConversation->sender_name ?? $selectedConversation->sender_mobile }}
                            </h2>
                            <x-tag color="primary" class="text-[10px] uppercase font-bold py-0 px-1.5 hidden 2xl:inline-flex shrink-0">
                                {{ ucfirst($selectedConversation->kanban_stage ?? 'Lead') }}
                            </x-tag>
                        </div>
                        <p class="text-[11px] text-[#667781] dark:text-[#8696a0] font-mono leading-tight flex items-center gap-1.5 mt-0.5 truncate">
                            <span>{{ $selectedConversation->sender_mobile }}</span>
                            <span class="text-gray-300 dark:text-gray-600">•</span>
                            <span class="text-[#008069] dark:text-[#00a884] font-medium group-hover:underline">Contact Profile</span>
                        </p>
                    </div>
                </div>

                <!-- Right Action Icons (Profile Button, Video, Call, Divider, Search, 3-Dots Menu) -->
                <div class="flex items-center gap-1 sm:gap-1.5 text-[#54656f] dark:text-[#aebac1] shrink-0">
                    <!-- Dedicated Contact Profile Toggle Button -->
                    <button type="button" 
                            @click="sidebarDetailsOpen = !sidebarDetailsOpen" 
                            :class="sidebarDetailsOpen ? 'bg-[#008069]/10 text-[#008069] dark:bg-[#00a884]/20 dark:text-[#00a884] font-bold border border-[#008069]/30' : 'text-[#54656f] dark:text-[#aebac1] hover:bg-black/5 dark:hover:bg-white/5 border border-transparent'"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer whitespace-nowrap"
                            title="Toggle Contact Profile & CRM Details">
                        <x-ph-icon name="user-circle" weight="bold" class="text-lg" />
                        <span class="hidden md:inline font-semibold">Profile</span>
                    </button>

                    <!-- Video Call Button -->
                    <button type="button" class="p-2 hover:bg-black/5 dark:hover:bg-white/5 rounded-full transition-colors" title="Video call">
                        <x-ph-icon name="video-camera" weight="bold" class="text-base" />
                    </button>

                    <!-- Phone Call Button -->
                    <button type="button" class="p-2 hover:bg-black/5 dark:hover:bg-white/5 rounded-full transition-colors" title="Voice call">
                        <x-ph-icon name="phone" weight="bold" class="text-base" />
                    </button>

                    <!-- Divider -->
                    <div class="h-5 w-px bg-gray-300 dark:bg-gray-700 mx-0.5 hidden sm:block"></div>

                    <!-- Search In Chat -->
                    <button type="button" class="p-2 hover:bg-black/5 dark:hover:bg-white/5 rounded-full transition-colors" title="Search in chat">
                        <x-ph-icon name="magnifying-glass" weight="bold" class="text-base" />
                    </button>

                    <!-- 3-Dots Menu Dropdown (Includes Templates and Contact Info) -->
                    <div class="relative" x-data="{ chatMenuOpen: false }" @click.outside="chatMenuOpen = false">
                        <button type="button" 
                                @click="chatMenuOpen = !chatMenuOpen" 
                                class="p-2 hover:bg-black/5 dark:hover:bg-white/5 rounded-full transition-colors"
                                title="Menu">
                            <x-ph-icon name="dots-three-vertical" weight="bold" class="text-base" />
                        </button>
                        
                        <div x-show="chatMenuOpen" 
                             x-transition
                             class="absolute right-0 mt-1 w-52 rounded-xl bg-white dark:bg-[#233138] py-1.5 shadow-xl border border-gray-100 dark:border-gray-800 z-50 text-xs font-medium text-[#111b21] dark:text-[#d1d7db]"
                             style="display: none;">
                            <button type="button" @click="sidebarDetailsOpen = !sidebarDetailsOpen; chatMenuOpen = false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-[#182229] flex items-center justify-between">
                                <span class="flex items-center gap-2"><x-ph-icon name="user-circle" weight="bold" class="text-sm" /> Contact Profile</span>
                                <span class="text-[10px] text-gray-400">CRM</span>
                            </button>
                            <button type="button" @click="templateModalOpen = true; chatMenuOpen = false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-[#182229] flex items-center justify-between">
                                <span class="flex items-center gap-2"><x-ph-icon name="file-text" weight="bold" class="text-sm" /> Templates</span>
                                <span class="text-[10px] text-emerald-500 font-bold">Meta</span>
                            </button>
                            <button type="button" wire:click="switchChannelToMeta" @click="chatMenuOpen = false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-[#182229] flex items-center gap-2">
                                <x-ph-icon name="meta-logo" weight="fill" class="text-sm" /> Switch to Meta Cloud
                            </button>
                            <button type="button" wire:click="switchChannelToBaileys" @click="chatMenuOpen = false" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-[#182229] flex items-center gap-2">
                                <x-ph-icon name="whatsapp-logo" weight="fill" class="text-sm" /> Switch to Baileys Session
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Chat Canvas with WhatsApp Doodle Pattern -->
            <div id="messages-container"
                 wire:key="messages-thread-{{ $selectedConversation->id }}"
                 x-init="$el.scrollTop = $el.scrollHeight"
                 @message-sent.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })"
                 class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 wa-chat-pattern">
                
                <!-- Messages Thread Loop -->
                @forelse ($activeMessages as $message)
                    @if (!empty(trim($message->content ?? '')) || !empty($message->media_url) || $message->type === 'collage')
                        <div wire:key="msg-bubble-{{ $message->id }}" class="flex {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] sm:max-w-md lg:max-w-lg space-y-1">
                                
                                <!-- 2x2 Image Collage Display (Authentic WhatsApp Multi-Photo Grid) -->
                                @if ($message->type === 'collage')
                                    <div class="p-1 rounded-2xl shadow-xs {{ $message->direction === 'outbound' ? 'wa-bubble-out bg-[#d9fdd3] dark:bg-[#005c4b]' : 'wa-bubble-in bg-white dark:bg-[#202c33]' }}">
                                        <div class="grid grid-cols-2 gap-1 rounded-xl overflow-hidden w-64 h-64 sm:w-72 sm:h-72 bg-black/10">
                                            <!-- Photo 1: Top-Left -->
                                            <div class="relative overflow-hidden w-full h-full rounded-tl-xl">
                                                <img src="https://images.unsplash.com/photo-1544717305-2782549b5136?w=400&h=400&fit=crop" alt="Photo 1" class="w-full h-full object-cover">
                                            </div>
                                            <!-- Photo 2: Top-Right -->
                                            <div class="relative overflow-hidden w-full h-full rounded-tr-xl">
                                                <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=400&h=400&fit=crop" alt="Photo 2" class="w-full h-full object-cover">
                                            </div>
                                            <!-- Photo 3: Bottom-Left -->
                                            <div class="relative overflow-hidden w-full h-full rounded-bl-xl">
                                                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=400&h=400&fit=crop" alt="Photo 3" class="w-full h-full object-cover">
                                            </div>
                                            <!-- Photo 4: Bottom-Right with +10 Badge -->
                                            <div class="relative overflow-hidden w-full h-full rounded-br-xl">
                                                <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=400&h=400&fit=crop" alt="Photo 4" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/45 flex items-center justify-center">
                                                    <span class="text-white text-3xl font-bold tracking-wider select-none">+10</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Timestamp & Checkmarks -->
                                        <div class="flex items-center justify-end gap-1 text-[11px] text-[#667781] dark:text-[#8696a0] px-1 pt-1 pb-0.5">
                                            <span>8:35 am</span>
                                            @if ($message->direction === 'outbound')
                                                <svg class="w-4 h-4 text-[#53bdeb]" viewBox="0 0 16 16" fill="currentColor"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4 0a.5.5 0 0 0-.708-.708L2 9.293l.646.647 5-5z"/></svg>
                                            @endif
                                        </div>
                                    </div>

                                <!-- Audio / Voice Note Bubble -->
                                @elseif ($message->type === 'audio')
                                    <div class="p-3.5 rounded-2xl shadow-xs text-sm {{ $message->direction === 'outbound' ? 'wa-bubble-out' : 'wa-bubble-in' }}">
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                    @click="playAudio({{ $message->id }})"
                                                    class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-[#00a884] text-white hover:bg-[#008f72] transition-transform active:scale-95 shadow-sm">
                                                <template x-if="activeAudio === {{ $message->id }}">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                                </template>
                                                <template x-if="activeAudio !== {{ $message->id }}">
                                                    <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </template>
                                            </button>

                                            <!-- Audio Waveform Visualizer -->
                                            <div class="flex-1 space-y-1.5">
                                                <div class="flex items-center gap-0.5 h-6">
                                                    @foreach ([30, 60, 45, 90, 75, 40, 85, 100, 65, 45, 80, 50, 70, 95, 40, 60, 85, 30, 50, 75, 90, 60, 40] as $h)
                                                        <span class="w-1 rounded-full transition-all duration-200 {{ $message->direction === 'outbound' ? 'bg-[#00a884]/80' : 'bg-gray-400' }}"
                                                              :class="activeAudio === {{ $message->id }} ? 'animate-pulse' : ''"
                                                              style="height: {{ $h }}%;"></span>
                                                    @endforeach
                                                </div>
                                                <div class="flex items-center justify-between text-[11px] text-[#667781] dark:text-[#8696a0]">
                                                    <span>0:14</span>
                                                    <span class="font-mono text-[10px] px-1.5 py-0.2 rounded bg-black/5 dark:bg-white/10 font-bold">1.5x</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <!-- Image / Photo Attachment Bubble -->
                                @elseif ($message->type === 'image' || $message->type === 'video' || $message->type === 'document')
                                    <div class="rounded-2xl overflow-hidden shadow-xs {{ $message->direction === 'outbound' ? 'wa-bubble-out' : 'wa-bubble-in' }}">
                                        @if ($message->type === 'image')
                                            <div class="relative group p-1">
                                                <a href="{{ $message->media_url }}" target="_blank">
                                                    <img src="{{ $message->media_url }}" alt="Attachment" class="w-full max-h-72 object-cover rounded-xl">
                                                </a>
                                            </div>
                                        @elseif ($message->type === 'video')
                                            <div class="p-1">
                                                <video controls src="{{ $message->media_url }}" class="w-full max-h-72 rounded-xl"></video>
                                            </div>
                                        @else
                                            <div class="p-3.5 flex items-center gap-3 bg-black/5">
                                                <div class="w-10 h-10 rounded-lg bg-red-500 text-white flex items-center justify-center shrink-0 shadow-sm font-bold text-xs uppercase">
                                                    PDF
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-xs truncate">{{ $message->content }}</p>
                                                    <p class="text-[10px] opacity-75">Document Attachment</p>
                                                </div>
                                                <a href="{{ $message->media_url }}" target="_blank" download class="p-2 rounded-lg bg-black/10 hover:bg-black/20 text-gray-700 dark:text-white shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            </div>
                                        @endif

                                        @if ($message->caption)
                                            <div class="p-3 text-xs leading-relaxed whitespace-pre-wrap">
                                                {{ $message->caption }}
                                            </div>
                                        @endif
                                    </div>

                                <!-- Standard WhatsApp Text Bubble (Inbound or Outbound) -->
                                @else
                                    <div class="p-4 rounded-2xl shadow-xs text-sm {{ $message->direction === 'outbound' ? 'wa-bubble-out' : 'wa-bubble-in bg-white dark:bg-[#202c33] border border-black/5 dark:border-white/5' }}">
                                        <p class="whitespace-pre-wrap break-words leading-relaxed text-[#111b21] dark:text-[#e9edef] text-[14px]">{{ $message->content }}</p>
                                        
                                        <!-- Bottom-right Timestamp & Double Check Ticks -->
                                        <div class="flex items-center justify-end gap-1 text-[11px] text-[#667781] dark:text-[#8696a0] mt-1.5 -mb-1">
                                            <span>{{ $message->created_at->format('g:i a') }}</span>
                                            @if ($message->direction === 'outbound')
                                                @if ($message->status === 'failed')
                                                    <span class="text-red-500 flex items-center gap-0.5 cursor-pointer" title="{{ $message->metadata['error'] ?? $message->metadata['failed_reason'] ?? 'Delivery failed' }}">
                                                        <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                    </span>
                                                @elseif ($message->status === 'read')
                                                    <svg class="w-4 h-4 text-[#53bdeb]" viewBox="0 0 16 16" fill="currentColor"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4 0a.5.5 0 0 0-.708-.708L2 9.293l.646.647 5-5z"/></svg>
                                                @elseif ($message->status === 'delivered')
                                                    <svg class="w-4 h-4 text-[#8696a0]" viewBox="0 0 16 16" fill="currentColor"><path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4 0a.5.5 0 0 0-.708-.708L2 9.293l.646.647 5-5z"/></svg>
                                                @elseif ($message->status === 'pending')
                                                    <svg class="w-3.5 h-3.5 text-[#8696a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" d="M12 7v5l3 2"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-[#8696a0]" viewBox="0 0 16 16" fill="currentColor"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- WhatsApp End-to-End Encryption Notice Pill (Centered between collage and Bengali message) -->
                        @if ($message->type === 'collage')
                            <div class="py-1.5 px-4 rounded-lg bg-[#ffeecd] dark:bg-[#182229] text-[#54656f] dark:text-[#8696a0] text-[11.5px] text-center max-w-lg mx-auto shadow-2xs leading-relaxed flex items-center justify-center gap-1.5 my-3 border border-amber-200/50 dark:border-amber-900/30">
                                <svg class="w-3.5 h-3.5 text-amber-700 dark:text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                <span>Messages and calls are now end-to-end encrypted. Only people in this chat can read, listen to, or share them. Click to learn more</span>
                            </div>
                        @endif
                    @endif
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center text-gray-400 text-xs py-12">
                        <p class="font-bold text-gray-700 dark:text-gray-300">No messages in this chat yet</p>
                    </div>
                @endforelse
            </div>

            <!-- Real Media Attachment Preview -->
            @if ($attachment)
                <div class="px-4 py-2 bg-emerald-50 dark:bg-emerald-950/40 border-t border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-7 h-7 rounded-lg bg-[#00a884] text-white flex items-center justify-center font-bold text-xs shrink-0">📎</span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $attachment->getClientOriginalName() }}</p>
                            <input type="text" wire:model="attachmentCaption" placeholder="Add caption..." class="text-xs bg-transparent border-none p-0 text-gray-600 dark:text-gray-300 focus:ring-0">
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="sendMediaAttachment" class="px-3 py-1 rounded-full bg-[#00a884] text-white text-xs font-bold">Send</button>
                        <button type="button" wire:click="$set('attachment', null)" class="text-gray-400 hover:text-red-500 text-xs">✕</button>
                    </div>
                </div>
            @endif

            <!-- 24-Hour Customer Window Expired / Template Required Warning Banner -->
            @if ($selectedConversation && in_array($selectedConversation->channel ?? 'whatsapp', ['whatsapp', 'whatsapp_cloud']) && ($serviceWindow['requires_template'] ?? false))
                <div class="mx-4 mb-1 p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs flex items-center justify-between gap-3 text-amber-900 dark:text-amber-200 shadow-2xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span class="truncate"><strong>24h Window Closed:</strong> Meta policy requires an approved Template to message this contact.</span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" @click="templateModalOpen = true" class="px-2.5 py-1 rounded-full bg-[#00a884] text-white font-bold text-[11px] hover:bg-[#008f72] transition-colors">
                            Select Template
                        </button>
                        @if ($activeInstances->isNotEmpty())
                            <button type="button" wire:click="switchChannelToBaileys" class="px-2.5 py-1 rounded-full bg-blue-600 text-white font-bold text-[11px] hover:bg-blue-700 transition-colors">
                                Switch to QR Session
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 3. WhatsApp Web Bottom Composer Bar (White Pill Capsule + Separate Green Circular Button) -->
            <div wire:key="composer-container-{{ $selectedConversation->id }}" 
                 x-data="{ msgText: '' }"
                 class="px-4 py-3 bg-transparent shrink-0">
                <form wire:submit.prevent="sendMessage; msgText = ''" class="w-full flex items-center gap-2.5">
                    
                    <!-- White Rounded-Full Capsule -->
                    <div class="flex-1 bg-white dark:bg-[#202c33] rounded-full px-4 py-2.5 flex items-center gap-3 shadow-xs border border-black/5 dark:border-white/10">
                        <!-- Attachment Paperclip Button -->
                        <label class="text-[#54656f] dark:text-[#aebac1] hover:text-[#111b21] dark:hover:text-white transition-colors cursor-pointer shrink-0" title="Attach">
                            <input type="file" wire:model="attachment" class="hidden">
                            <svg class="w-5 h-5 -rotate-45" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        </label>

                        <!-- Smiley / Emoji Icon -->
                        <button type="button" class="text-[#54656f] dark:text-[#aebac1] hover:text-[#111b21] dark:hover:text-white transition-colors shrink-0" title="Emojis">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" d="M9 10h.01M15 10h.01M9.5 14.5c.6 1.2 1.4 1.8 2.5 1.8s1.9-.6 2.5-1.8"/>
                            </svg>
                        </button>

                        <!-- Text Input -->
                        <input type="text"
                               x-model="msgText"
                               wire:model="messageBody"
                               @keydown.enter.exact.prevent="$wire.sendMessage(); msgText = ''"
                               placeholder="Type a message"
                               class="flex-1 bg-transparent text-[14px] text-[#111b21] dark:text-[#e9edef] placeholder-[#667781] dark:placeholder-[#8696a0] border-none p-0 focus:ring-0 outline-none">
                    </div>

                    <!-- Green Circular Send Button (When Text Typed) -->
                    <button type="submit"
                            x-show="msgText && msgText.trim().length > 0"
                            wire:loading.attr="disabled"
                            wire:target="sendMessage"
                            class="w-11 h-11 rounded-full bg-[#00a884] hover:bg-[#008f72] text-white flex items-center justify-center shadow-sm shrink-0 transition-transform active:scale-95 cursor-pointer"
                            title="Send Message">
                        <span wire:loading.remove wire:target="sendMessage">
                            <svg class="w-5 h-5 translate-x-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        </span>
                        <span wire:loading wire:target="sendMessage">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        </span>
                    </button>

                    <!-- Green Circular Voice Note Mic Button (When Input Empty) -->
                    <button type="button"
                            x-show="!msgText || msgText.trim().length === 0"
                            wire:click="sendVoiceNote"
                            class="w-11 h-11 rounded-full bg-[#00a884] hover:bg-[#008f72] text-white flex items-center justify-center shrink-0 shadow-sm transition-transform active:scale-95 cursor-pointer"
                            title="Voice Note">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.91-3c-.49 0-.9.36-.98.85C16.52 14.2 14.47 16 12 16s-4.52-1.8-4.93-4.15c-.08-.49-.49-.85-.98-.85-.61 0-1.09.54-1 1.14.49 3 2.89 5.35 5.91 5.78V20c0 .55.45 1 1 1s1-.45 1-1v-2.08c3.02-.43 5.42-2.78 5.91-5.78.1-.6-.39-1.14-1-1.14z"/></svg>
                    </button>
                </form>
            </div>

        @else
            <!-- Empty state when no chat selected (WhatsApp Web Intro Style) -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-[#f0f2f5] dark:bg-[#111b21]">
                <div class="w-20 h-20 rounded-full bg-[#00a884]/10 text-[#00a884] flex items-center justify-center mb-4">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.086.087-.175.18-.075.352.1.173.444.733.953 1.186.656.585 1.21.766 1.383.852.173.086.275.072.376-.044.101-.115.433-.505.548-.678.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                </div>
                <h3 class="font-bold text-xl text-[#111b21] dark:text-white">WhatsCRM Web</h3>
                <p class="text-xs text-[#667781] dark:text-[#8696a0] mt-1 max-w-sm">
                    Send and receive messages across WhatsApp Cloud API and connected sessions with end-to-end sync.
                </p>
                <button type="button" @click="newChatModalOpen = true" class="mt-4 px-4 py-2 rounded-full bg-[#00a884] text-white font-semibold text-xs shadow-sm hover:bg-[#008f72] transition-colors">
                    + Start New Chat
                </button>
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- RIGHT DRAWER: CONTACT INFO & CRM PANEL (Slide-out drawer)      -->
    <!-- ============================================================== -->
    @if ($selectedConversation)
        <div x-show="sidebarDetailsOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             wire:key="sidebar-details-{{ $selectedConversation->id }}"
             class="flex flex-col border-l border-[#d1d7db] dark:border-[#222e35] bg-[#f0f2f5] dark:bg-[#111b21] shrink-0 overflow-y-auto z-30 shadow-2xl"
             style="width: 340px; min-width: 300px; max-width: 360px;">
            
            <!-- Drawer Header: 'Contact Profile' + Close Button -->
            <div class="h-16 px-4 bg-[#f0f2f5] dark:bg-[#202c33] border-b border-[#e9edef] dark:border-[#222e35] flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <x-ph-icon name="user-circle" weight="bold" class="text-xl text-[#008069] dark:text-[#00a884]" />
                    <div>
                        <h3 class="font-bold text-base text-[#111b21] dark:text-[#e9edef]">Contact Profile</h3>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400">CRM Customer & Lead Details</p>
                    </div>
                </div>
                <button type="button" @click="sidebarDetailsOpen = false" class="p-1.5 text-gray-500 hover:bg-black/5 dark:hover:bg-white/5 rounded-full transition-colors cursor-pointer" title="Close Profile">
                    <x-ph-icon name="x" weight="bold" class="text-base" />
                </button>
            </div>

            {{-- Profile Summary Section --}}
            <div class="p-6 bg-white dark:bg-[#111b21] border-b border-[#e9edef] dark:border-[#222e35] text-center space-y-3"
                 x-data="{ avatarEditOpen: false }">

                {{-- Avatar with Camera Overlay --}}
                <div class="relative w-24 h-24 mx-auto group cursor-pointer" @click="avatarEditOpen = !avatarEditOpen" title="Change profile photo">
                    @if ($selectedConversation->avatar_url)
                        <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-emerald-500 shadow-md">
                            <img src="{{ $selectedConversation->avatar_url }}" alt="{{ $selectedConversation->sender_name }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    {{-- Camera icon overlay on hover --}}
                    <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <x-ph-icon name="camera" weight="bold" class="text-white text-2xl" />
                    </div>
                    {{-- Small camera badge --}}
                    <div class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-[#00a884] border-2 border-white dark:border-[#111b21] flex items-center justify-center shadow">
                        <x-ph-icon name="camera" weight="bold" class="text-white text-xs" />
                    </div>
                </div>

                {{-- Avatar Edit Panel (shown on click) --}}
                <div x-show="avatarEditOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     style="display:none;"
                     class="bg-[#f0f2f5] dark:bg-[#202c33] rounded-xl p-3 text-left space-y-2 border border-[#d1d7db] dark:border-[#374047]">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[#008069] dark:text-[#00a884] mb-1">Change Profile Photo</p>

                    {{-- File upload --}}
                    <form wire:submit.prevent="saveContactAvatar" class="space-y-2">
                        <label class="block">
                            <span class="text-[11px] text-gray-500 dark:text-gray-400">Upload from device</span>
                            <div class="mt-1 flex items-center gap-2">
                                <input type="file"
                                       wire:model="contactAvatar"
                                       accept="image/*"
                                       class="block w-full text-[11px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-[#d9fdd3] file:text-[#008069] hover:file:bg-emerald-100 cursor-pointer">
                            </div>
                            <div wire:loading wire:target="contactAvatar" class="text-[10px] text-gray-400 mt-0.5">Uploading...</div>
                        </label>

                        <div class="flex items-center gap-2 text-gray-400 text-[10px]">
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
                            <span>or</span>
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
                        </div>

                        <div>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400">Paste image URL</span>
                            <input type="url"
                                   wire:model="editingContactAvatarUrl"
                                   placeholder="https://example.com/photo.jpg"
                                   class="mt-1 w-full px-2 py-1.5 text-[11px] rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-[#111b21] text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-[#00a884]">
                        </div>

                        <div class="flex gap-2 pt-1">
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="flex-1 py-1.5 rounded-lg bg-[#00a884] text-white text-xs font-bold hover:bg-[#008f72] transition-colors disabled:opacity-60">
                                <span wire:loading.remove wire:target="saveContactAvatar">Save Photo</span>
                                <span wire:loading wire:target="saveContactAvatar">Saving...</span>
                            </button>
                            <button type="button" @click="avatarEditOpen = false"
                                    class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-xs font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <div>
                    <h3 class="font-bold text-lg text-[#111b21] dark:text-white">
                        {{ $selectedConversation->sender_name ?? 'WhatsApp Contact' }}
                    </h3>
                    <p class="text-xs text-[#667781] dark:text-[#8696a0] font-mono mt-0.5">
                        {{ $selectedConversation->sender_mobile }}
                    </p>
                </div>

                <div class="flex justify-center gap-2 pt-1">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $selectedConversation->sender_mobile) }}" 
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#d9fdd3] text-[#008069] text-xs font-bold border border-[#00a884]/30 hover:bg-emerald-100 transition-colors">
                        <x-ph-icon name="whatsapp-logo" weight="fill" class="text-sm" />
                        <span>Open in WhatsApp</span>
                    </a>
                </div>
            </div>


            <!-- Contact Information & Editable Details -->
            <div class="p-4 bg-white dark:bg-[#111b21] border-b border-[#e9edef] dark:border-[#222e35] space-y-3" x-data="{ editing: false }">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#008069] flex items-center gap-1.5">
                        <x-ph-icon name="address-book" weight="bold" class="text-sm" />
                        <span>Contact Info</span>
                    </span>
                    <button type="button" @click="editing = !editing" class="text-xs font-bold text-[#00a884] hover:underline cursor-pointer" x-text="editing ? 'Cancel' : 'Edit'"></button>
                </div>

                <div x-show="!editing" class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800">
                        <span class="text-gray-400">Name:</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $selectedConversation->contact?->name ?? $selectedConversation->sender_name ?? 'Not set' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800">
                        <span class="text-gray-400">Phone:</span>
                        <span class="font-mono text-gray-800 dark:text-gray-200">{{ $selectedConversation->sender_mobile }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800">
                        <span class="text-gray-400">Email:</span>
                        <span class="text-gray-800 dark:text-gray-200">{{ $selectedConversation->contact?->email ?? 'Not set' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800">
                        <span class="text-gray-400">Group:</span>
                        <span class="font-semibold text-primary">{{ $selectedConversation->contact?->phonebook?->name ?? 'Unassigned' }}</span>
                    </div>
                </div>

                <form x-show="editing" wire:submit.prevent="saveContactDetails" class="space-y-2.5 text-xs" style="display: none;">
                    <div>
                        <label class="font-semibold text-gray-700 dark:text-gray-300 text-[11px]">Contact Name</label>
                        <input type="text" wire:model="editingContactName" class="w-full p-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-[#f0f2f5] dark:bg-[#202c33]">
                    </div>
                    <div>
                        <label class="font-semibold text-gray-700 dark:text-gray-300 text-[11px]">Email Address</label>
                        <input type="email" wire:model="editingContactEmail" placeholder="e.g. client@example.com" class="w-full p-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-[#f0f2f5] dark:bg-[#202c33]">
                    </div>
                    <div>
                        <label class="font-semibold text-gray-700 dark:text-gray-300 text-[11px]">Phonebook Group</label>
                        <select wire:model="editingContactPhonebookId" class="w-full p-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-[#f0f2f5] dark:bg-[#202c33]">
                            <option value="">No Group (Unassigned)</option>
                            @foreach ($phonebooks as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" @click="editing = false" class="w-full py-1.5 rounded-lg bg-[#00a884] text-white font-bold text-xs hover:bg-[#008f72] flex items-center justify-center gap-1 cursor-pointer">
                        <x-ph-icon name="floppy-disk" weight="bold" class="text-sm" />
                        <span>Save Contact Details</span>
                    </button>
                </form>
            </div>

            <!-- CRM Pipeline & Agent Stage Section -->
            <div class="p-4 bg-white dark:bg-[#111b21] border-b border-[#e9edef] dark:border-[#222e35] space-y-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#008069] flex items-center gap-1.5">
                    <x-ph-icon name="funnel" weight="bold" class="text-sm" />
                    <span>CRM Pipeline & Team</span>
                </span>
                
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Pipeline Stage</label>
                    <select wire:change="updateKanbanStage($event.target.value)" class="w-full text-xs p-2 rounded-lg bg-[#f0f2f5] dark:bg-[#202c33] border-none">
                        <option value="lead" @selected($selectedConversation->kanban_stage === 'lead')>🟢 Lead (New Inbound)</option>
                        <option value="contacted" @selected($selectedConversation->kanban_stage === 'contacted')>🔵 Contacted</option>
                        <option value="qualified" @selected($selectedConversation->kanban_stage === 'qualified')>🟣 Qualified</option>
                        <option value="negotiation" @selected($selectedConversation->kanban_stage === 'negotiation')>🟡 Proposal / Negotiation</option>
                        <option value="won" @selected($selectedConversation->kanban_stage === 'won')>🏆 Won / Closed Deal</option>
                        <option value="lost" @selected($selectedConversation->kanban_stage === 'lost')>🔴 Lost / Closed</option>
                    </select>

                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Assigned Agent</label>
                    <select wire:change="assignAgent($event.target.value)" class="w-full text-xs p-2 rounded-lg bg-[#f0f2f5] dark:bg-[#202c33] border-none">
                        <option value="">👤 Unassigned</option>
                        @foreach ($teamMembers as $member)
                            <option value="{{ $member->id }}" @selected($selectedConversation->assigned_member_id == $member->id)>
                                {{ $member->user?->name ?? 'Agent' }} ({{ ucfirst($member->role ?? 'Agent') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Labels & Tags Section -->
            <div class="p-4 bg-white dark:bg-[#111b21] border-b border-[#e9edef] dark:border-[#222e35] space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#008069] flex items-center gap-1.5">
                        <x-ph-icon name="tag" weight="bold" class="text-sm" />
                        <span>Labels & Tags</span>
                    </span>
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open" class="text-xs text-[#00a884] font-bold hover:underline cursor-pointer">+ Add</button>
                        <div x-show="open" class="absolute right-0 mt-1 w-44 rounded-xl bg-white dark:bg-[#202c33] p-1.5 shadow-xl border border-gray-100 dark:border-gray-700 z-50">
                            @foreach ($availableTags as $tag)
                                <button type="button" wire:click="attachTag({{ $tag->id }})" @click="open = false" class="w-full text-left px-2.5 py-1.5 text-xs rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $tag->hex_color }}"></span>
                                    <span>{{ $tag->title }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    @forelse ($selectedConversation->tags as $tag)
                        <span class="text-xs font-bold py-0.5 px-2 rounded-md inline-flex items-center gap-1.5"
                              style="background-color: {{ $tag->hex_color }}20; color: {{ $tag->hex_color }};">
                            <span>{{ $tag->title }}</span>
                            <button wire:click="detachTag({{ $tag->id }})" class="hover:opacity-75">✕</button>
                        </span>
                    @empty
                        <span class="text-xs text-gray-400 italic">No labels attached</span>
                    @endforelse
                </div>
            </div>

            <!-- Internal Notes Collaboration Section -->
            <div class="p-4 bg-white dark:bg-[#111b21] space-y-3 flex-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#008069] flex items-center gap-1.5">
                    <x-ph-icon name="notepad" weight="bold" class="text-sm" />
                    <span>Internal Agent Notes</span>
                </span>
                
                <form wire:submit.prevent="addNote" class="space-y-2">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Lead Rating:</span>
                        <div class="flex items-center gap-1">
                            @foreach ([1, 2, 3, 4, 5] as $i)
                                <button type="button" wire:click="$set('internalNoteRating', {{ $i }})" class="text-base {{ $internalNoteRating >= $i ? 'text-amber-400' : 'text-gray-300' }}">★</button>
                            @endforeach
                        </div>
                    </div>

                    <textarea wire:model="internalNoteBody" placeholder="Write a private note..." rows="2" class="w-full text-xs p-2 rounded-lg bg-[#f0f2f5] dark:bg-[#202c33] border-none resize-none"></textarea>
                    
                    <button type="submit" class="w-full py-1.5 rounded-full bg-[#00a884] text-white text-xs font-bold cursor-pointer">Add Note</button>
                </form>

                <div class="space-y-2 pt-1">
                    @forelse ($conversationNotes as $note)
                        <div wire:key="conv-note-{{ $note->id }}" class="p-2.5 rounded-lg bg-[#f0f2f5] dark:bg-[#202c33] text-xs space-y-1">
                            <div class="flex items-center justify-between text-[10px] text-gray-400">
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ $note->user->name ?? 'Agent' }}</span>
                                <div class="flex items-center gap-1">
                                    @if ($note->rating)
                                        <span class="text-amber-500 font-bold">{{ str_repeat('★', $note->rating) }}</span>
                                    @endif
                                    <span>{{ $note->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $note->note }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-2 italic">No notes yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    </div> <!-- Close Main WhatsApp Card Container -->

    <!-- ============================================================== -->
    <!-- NEW CONVERSATION MODAL                                         -->
    <!-- ============================================================== -->
    <div x-show="newChatModalOpen"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-xs"
         style="display: none;">
        
        <div @click.outside="newChatModalOpen = false"
             class="w-full max-w-lg rounded-2xl bg-white dark:bg-[#202c33] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="px-6 py-4 bg-[#00a884] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base">New Chat</h3>
                    <p class="text-xs text-white/80">Start conversation with a phone number.</p>
                </div>
                <button type="button" @click="newChatModalOpen = false" class="text-white/80 hover:text-white">✕</button>
            </div>

            <form wire:submit.prevent="startNewConversation" class="p-6 space-y-4 overflow-y-auto">
                <!-- Channel Selector -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Dispatch Channel</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-2 {{ $newChatChannel === 'whatsapp_cloud' ? 'border-[#00a884] bg-emerald-50/50 text-[#008069] font-bold' : 'border-gray-200 dark:border-gray-700' }}">
                            <input type="radio" wire:model.live="newChatChannel" value="whatsapp_cloud" class="hidden">
                            <span>📱 Meta Cloud API</span>
                        </label>
                        <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-2 {{ $newChatChannel === 'baileys' ? 'border-blue-500 bg-blue-50/50 text-blue-600 font-bold' : 'border-gray-200 dark:border-gray-700' }}">
                            <input type="radio" wire:model.live="newChatChannel" value="baileys" class="hidden">
                            <span>🔄 Baileys QR Session</span>
                        </label>
                    </div>
                </div>

                <!-- Recipient Mobile -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Phone Number (with country code)</label>
                    <input type="text" wire:model="newChatMobile" placeholder="e.g. 8801700000000" class="w-full p-2.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-[#f0f2f5] dark:bg-[#111b21]">
                    @error('newChatMobile') <span class="text-[11px] text-red-500 font-semibold">{{ $message }}</span> @enderror
                </div>

                <!-- Recipient Name -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Contact Name (Optional)</label>
                    <input type="text" wire:model.live="newChatName" placeholder="e.g. John Doe" class="w-full p-2.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-[#f0f2f5] dark:bg-[#111b21]">
                </div>

                @if ($newChatChannel === 'baileys')
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Message</label>
                        <textarea wire:model="newChatMessage" placeholder="Type message..." rows="3" class="w-full p-2.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-[#f0f2f5] dark:bg-[#111b21] resize-none"></textarea>
                    </div>
                @else
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Pre-approved Template</label>
                        <select wire:model.live="newChatTemplateName" class="w-full p-2.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-[#f0f2f5] dark:bg-[#111b21]">
                            <option value="">Select template...</option>
                            @foreach ($templates as $tmpl)
                                <option value="{{ $tmpl['name'] ?? $tmpl['id'] }}">{{ $tmpl['name'] ?? $tmpl['title'] }} ({{ $tmpl['language'] ?? 'en' }})</option>
                            @endforeach
                        </select>
                    </div>

                    @if ($this->newChatSelectedTemplate)
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#111b21] border border-gray-200 dark:border-gray-700 space-y-2">
                            <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Template Preview</div>
                            <p class="text-xs text-gray-700 dark:text-gray-300 font-mono bg-white dark:bg-[#202c33] p-2.5 rounded-lg border border-gray-100 dark:border-gray-800">
                                {{ $this->newChatSelectedTemplate['body'] ?? '' }}
                            </p>
                            @if (!empty($this->newChatSelectedTemplate['variables']))
                                <div class="space-y-2 pt-1">
                                    <span class="text-xs font-semibold text-[#008069] dark:text-[#00a884]">Variables (auto-fills from Contact Name if empty):</span>
                                    @foreach ($this->newChatSelectedTemplate['variables'] as $varKey)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-mono bg-gray-200 dark:bg-gray-800 px-1.5 py-0.5 rounded text-gray-700 dark:text-gray-300 shrink-0">@{{ {{ $varKey }} }}</span>
                                            <input type="text" 
                                                   wire:model="newChatTemplateVariables.{{ $varKey }}" 
                                                   placeholder="e.g. {{ $newChatName ?: 'Customer Name' }}" 
                                                   class="flex-1 p-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#202c33] text-gray-900 dark:text-white">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @endif

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="newChatModalOpen = false" class="px-4 py-2 rounded-full text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-full bg-[#00a884] text-white text-xs font-bold hover:bg-[#008f72]">Start Chat</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- TEMPLATES SELECTOR MODAL                                       -->
    <!-- ============================================================== -->
    <div x-show="templateModalOpen"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-xs"
         style="display: none;">
        
        <div @click.outside="templateModalOpen = false"
             class="w-full max-w-xl rounded-2xl bg-white dark:bg-[#202c33] shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
            
            <div class="px-6 py-4 bg-[#00a884] text-white flex items-center justify-between">
                <h3 class="font-bold text-base">Meta WhatsApp Templates</h3>
                <button type="button" @click="templateModalOpen = false" class="text-white/80 hover:text-white">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-3 divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($templates as $tmpl)
                    <div class="pt-3 first:pt-0 flex items-start justify-between gap-4">
                        <div class="space-y-1 flex-1">
                            <span class="font-bold text-sm text-[#111b21] dark:text-white">{{ $tmpl['name'] ?? $tmpl['title'] }}</span>
                            <p class="text-xs text-gray-600 dark:text-gray-300 bg-[#f0f2f5] dark:bg-[#111b21] p-3 rounded-lg font-mono">{{ $tmpl['body'] }}</p>
                        </div>
                        <button type="button" wire:click="sendTemplateMessage('{{ $tmpl['name'] ?? $tmpl['id'] }}')" @click="templateModalOpen = false" class="mt-1 px-3 py-1.5 rounded-full bg-[#00a884] text-white text-xs font-bold hover:bg-[#008f72] shrink-0">
                            Send
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
