<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Contacts & Phonebook Directory</h1>
                <x-tag color="primary" class="font-bold">{{ number_format($totalContacts) }} Contacts</x-tag>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Manage customer phonebook groups, custom broadcast variables, and opt-out registry.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <x-button wire:click="exportCsv" variant="default" size="sm" title="Export all contacts to CSV">
                <svg class="w-4 h-4 mr-1 text-gray-500 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export CSV</span>
            </x-button>

            <x-button wire:click="$set('showImportModal', true)" variant="default" size="sm">
                <svg class="w-4 h-4 mr-1 text-gray-500 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import CSV</span>
            </x-button>

            <x-button wire:click="openCreateContactModal" variant="solid" size="sm">
                <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Contact</span>
            </x-button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <x-tabs>
        <x-tab-item wire:click="setTab('contacts')" :active="$activeTab === 'contacts'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>All Contacts ({{ number_format($totalContacts) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('groups')" :active="$activeTab === 'groups'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            <span>Contact Groups ({{ $phonebooks->count() }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('fields')" :active="$activeTab === 'fields'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            <span>Custom Fields ({{ count($customFields) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('blacklist')" :active="$activeTab === 'blacklist'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            <span>Blacklist & Opt-Out ({{ count($blacklist) }})</span>
        </x-tab-item>
    </x-tabs>

    <!-- ============================================================== -->
    <!-- TAB 1: ALL CONTACTS DATA TABLE                                 -->
    <!-- ============================================================== -->
    @if ($activeTab === 'contacts')
        <x-card gutterless class="overflow-hidden">
            <!-- Table Toolbar (Search, Group Dropdown, Bulk Actions) -->
            <div class="p-4 sm:px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200/70 dark:border-gray-800 bg-white dark:bg-gray-800">
                <!-- Left Controls: Search Bar & Group Filter -->
                <div class="flex flex-1 items-center gap-3 flex-wrap">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 z-10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" 
                               type="text" 
                               placeholder="Search name, phone, or email..." 
                               style="padding-left: 2.35rem; padding-right: 2rem;"
                               class="input input-sm w-full">
                        @if ($search)
                            <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 z-10" title="Clear search">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        @endif
                    </div>

                    <!-- Phonebook Group Filter Custom Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" 
                                @click="open = !open" 
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/80 transition-all shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            <span>
                                @if($phonebookFilter && ($currentPb = $phonebooks->firstWhere('id', $phonebookFilter)))
                                    {{ $currentPb->name }}
                                @else
                                    All Phonebooks ({{ $phonebooks->count() }})
                                @endif
                            </span>
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-150 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-1.5 w-56 rounded-2xl bg-white dark:bg-gray-900 p-1.5 shadow-2xl border border-gray-100 dark:border-gray-800 z-30 focus:outline-none"
                             style="display: none;">
                            <button type="button" 
                                    wire:click="$set('phonebookFilter', '')" 
                                    @click="open = false" 
                                    class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center justify-between transition-colors {{ empty($phonebookFilter) ? 'text-primary bg-primary-subtle font-bold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <div class="flex items-center gap-2 truncate">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    <span>All Phonebooks</span>
                                </div>
                                @if(empty($phonebookFilter))
                                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </button>

                            @foreach ($phonebooks as $pb)
                                <button type="button" 
                                        wire:click="$set('phonebookFilter', '{{ $pb->id }}')" 
                                        @click="open = false" 
                                        class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center justify-between transition-colors {{ $phonebookFilter == $pb->id ? 'text-primary bg-primary-subtle font-bold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                    <div class="flex items-center gap-2 truncate">
                                        <svg class="w-4 h-4 text-primary/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                        <span class="truncate">{{ $pb->name }}</span>
                                    </div>
                                    @if($phonebookFilter == $pb->id)
                                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Reset Active Filters -->
                    @if ($search || $phonebookFilter)
                        <button wire:click="resetFilters" class="text-xs text-rose-500 hover:text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1.5 py-1.5 px-2.5 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Reset filters</span>
                        </button>
                    @endif
                </div>

                <!-- Right: Results Count -->
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                    <span>Showing <strong class="text-gray-900 dark:text-white font-semibold">{{ $contacts->count() }}</strong> of <strong class="text-gray-900 dark:text-white font-semibold">{{ $contacts->total() }}</strong> contacts</span>
                </div>
            </div>

            <!-- Bulk Action Bar -->
            @if (count($selectedContacts) > 0)
                <div class="px-6 py-2.5 bg-primary-subtle dark:bg-primary/20 border-b border-primary/20 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2 font-bold text-primary dark:text-primary-light">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span>{{ count($selectedContacts) }} contacts selected</span>
                    </div>

                    <div class="flex items-center gap-2.5 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <x-select wire:model="bulkPhonebookId" placeholder="Assign to Group..." class="!py-1 !text-xs">
                                @foreach ($phonebooks as $pb)
                                    <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                                @endforeach
                            </x-select>
                            <x-button wire:click="bulkAssignGroup" variant="solid" size="xs">
                                Move
                            </x-button>
                        </div>

                        <x-button wire:click="bulkDelete" wire:confirm="Are you sure you want to delete all selected contacts?" variant="plain" size="xs" class="text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                            Delete Selected
                        </x-button>
                    </div>
                </div>
            @endif

            <!-- Main Contacts Table -->
            <x-table hoverable>
                <thead>
                    <tr>
                        <th class="w-10">
                            <x-checkbox wire:model.live="selectAll" />
                        </th>
                        <th>Name</th>
                        <th>WhatsApp Phone</th>
                        <th>Email</th>
                        <th>Phonebook Group</th>
                        <th>Source</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contacts as $contact)
                        <tr>
                            <!-- Row Select Checkbox -->
                            <td>
                                <x-checkbox wire:model.live="selectedContacts" value="{{ $contact->id }}" />
                            </td>

                            <!-- Name & Initials Avatar -->
                            <td class="flex items-center gap-3">
                                <x-avatar :name="$contact->name ?? $contact->mobile" size="sm" />
                                <div class="min-w-0">
                                    <span class="font-semibold text-gray-900 dark:text-white truncate block">
                                        {{ $contact->name ?? 'Unnamed Contact' }}
                                    </span>
                                    @if (!empty($contact->custom_fields['company_name']))
                                        <span class="text-[10px] text-gray-400 truncate block">{{ $contact->custom_fields['company_name'] }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Phone -->
                            <td class="font-mono text-xs text-gray-800 dark:text-gray-200">
                                {{ $contact->mobile }}
                            </td>

                            <!-- Email -->
                            <td class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[150px]">
                                {{ $contact->email ?? '—' }}
                            </td>

                            <!-- Phonebook Group -->
                            <td class="text-xs">
                                @if ($contact->phonebook)
                                    <x-tag color="primary">
                                        {{ $contact->phonebook->name }}
                                    </x-tag>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            <!-- Source -->
                            <td class="text-xs">
                                <x-tag color="gray" class="capitalize">
                                    {{ $contact->source ?? 'manual' }}
                                </x-tag>
                            </td>

                            <!-- Action Buttons -->
                            <td class="text-right space-x-1 shrink-0">
                                <a href="{{ route('inbox') }}" class="p-1.5 text-primary hover:bg-primary/10 rounded-lg inline-block transition-colors" title="Chat in Inbox">
                                    <x-nav-icon name="inbox" class="w-4 h-4" />
                                </a>
                                <button wire:click="editContact({{ $contact->id }})" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg inline-block transition-colors" title="Edit Contact">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button wire:click="deleteContact({{ $contact->id }})" wire:confirm="Are you sure you want to delete this contact?" class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-lg inline-block transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400 text-xs">
                                No contacts found in this workspace. Add contacts or import a CSV file above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>

            <!-- Pagination Footer -->
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $contacts->links() }}
            </div>
        </x-card>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 2: CONTACT GROUPS (PHONEBOOKS)                             -->
    <!-- ============================================================== -->
    @if ($activeTab === 'groups')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Phonebook Groups</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Segment your audience for targeted broadcast campaigns and workflow automations.</p>
                </div>
                <x-button wire:click="openCreatePhonebookModal" variant="solid" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>New Group</span>
                </x-button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($phonebooks as $pb)
                    <x-card bodyClass="p-6 flex flex-col justify-between h-full space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-primary-subtle dark:bg-primary/20 text-primary flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                </div>
                                <x-tag color="primary" class="font-bold text-xs">{{ $pb->contacts_count }} Contacts</x-tag>
                            </div>

                            <div>
                                <h4 class="text-base font-bold text-gray-900 dark:text-white truncate">{{ $pb->name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Created {{ $pb->created_at ? $pb->created_at->format('M d, Y') : 'Recently' }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-2">
                            <button wire:click="$set('phonebookFilter', '{{ $pb->id }}'); setTab('contacts');" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                                <span>View Contacts</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div class="flex items-center gap-1">
                                <button wire:click="editPhonebook({{ $pb->id }})" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button wire:click="deletePhonebook({{ $pb->id }})" wire:confirm="Delete this phonebook group? Contacts will become unassigned." class="p-1.5 text-rose-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </x-card>
                @empty
                    <div class="col-span-full">
                        <x-card bodyClass="p-12 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white">No Phonebook Groups Yet</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Create targeted audience groups to organize your customer phone list.</p>
                            <x-button wire:click="openCreatePhonebookModal" variant="solid" size="sm">
                                + Create First Group
                            </x-button>
                        </x-card>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 3: CUSTOM FIELDS MANAGER                                   -->
    <!-- ============================================================== -->
    @if ($activeTab === 'fields')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Custom Broadcast Variables</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Define dynamic custom contact parameters used for WhatsApp template variables (e.g. &#123;&#123;company_name&#125;&#125;, &#123;&#123;order_id&#125;&#125;).
                    </p>
                </div>
                <x-button wire:click="$set('showFieldModal', true)" variant="solid" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>New Custom Field</span>
                </x-button>
            </div>

            <x-card gutterless class="overflow-hidden">
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Field Label</th>
                            <th>Variable Key</th>
                            <th>Data Type</th>
                            <th>Template Tag Syntax</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customFields as $field)
                            <tr>
                                <td class="font-bold text-gray-900 dark:text-white text-xs">
                                    {{ $field['label'] }}
                                </td>
                                <td>
                                    <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                        {{ $field['key'] }}
                                    </span>
                                </td>
                                <td>
                                    <x-tag color="blue" class="text-[10px] uppercase font-bold">{{ $field['type'] }}</x-tag>
                                </td>
                                <td>
                                    <span class="font-mono text-xs text-primary font-bold">
                                        &#123;&#123;{{ $field['key'] }}&#125;&#125;
                                    </span>
                                </td>
                                <td class="text-right">
                                    <button wire:click="deleteCustomField('{{ $field['key'] }}')" wire:confirm="Delete this custom field variable?" class="p-1.5 text-rose-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 text-xs">
                                    No custom fields created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 4: BLACKLIST / OPT-OUT REGISTRY                            -->
    <!-- ============================================================== -->
    @if ($activeTab === 'blacklist')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Blacklist & Opt-Out Registry</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Contacts who have sent STOP or unsubscribed from outbound WhatsApp campaigns are excluded automatically.
                    </p>
                </div>
                <x-button wire:click="$set('showBlacklistModal', true)" variant="solid" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add to Blacklist</span>
                </x-button>
            </div>

            <x-card gutterless class="overflow-hidden">
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th>Phone Number</th>
                            <th>Opt-Out Reason</th>
                            <th>Registered Date</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($blacklist as $item)
                            <tr>
                                <td class="font-mono font-bold text-xs text-gray-900 dark:text-white">
                                    {{ $item['number'] }}
                                </td>
                                <td class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $item['reason'] }}
                                </td>
                                <td class="text-xs text-gray-400 font-mono">
                                    {{ $item['added_at'] ?? 'N/A' }}
                                </td>
                                <td>
                                    <x-tag color="rose" class="text-[10px] font-bold">Blocked</x-tag>
                                </td>
                                <td class="text-right">
                                    <button wire:click="removeFromBlacklist('{{ $item['number'] }}')" class="text-xs font-semibold text-primary hover:underline">
                                        Unblock Number
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 text-xs">
                                    Blacklist is currently empty.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: ADD / EDIT CONTACT                                      -->
    <!-- ============================================================== -->
    <x-modal name="contact-modal" :show="$showContactModal" focusable>
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-base text-gray-900 dark:text-white">
                    {{ $editingContactId ? 'Edit Contact Details' : 'Create New Customer Contact' }}
                </h3>
                <button wire:click="$set('showContactModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveContact" class="space-y-4">
                <x-form-item label="WhatsApp Mobile Number" :required="true" :error="$errors->first('mobile')">
                    <x-input wire:model="mobile" placeholder="+15551234567" :invalid="$errors->has('mobile')" />
                </x-form-item>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-item label="Full Name" :error="$errors->first('name')">
                        <x-input wire:model="name" placeholder="e.g. Jane Doe" :invalid="$errors->has('name')" />
                    </x-form-item>

                    <x-form-item label="Email Address" :error="$errors->first('email')">
                        <x-input wire:model="email" type="email" placeholder="jane@example.com" :invalid="$errors->has('email')" />
                    </x-form-item>
                </div>

                <x-form-item label="Phonebook Group">
                    <x-select wire:model="phonebook_id" placeholder="No Group">
                        <option value="">Unassigned</option>
                        @foreach ($phonebooks as $pb)
                            <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                        @endforeach
                    </x-select>
                </x-form-item>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="$set('showContactModal', false)" variant="default" size="sm">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="solid" size="sm">
                        {{ $editingContactId ? 'Update Contact' : 'Save Contact' }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- ============================================================== -->
    <!-- MODAL: CREATE PHONEBOOK GROUP                                  -->
    <!-- ============================================================== -->
    <x-modal name="phonebook-modal" :show="$showPhonebookModal" focusable>
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-base text-gray-900 dark:text-white">
                    {{ $editingPhonebookId ? 'Edit Group' : 'Create Phonebook Group' }}
                </h3>
                <button wire:click="$set('showPhonebookModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="createPhonebook" class="space-y-4">
                <x-form-item label="Group Name" :required="true" :error="$errors->first('newPhonebookName')">
                    <x-input wire:model="newPhonebookName" placeholder="e.g. VIP Retail Clients, Inactive Leads" :invalid="$errors->has('newPhonebookName')" />
                </x-form-item>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="$set('showPhonebookModal', false)" variant="default" size="sm">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="solid" size="sm">
                        {{ $editingPhonebookId ? 'Save Changes' : 'Create Group' }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- ============================================================== -->
    <!-- MODAL: CSV IMPORT WIZARD                                       -->
    <!-- ============================================================== -->
    <x-modal name="import-modal" :show="$showImportModal" focusable>
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-base text-gray-900 dark:text-white">Import Contacts from CSV</h3>
                <button wire:click="$set('showImportModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="importCsv" class="space-y-4">
                <x-form-item label="Target Phonebook Group (Optional)">
                    <x-select wire:model="importPhonebookId" placeholder="Unassigned">
                        <option value="">No Group (Unassigned)</option>
                        @foreach ($phonebooks as $pb)
                            <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                        @endforeach
                    </x-select>
                </x-form-item>

                <x-form-item :error="$errors->first('csvFile')">
                    <x-upload 
                        wire:model="csvFile" 
                        accept=".csv,text/csv" 
                        title="Drop CSV file here or click to browse" 
                        description="CSV must contain 'name' and 'mobile' (or 'phone') header columns"
                    />
                </x-form-item>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="$set('showImportModal', false)" variant="default" size="sm">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="solid" size="sm" wire:loading.attr="disabled">
                        <span wire:loading.remove>Start Import</span>
                        <span wire:loading>Importing...</span>
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- ============================================================== -->
    <!-- MODAL: ADD CUSTOM FIELD                                        -->
    <!-- ============================================================== -->
    <x-modal name="custom-field-modal" :show="$showFieldModal" focusable>
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-base text-gray-900 dark:text-white">Create Custom Variable Field</h3>
                <button wire:click="$set('showFieldModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="addCustomField" class="space-y-4">
                <x-form-item label="Field Label" :required="true" :error="$errors->first('newFieldLabel')">
                    <x-input wire:model="newFieldLabel" placeholder="e.g. Order Tracking ID" :invalid="$errors->has('newFieldLabel')" />
                </x-form-item>

                <x-form-item label="Variable Key (Alphanumeric and underscores only)" :required="true" :error="$errors->first('newFieldKey')">
                    <x-input wire:model="newFieldKey" placeholder="e.g. order_id" class="font-mono text-xs" :invalid="$errors->has('newFieldKey')" />
                </x-form-item>

                <x-form-item label="Data Type" :required="true">
                    <x-select wire:model="newFieldType">
                        <option value="text">Text / String</option>
                        <option value="number">Number / Amount</option>
                        <option value="date">Date</option>
                        <option value="select">Dropdown Choice</option>
                    </x-select>
                </x-form-item>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="$set('showFieldModal', false)" variant="default" size="sm">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="solid" size="sm">
                        Add Field
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- ============================================================== -->
    <!-- MODAL: ADD NUMBER TO BLACKLIST                                 -->
    <!-- ============================================================== -->
    <x-modal name="blacklist-modal" :show="$showBlacklistModal" focusable>
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-base text-gray-900 dark:text-white">Add Number to Campaign Blacklist</h3>
                <button wire:click="$set('showBlacklistModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="addToBlacklist" class="space-y-4">
                <x-form-item label="Phone Number" :required="true" :error="$errors->first('newBlacklistNumber')">
                    <x-input wire:model="newBlacklistNumber" placeholder="+15551234567" class="font-mono text-xs" :invalid="$errors->has('newBlacklistNumber')" />
                </x-form-item>

                <x-form-item label="Opt-Out Reason" :required="true" :error="$errors->first('newBlacklistReason')">
                    <x-input wire:model="newBlacklistReason" placeholder="e.g. Customer sent STOP on broadcast" :invalid="$errors->has('newBlacklistReason')" />
                </x-form-item>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="$set('showBlacklistModal', false)" variant="default" size="sm">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="solid" size="sm">
                        Add to Blacklist
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
