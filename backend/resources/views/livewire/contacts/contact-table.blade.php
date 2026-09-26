<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Contacts & Phonebook Directory</h1>
                <x-tag color="primary" class="font-bold">{{ number_format($totalContacts) }} Contacts</x-tag>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Manage audience phonebooks, broadcast template variables, and opt-out registry.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Sync from WhatsApp Session Button -->
            <x-button wire:click="openDeviceSyncModal" variant="default" size="md" title="Sync contacts from connected WhatsApp Session">
                <x-ph-icon name="arrows-clockwise" weight="bold" class="text-base mr-1.5 text-emerald-600 dark:text-emerald-400" />
                <span>Sync Device</span>
            </x-button>

            <!-- Export CSV Button -->
            <x-button wire:click="exportCsv" variant="default" size="md" title="Export contacts to CSV">
                <x-ph-icon name="download-simple" weight="bold" class="text-base mr-1.5 text-gray-500 dark:text-gray-400" />
                <span>Export CSV</span>
            </x-button>

            <!-- Import CSV Button -->
            <x-button wire:click="openImportModal" x-on:click="$dispatch('open-modal', 'import-modal')" variant="default" size="md">
                <x-ph-icon name="upload-simple" weight="bold" class="text-base mr-1.5 text-gray-500 dark:text-gray-400" />
                <span>Import CSV</span>
            </x-button>

            <!-- Add Contact Button -->
            <x-button wire:click="openCreateContactModal" variant="solid" size="md">
                <x-ph-icon name="plus" weight="bold" class="text-base mr-1.5" />
                <span>Add Contact</span>
            </x-button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between gap-2 shadow-2xs">
            <div class="flex items-center gap-2">
                <x-ph-icon name="check-circle" weight="fill" class="text-base text-emerald-500 shrink-0" />
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 p-1 rounded-lg hover:bg-emerald-100/50 dark:hover:bg-emerald-900/50 transition-colors">
                <x-ph-icon name="x" weight="bold" class="text-xs" />
            </button>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <x-tabs>
        <x-tab-item wire:click="setTab('contacts')" :active="$activeTab === 'contacts'">
            <x-ph-icon name="users" weight="bold" class="text-base mr-1.5 shrink-0" />
            <span>All Contacts ({{ number_format($totalContacts) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('groups')" :active="$activeTab === 'groups'">
            <x-ph-icon name="folder" weight="bold" class="text-base mr-1.5 shrink-0" />
            <span>Contact Groups ({{ $phonebooks->count() }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('fields')" :active="$activeTab === 'fields'">
            <x-ph-icon name="textbox" weight="bold" class="text-base mr-1.5 shrink-0" />
            <span>Custom Fields ({{ count($customFields) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('blacklist')" :active="$activeTab === 'blacklist'">
            <x-ph-icon name="prohibit" weight="bold" class="text-base mr-1.5 shrink-0" />
            <span>Blacklist & Opt-Out ({{ count($blacklist) }})</span>
        </x-tab-item>
    </x-tabs>

    <!-- ============================================================== -->
    <!-- TAB 1: ALL CONTACTS DATA TABLE                                 -->
    <!-- ============================================================== -->
    @if ($activeTab === 'contacts')
        <x-card gutterless class="overflow-hidden">
            <!-- Table Toolbar (Search, Group Dropdown, Per-Page, Bulk Actions) -->
            <div class="p-4 sm:px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200/70 dark:border-gray-800 bg-white dark:bg-gray-800">
                <!-- Left Controls: Search Bar & Group Filter -->
                <div class="flex flex-1 items-center gap-3 flex-wrap">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 z-10">
                            <x-ph-icon name="magnifying-glass" weight="bold" class="text-sm" />
                        </div>
                        <input wire:model.live.debounce.300ms="search" 
                               type="text" 
                               placeholder="Search name, phone, or variables..." 
                               style="padding-left: 2.35rem; padding-right: 2rem;"
                               class="input input-sm w-full">
                        @if ($search)
                            <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 z-10" title="Clear search">
                                <x-ph-icon name="x" weight="bold" class="text-xs" />
                            </button>
                        @endif
                    </div>

                    <!-- Phonebook Group Filter Custom Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" 
                                @click="open = !open" 
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/80 transition-all shadow-2xs">
                            <x-ph-icon name="folder" weight="duotone" class="text-sm text-gray-400 shrink-0" />
                            <span>
                                @if($phonebookFilter && ($currentPb = $phonebooks->firstWhere('id', $phonebookFilter)))
                                    {{ $currentPb->name }}
                                @else
                                    All Groups ({{ $phonebooks->count() }})
                                @endif
                            </span>
                            <x-ph-icon name="caret-down" weight="bold" class="text-xs text-gray-400 transition-transform duration-150 shrink-0" ::class="open ? 'rotate-180' : ''" />
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
                                    <x-ph-icon name="folder" weight="duotone" class="text-sm text-gray-400" />
                                    <span>All Groups</span>
                                </div>
                                @if(empty($phonebookFilter))
                                    <x-ph-icon name="check" weight="bold" class="text-xs text-primary shrink-0" />
                                @endif
                            </button>

                            @foreach ($phonebooks as $pb)
                                <button type="button" 
                                        wire:click="$set('phonebookFilter', '{{ $pb->id }}')" 
                                        @click="open = false" 
                                        class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center justify-between transition-colors {{ $phonebookFilter == $pb->id ? 'text-primary bg-primary-subtle font-bold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                    <div class="flex items-center gap-2 truncate">
                                        <x-ph-icon name="folder" weight="duotone" class="text-sm text-primary/70 shrink-0" />
                                        <span class="truncate">{{ $pb->name }}</span>
                                    </div>
                                    @if($phonebookFilter == $pb->id)
                                        <x-ph-icon name="check" weight="bold" class="text-xs text-primary shrink-0" />
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Per Page Limit Custom Dropdown (Fixed & Upgraded) -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" 
                                @click="open = !open" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/80 transition-all shadow-2xs cursor-pointer">
                            <span>{{ $perPage }} / page</span>
                            <x-ph-icon name="caret-down" weight="bold" class="text-xs text-gray-400 transition-transform duration-150 shrink-0" ::class="open ? 'rotate-180' : ''" />
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-1.5 w-36 rounded-2xl bg-white dark:bg-gray-900 p-1.5 shadow-2xl border border-gray-100 dark:border-gray-800 z-30 focus:outline-none"
                             style="display: none;">
                            @foreach ([15, 25, 50, 100] as $size)
                                <button type="button" 
                                        wire:click="$set('perPage', {{ $size }})" 
                                        @click="open = false" 
                                        class="w-full text-left px-3 py-2 text-xs font-semibold rounded-xl flex items-center justify-between transition-colors {{ $perPage == $size ? 'text-primary bg-primary-subtle font-bold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                    <span>{{ $size }} / page</span>
                                    @if ($perPage == $size)
                                        <x-ph-icon name="check" weight="bold" class="text-xs text-primary shrink-0" />
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Reset Active Filters -->
                    @if ($search || $phonebookFilter)
                        <button wire:click="resetFilters" class="text-xs text-rose-500 hover:text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1.5 py-1.5 px-2.5 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-all cursor-pointer">
                            <x-ph-icon name="x-circle" weight="bold" class="text-sm" />
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

                    <div class="flex items-center gap-2">
                        <!-- Bulk Assign Group Selector -->
                        <div class="flex items-center gap-1.5">
                            <select wire:model="bulkPhonebookId" class="select select-sm text-xs py-1 rounded-xl">
                                <option value="">Assign to Group...</option>
                                @foreach ($phonebooks as $pb)
                                    <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                                @endforeach
                            </select>
                            <x-button wire:click="bulkAssignGroup" variant="default" size="xs" :disabled="!$bulkPhonebookId">
                                Apply
                            </x-button>
                        </div>

                        <!-- Bulk Delete Button -->
                        <x-button wire:click="bulkDelete" wire:confirm="Are you sure you want to delete the selected contacts?" variant="danger" size="xs">
                            <x-ph-icon name="trash" weight="bold" class="text-sm mr-1" />
                            <span>Delete Selected</span>
                        </x-button>
                    </div>
                </div>
            @endif

            <!-- Main Contacts Data Table -->
            <x-table hoverable>
                <thead>
                    <tr>
                        <th class="w-10 px-4 py-3.5 text-center">
                            <x-checkbox wire:model.live="selectAll" />
                        </th>
                        <th class="min-w-[180px] whitespace-nowrap">Contact</th>
                        <th class="min-w-[140px] whitespace-nowrap">WhatsApp Phone</th>
                        <th class="min-w-[110px] whitespace-nowrap">Group</th>
                        <th class="min-w-[220px] whitespace-nowrap">Broadcast Variables (var1–var5)</th>
                        <th class="min-w-[90px] whitespace-nowrap">Source</th>
                        <th class="min-w-[110px] text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contacts as $contact)
                        <tr wire:key="contact-row-{{ $contact->id }}">
                            <!-- Row Select Checkbox -->
                            <td class="w-10 px-4 py-3.5 text-center">
                                <x-checkbox wire:model.live="selectedContacts" value="{{ $contact->id }}" />
                            </td>

                            <!-- Name & Initials Avatar -->
                            <td class="whitespace-nowrap">
                                <div class="flex items-center gap-3 min-w-[180px] max-w-[260px]">
                                    <x-avatar :name="$contact->name ?? $contact->mobile" size="sm" class="shrink-0" />
                                    <div class="min-w-0 flex-1">
                                        <span class="font-semibold text-gray-900 dark:text-white truncate block text-xs" title="{{ $contact->name ?? 'Unnamed Contact' }}">
                                            {{ $contact->name ?? 'Unnamed Contact' }}
                                        </span>
                                        @if ($contact->email)
                                            <span class="text-[11px] text-gray-400 truncate block" title="{{ $contact->email }}">{{ $contact->email }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Phone -->
                            <td class="font-mono text-xs text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                {{ $contact->mobile }}
                            </td>

                            <!-- Phonebook Group -->
                            <td class="text-xs whitespace-nowrap">
                                @if ($contact->phonebook)
                                    <x-tag color="primary" class="truncate max-w-[140px] inline-block">
                                        {{ $contact->phonebook->name }}
                                    </x-tag>
                                @else
                                    <span class="text-gray-400 text-xs">Unassigned</span>
                                @endif
                            </td>

                            <!-- Variables (var1–var5 compact badges) -->
                            <td class="text-xs">
                                <div class="flex flex-wrap gap-1 min-w-[200px] max-w-[320px]">
                                    @if ($contact->var1)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 truncate max-w-[120px]" title="Var 1: {{ $contact->var1 }}">
                                            <strong class="mr-0.5 shrink-0">V1:</strong> <span class="truncate">{{ $contact->var1 }}</span>
                                        </span>
                                    @endif
                                    @if ($contact->var2)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 truncate max-w-[120px]" title="Var 2: {{ $contact->var2 }}">
                                            <strong class="mr-0.5 shrink-0">V2:</strong> <span class="truncate">{{ $contact->var2 }}</span>
                                        </span>
                                    @endif
                                    @if ($contact->var3)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 truncate max-w-[120px]" title="Var 3: {{ $contact->var3 }}">
                                            <strong class="mr-0.5 shrink-0">V3:</strong> <span class="truncate">{{ $contact->var3 }}</span>
                                        </span>
                                    @endif
                                    @if ($contact->var4)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 truncate max-w-[120px]" title="Var 4: {{ $contact->var4 }}">
                                            <strong class="mr-0.5 shrink-0">V4:</strong> <span class="truncate">{{ $contact->var4 }}</span>
                                        </span>
                                    @endif
                                    @if ($contact->var5)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 truncate max-w-[120px]" title="Var 5: {{ $contact->var5 }}">
                                            <strong class="mr-0.5 shrink-0">V5:</strong> <span class="truncate">{{ $contact->var5 }}</span>
                                        </span>
                                    @endif
                                    @if (!$contact->var1 && !$contact->var2 && !$contact->var3 && !$contact->var4 && !$contact->var5)
                                        <span class="text-gray-400 text-[11px]">—</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Source -->
                            <td class="text-xs whitespace-nowrap">
                                <x-tag color="gray" class="capitalize text-[11px]">
                                    {{ $contact->source ?? 'manual' }}
                                </x-tag>
                            </td>

                            <!-- Action Buttons -->
                            <td class="text-right whitespace-nowrap space-x-1 shrink-0">
                                <!-- Chat in Inbox Button -->
                                <a href="{{ route('inbox') }}?mobile={{ $contact->mobile }}" 
                                   class="p-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 rounded-lg inline-flex items-center justify-center transition-colors align-middle" 
                                   title="Chat in Inbox">
                                    <x-ph-icon name="whatsapp-logo" weight="fill" class="text-base" />
                                </a>

                                <!-- Edit Contact -->
                                <button wire:click="editContact({{ $contact->id }})" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg inline-flex items-center justify-center transition-colors align-middle cursor-pointer" title="Edit Contact">
                                    <x-ph-icon name="pencil-simple" weight="bold" class="text-sm" />
                                </button>

                                <!-- Delete Contact -->
                                <button wire:click="deleteContact({{ $contact->id }})" wire:confirm="Are you sure you want to delete this contact?" class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-lg inline-flex items-center justify-center transition-colors align-middle cursor-pointer" title="Delete">
                                    <x-ph-icon name="trash" weight="bold" class="text-sm" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400 text-xs">
                                No contacts found matching the filters. Add contacts or import a CSV file above.
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
                <x-button wire:click="openCreatePhonebookModal" variant="solid" size="md">
                    <x-ph-icon name="plus" weight="bold" class="text-base mr-1.5" />
                    <span>New Group</span>
                </x-button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($phonebooks as $pb)
                    <x-card bodyClass="p-6 flex flex-col justify-between h-full space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-primary-subtle dark:bg-primary/20 text-primary flex items-center justify-center shrink-0">
                                    <x-ph-icon name="folder" weight="duotone" class="text-xl text-primary" />
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
                            <button wire:click="$set('phonebookFilter', '{{ $pb->id }}'); setTab('contacts');" class="text-xs font-bold text-primary hover:underline flex items-center gap-1 cursor-pointer">
                                <span>View Contacts</span>
                                <x-ph-icon name="arrow-right" weight="bold" class="text-xs" />
                            </button>

                            <div class="flex items-center gap-1">
                                <button wire:click="editPhonebook({{ $pb->id }})" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 inline-flex items-center justify-center transition-colors cursor-pointer" title="Edit Group Name">
                                    <x-ph-icon name="pencil-simple" weight="bold" class="text-sm" />
                                </button>
                                <button wire:click="confirmDeletePhonebook({{ $pb->id }})" class="p-1.5 text-rose-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 inline-flex items-center justify-center transition-colors cursor-pointer" title="Delete Group">
                                    <x-ph-icon name="trash" weight="bold" class="text-sm" />
                                </button>
                            </div>
                        </div>
                    </x-card>
                @empty
                    <div class="col-span-full">
                        <x-card bodyClass="p-12 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto text-gray-400">
                                <x-ph-icon name="folder-open" weight="duotone" class="text-3xl text-gray-400" />
                            </div>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white">No Phonebook Groups Yet</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Create targeted audience groups to organize your customer phone list.</p>
                            <x-button wire:click="openCreatePhonebookModal" variant="solid" size="sm">
                                <x-ph-icon name="plus" weight="bold" class="text-xs mr-1" />
                                <span>Create First Group</span>
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
                <x-button wire:click="openFieldModal" variant="solid" size="md">
                    <x-ph-icon name="plus" weight="bold" class="text-base mr-1.5" />
                    <span>New Custom Field</span>
                </x-button>
            </div>

            <x-card gutterless class="overflow-hidden">
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th class="min-w-[150px] whitespace-nowrap">Field Label</th>
                            <th class="min-w-[130px] whitespace-nowrap">Variable Key</th>
                            <th class="min-w-[100px] whitespace-nowrap">Data Type</th>
                            <th class="min-w-[180px] whitespace-nowrap">Template Tag Syntax</th>
                            <th class="min-w-[80px] text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customFields as $field)
                            <tr>
                                <td class="font-bold text-gray-900 dark:text-white text-xs whitespace-nowrap">
                                    {{ $field['label'] }}
                                </td>
                                <td class="whitespace-nowrap">
                                    <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                        {{ $field['key'] }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap">
                                    <x-tag color="blue" class="text-[10px] uppercase font-bold">{{ $field['type'] }}</x-tag>
                                </td>
                                <td class="whitespace-nowrap">
                                    <span class="font-mono text-xs text-primary font-bold">
                                        &#123;&#123;{{ $field['key'] }}&#125;&#125;
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <button wire:click="deleteCustomField('{{ $field['key'] }}')" wire:confirm="Delete this custom field variable?" class="p-1.5 text-rose-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 inline-flex items-center justify-center transition-colors cursor-pointer">
                                        <x-ph-icon name="trash" weight="bold" class="text-sm" />
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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Blacklist & Opt-Out Registry</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Contacts who have sent STOP or unsubscribed from outbound WhatsApp campaigns are excluded automatically.
                    </p>
                </div>
                <x-button wire:click="openBlacklistModal" variant="solid" size="md">
                    <x-ph-icon name="plus" weight="bold" class="text-base mr-1.5" />
                    <span>Add to Blacklist</span>
                </x-button>
            </div>

            <x-card gutterless class="overflow-hidden">
                <x-table hoverable>
                    <thead>
                        <tr>
                            <th class="min-w-[160px] whitespace-nowrap">Phone Number</th>
                            <th class="min-w-[200px] whitespace-nowrap">Opt-Out Reason</th>
                            <th class="min-w-[140px] whitespace-nowrap">Registered Date</th>
                            <th class="min-w-[90px] whitespace-nowrap">Status</th>
                            <th class="min-w-[100px] text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($blacklist as $item)
                            <tr>
                                <td class="font-mono font-bold text-xs text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ $item['number'] }}
                                </td>
                                <td class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $item['reason'] }}
                                </td>
                                <td class="text-xs text-gray-400 font-mono whitespace-nowrap">
                                    {{ $item['added_at'] ?? 'N/A' }}
                                </td>
                                <td class="whitespace-nowrap">
                                    <x-tag color="rose" class="text-[10px] font-bold">Blocked</x-tag>
                                </td>
                                <td class="text-right whitespace-nowrap">
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
                <button wire:click="$set('showContactModal', false)" x-on:click="$dispatch('close-modal', 'contact-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    <x-ph-icon name="x" weight="bold" class="text-lg" />
                </button>
            </div>

            <form wire:submit.prevent="saveContact" class="space-y-4">
                <x-form-item label="WhatsApp Mobile Number (with country code)" :required="true" :error="$errors->first('mobile')">
                    <x-input wire:model="mobile" placeholder="e.g. 8801700000000" :invalid="$errors->has('mobile')" />
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

                <!-- Broadcast Custom Variables (WhatsCRM v6.1.0 var1–var5) -->
                <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">Broadcast Variables (Template Placeholders)</h4>
                        <span class="text-[11px] text-gray-400">Used for &#123;&#123;1&#125;&#125;, &#123;&#123;2&#125;&#125; substitutions</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-form-item label="Variable 1 (e.g. Plan / Role)">
                            <x-input wire:model="var1" placeholder="e.g. VIP, Manager" />
                        </x-form-item>
                        <x-form-item label="Variable 2 (e.g. City / Location)">
                            <x-input wire:model="var2" placeholder="e.g. Dhaka, New York" />
                        </x-form-item>
                        <x-form-item label="Variable 3 (e.g. Order / Account #)">
                            <x-input wire:model="var3" placeholder="e.g. ACC-9842" />
                        </x-form-item>
                        <x-form-item label="Variable 4 (e.g. Date / Expiry)">
                            <x-input wire:model="var4" placeholder="e.g. 2026-10-01" />
                        </x-form-item>
                        <x-form-item label="Variable 5 (e.g. Custom Notes)">
                            <x-input wire:model="var5" placeholder="e.g. High Priority" />
                        </x-form-item>
                        <x-form-item label="Variable 6 (Extra Metadata)">
                            <x-input wire:model="var6" placeholder="e.g. Referred by Partner" />
                        </x-form-item>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="$set('showContactModal', false)" x-on:click="$dispatch('close-modal', 'contact-modal')" variant="default" size="sm">
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
                <button wire:click="$set('showPhonebookModal', false)" x-on:click="$dispatch('close-modal', 'phonebook-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    <x-ph-icon name="x" weight="bold" class="text-lg" />
                </button>
            </div>

            <form wire:submit.prevent="createPhonebook" class="space-y-4">
                <x-form-item label="Group Name" :required="true" :error="$errors->first('newPhonebookName')">
                    <x-input wire:model="newPhonebookName" placeholder="e.g. VIP Retail Clients, Inactive Leads" :invalid="$errors->has('newPhonebookName')" />
                </x-form-item>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="$set('showPhonebookModal', false)" x-on:click="$dispatch('close-modal', 'phonebook-modal')" variant="default" size="sm">
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
    <!-- MODAL: DELETE PHONEBOOK CONFIRMATION                           -->
    <!-- ============================================================== -->
    <x-modal name="delete-phonebook-modal" :show="$showDeletePhonebookModal" focusable>
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
                    <x-ph-icon name="warning-circle" weight="bold" class="text-xl" />
                    <h3 class="font-bold text-base">Delete Phonebook Group</h3>
                </div>
                <button wire:click="$set('showDeletePhonebookModal', false)" x-on:click="$dispatch('close-modal', 'delete-phonebook-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    <x-ph-icon name="x" weight="bold" class="text-lg" />
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <p class="text-gray-600 dark:text-gray-300">
                    Please choose how to handle contacts associated with this group:
                </p>

                <div class="space-y-2">
                    <label class="p-3.5 rounded-xl border flex items-start gap-3 cursor-pointer {{ $deletePhonebookMode === 'unassign' ? 'border-primary bg-primary-subtle/50 text-gray-900 dark:text-white' : 'border-gray-200 dark:border-gray-700' }}">
                        <input type="radio" wire:model.live="deletePhonebookMode" value="unassign" class="mt-0.5">
                        <div>
                            <span class="font-bold block">Keep Contacts (Safe Default)</span>
                            <span class="text-gray-500 dark:text-gray-400 text-[11px]">Contacts will remain in your CRM as Unassigned.</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl border flex items-start gap-3 cursor-pointer {{ $deletePhonebookMode === 'purge' ? 'border-rose-500 bg-rose-50/50 dark:bg-rose-950/20 text-rose-900 dark:text-rose-200' : 'border-gray-200 dark:border-gray-700' }}">
                        <input type="radio" wire:model.live="deletePhonebookMode" value="purge" class="mt-0.5">
                        <div>
                            <span class="font-bold text-rose-600 dark:text-rose-400 block">Permanently Delete Contacts (WhatsCRM v6.1.0 Behavior)</span>
                            <span class="text-gray-500 dark:text-gray-400 text-[11px]">All contacts currently assigned to this phonebook group will be deleted.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                <x-button type="button" wire:click="$set('showDeletePhonebookModal', false)" x-on:click="$dispatch('close-modal', 'delete-phonebook-modal')" variant="default" size="sm">
                    Cancel
                </x-button>
                <x-button type="button" wire:click="deletePhonebookConfirmed" variant="danger" size="sm">
                    Confirm Delete
                </x-button>
            </div>
        </div>
    </x-modal>

    <!-- ============================================================== -->
    <!-- MODAL: CSV IMPORT WIZARD                                       -->
    <!-- ============================================================== -->
    <x-modal name="import-modal" :show="$showImportModal" focusable>
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Import Contacts from CSV</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Supports WhatsCRM v6.1.0 format with custom variables (var1–var5).</p>
                </div>
                <button wire:click="$set('showImportModal', false)" x-on:click="$dispatch('close-modal', 'import-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    <x-ph-icon name="x" weight="bold" class="text-lg" />
                </button>
            </div>

            <!-- Download Sample CSV Banner -->
            <div class="p-3.5 rounded-xl bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800 flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5 text-blue-950 dark:text-blue-200 font-medium">
                    <x-ph-icon name="info" weight="fill" class="text-base text-blue-600 dark:text-blue-400 shrink-0" />
                    <span>Need a template? Download the official CSV format.</span>
                </div>
                <x-button type="button" wire:click="downloadSampleCsv" variant="solid" size="xs">
                    <x-ph-icon name="download-simple" weight="bold" class="text-sm mr-1" />
                    <span>Download Sample CSV</span>
                </x-button>
            </div>

            <!-- Validation Error List from previous upload -->
            @if (!empty($importErrors))
                <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 space-y-2">
                    <div class="flex items-center gap-2 text-rose-800 dark:text-rose-300 font-bold text-xs">
                        <x-ph-icon name="warning-circle" weight="fill" class="text-base text-rose-500 shrink-0" />
                        <span>{{ count($importErrors) }} invalid numbers skipped in CSV:</span>
                    </div>
                    <div class="max-h-36 overflow-y-auto space-y-1 text-[11px] font-mono text-rose-700 dark:text-rose-300">
                        @foreach (array_slice($importErrors, 0, 10) as $err)
                            <div class="flex items-center justify-between bg-white dark:bg-gray-900 px-2 py-1 rounded border border-rose-100 dark:border-rose-900">
                                <span>Row {{ $err['row'] }}: {{ $err['name'] ?: 'No Name' }} ({{ $err['mobile'] ?: 'Empty' }})</span>
                                <span class="text-rose-500 font-sans text-[10px]">{{ $err['reason'] }}</span>
                            </div>
                        @endforeach
                        @if (count($importErrors) > 10)
                            <p class="text-[10px] text-gray-500 pt-1">+ {{ count($importErrors) - 10 }} more rows...</p>
                        @endif
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="importCsv" class="space-y-4">
                <x-form-item label="Target Phonebook Group (Optional)">
                    <x-select wire:model="importPhonebookId" placeholder="Unassigned">
                        <option value="">No Group (Unassigned)</option>
                        @foreach ($phonebooks as $pb)
                            <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                        @endforeach
                    </x-select>
                </x-form-item>

                <!-- Interactive Upload Area with Live Progress & File Preview -->
                <div
                    x-data="{ isUploading: false, progress: 0 }"
                    x-on:open-modal.window="isUploading = false; progress = 0"
                    x-on:close-modal.window="isUploading = false; progress = 0"
                    x-on:csv-reset.window="isUploading = false; progress = 0"
                    x-on:livewire-upload-start="isUploading = true; progress = 0"
                    x-on:livewire-upload-finish="isUploading = false; progress = 0"
                    x-on:livewire-upload-error="isUploading = false; progress = 0"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    class="space-y-3"
                >
                    <!-- 1. Selected File Preview Card -->
                    @if ($csvPreview)
                        <div class="p-4 rounded-2xl border border-emerald-300 dark:border-emerald-800 bg-emerald-50/70 dark:bg-emerald-950/30 space-y-3 shadow-xs">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                                        <x-ph-icon name="file-text" weight="duotone" class="text-2xl" />
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate max-w-[240px] sm:max-w-[320px]">
                                                {{ $csvPreview['filename'] }}
                                            </h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 shrink-0">
                                                ✓ Ready
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $csvPreview['size'] }} &bull; <strong class="text-emerald-600 dark:text-emerald-400">{{ number_format($csvPreview['total_rows']) }} contacts detected</strong>
                                        </p>
                                    </div>
                                </div>

                                <button type="button" wire:click="clearCsvFile" class="text-xs text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 font-semibold p-1 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors flex items-center gap-1 shrink-0 cursor-pointer" title="Remove and choose another file">
                                    <x-ph-icon name="trash" weight="bold" class="text-sm" />
                                    <span>Remove</span>
                                </button>
                            </div>

                            <!-- Detected Headers Tags -->
                            <div class="pt-2 border-t border-emerald-200/60 dark:border-emerald-800/60">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-1.5">Detected Columns Mapping:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($csvPreview['headers'] as $col)
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-medium {{ in_array($col, ['mobile', 'phone', 'whatsapp', 'number', 'phone number', 'contact']) ? 'bg-emerald-200 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-200 font-bold border border-emerald-400' : (str_starts_with($col, 'var') ? 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300') }}">
                                            {{ $col }}
                                            @if (in_array($col, ['mobile', 'phone', 'whatsapp', 'number', 'phone number', 'contact']))
                                                <x-ph-icon name="check" weight="bold" class="text-xs inline ml-0.5 text-emerald-700 dark:text-emerald-300" />
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            @if (!empty($csvPreview['sample_rows']))
                                <div class="pt-2 border-t border-emerald-200/60 dark:border-emerald-800/60">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-1">First Rows Preview:</span>
                                    <div class="space-y-1">
                                        @foreach ($csvPreview['sample_rows'] as $sRow)
                                            <div class="text-[11px] font-mono text-gray-600 dark:text-gray-300 bg-white/70 dark:bg-gray-900/60 px-2 py-0.5 rounded truncate border border-emerald-100 dark:border-emerald-900">
                                                {{ implode(' | ', array_filter($sRow)) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (!$csvPreview['has_mobile_col'])
                                <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 text-amber-800 dark:text-amber-200 text-xs flex items-center gap-2">
                                    <x-ph-icon name="warning" weight="fill" class="text-base text-amber-500 shrink-0" />
                                    <span><strong>Column Warning:</strong> No "mobile" or "phone" header detected. The import may skip rows without valid phone digits.</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- 2. Live Uploading Progress Indicator (Only when uploading and no preview yet) -->
                        <div x-show="isUploading" class="p-6 rounded-2xl border-2 border-dashed border-primary bg-primary-subtle/30 flex flex-col items-center justify-center text-center space-y-3 transition-all" style="display: none;">
                            <div class="w-12 h-12 rounded-2xl bg-primary text-white flex items-center justify-center shadow-md">
                                <x-ph-icon name="cloud-arrow-up" weight="duotone" class="text-2xl animate-bounce" />
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-gray-900 dark:text-white">Uploading & Analyzing CSV File...</p>
                                <p class="text-[11px] text-primary font-semibold font-mono" x-text="`${progress}% uploaded`"></p>
                            </div>
                            <div class="w-full max-w-xs bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                                <div class="bg-primary h-full rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>

                        <!-- 3. Empty Dropzone State (When NOT uploading and no preview yet) -->
                        <div x-show="!isUploading">
                            <label for="csvFileInput" class="cursor-pointer block border-2 border-dashed border-gray-300 dark:border-gray-700 hover:border-primary dark:hover:border-primary rounded-2xl p-7 text-center transition-all group bg-gray-50/50 hover:bg-primary-subtle/10 dark:bg-gray-800/40">
                                <input 
                                    id="csvFileInput" 
                                    type="file" 
                                    wire:model="csvFile" 
                                    accept=".csv,text/csv,text/plain" 
                                    class="sr-only" 
                                    x-on:change="isUploading = true"
                                    x-on:csv-reset.window="$el.value = ''"
                                />
                                <div class="flex flex-col items-center justify-center pointer-events-none">
                                    <div class="w-14 h-14 mb-3 rounded-2xl bg-primary-subtle text-primary flex items-center justify-center group-hover:scale-105 transition-transform duration-200 shadow-2xs">
                                        <x-ph-icon name="cloud-arrow-up" weight="duotone" class="text-3xl" />
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100 group-hover:text-primary transition-colors">
                                        Click to browse or drop CSV file here
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        CSV columns: name, mobile, var1, var2, var3, var4, var5 (Max: 10MB)
                                    </span>
                                </div>
                            </label>
                        </div>
                    @endif

                    @error('csvFile')
                        <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1">
                            <x-ph-icon name="warning-circle" weight="fill" class="text-sm shrink-0" />
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="$set('showImportModal', false)" x-on:click="$dispatch('close-modal', 'import-modal')" variant="default" size="sm">
                        Close
                    </x-button>
                    <x-button 
                        type="submit" 
                        variant="solid" 
                        size="sm" 
                        :disabled="!$csvFile || empty($csvPreview)"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="importCsv" class="flex items-center gap-1.5">
                            <x-ph-icon name="upload-simple" weight="bold" class="text-sm" />
                            <span>{{ $csvPreview && !empty($csvPreview['total_rows']) ? 'Import ' . number_format($csvPreview['total_rows']) . ' Contacts' : 'Start Import' }}</span>
                        </span>
                        <span wire:loading wire:target="importCsv" class="flex items-center gap-1.5">
                            <x-ph-icon name="spinner" weight="bold" class="text-sm animate-spin mr-1.5" />
                            <span>Importing Contacts...</span>
                        </span>
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- ============================================================== -->
    <!-- MODAL: DEVICE CONTACTS SYNC (BAILEYS WHATSAPP)                 -->
    <!-- ============================================================== -->
    <x-modal name="device-sync-modal" :show="$showDeviceSyncModal" focusable>
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center font-bold">
                        <x-ph-icon name="whatsapp-logo" weight="fill" class="text-lg" />
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-gray-900 dark:text-white">Sync WhatsApp Device Contacts</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Import chats and phone numbers from your connected WhatsApp instance.</p>
                    </div>
                </div>
                <button wire:click="$set('showDeviceSyncModal', false)" x-on:click="$dispatch('close-modal', 'device-sync-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    <x-ph-icon name="x" weight="bold" class="text-lg" />
                </button>
            </div>

            @if ($syncSummary)
                <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between">
                    <span>Sync Complete: <strong>{{ $syncSummary['imported'] }}</strong> new contacts created, <strong>{{ $syncSummary['updated'] }}</strong> existing updated.</span>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono">{{ $syncSummary['instance_name'] }}</span>
                </div>
            @endif

            @if ($activeInstances->isEmpty())
                <div class="p-6 text-center rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs space-y-2">
                    <p class="font-bold">No Active WhatsApp QR Sessions</p>
                    <p class="text-gray-500 dark:text-gray-400">Link a WhatsApp device in Devices to enable 1-click contact synchronization.</p>
                    <a href="{{ route('devices') }}" class="inline-block mt-2 px-3 py-1.5 rounded-full bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700">Go to Devices</a>
                </div>
            @else
                <form wire:submit.prevent="syncFromDevice" class="space-y-4">
                    <x-form-item label="Source WhatsApp Device" :required="true">
                        <x-select wire:model="syncInstanceId">
                            @foreach ($activeInstances as $inst)
                                <option value="{{ $inst->uniqueId }}">
                                    📱 {{ $inst->name ?: $inst->number ?: 'WhatsApp Session' }} ({{ $inst->number }})
                                </option>
                            @endforeach
                        </x-select>
                    </x-form-item>

                    <x-form-item label="Target Phonebook Group (Optional)">
                        <x-select wire:model="syncPhonebookId" placeholder="Unassigned">
                            <option value="">No Group (Unassigned)</option>
                            @foreach ($phonebooks as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                            @endforeach
                        </x-select>
                    </x-form-item>

                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <x-button type="button" wire:click="$set('showDeviceSyncModal', false)" x-on:click="$dispatch('close-modal', 'device-sync-modal')" variant="default" size="sm">
                            Close
                        </x-button>
                        <x-button type="submit" variant="solid" size="sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Sync Contacts Now</span>
                            <span wire:loading>Syncing...</span>
                        </x-button>
                    </div>
                </form>
            @endif
        </div>
    </x-modal>

    <!-- ============================================================== -->
    <!-- MODAL: ADD CUSTOM FIELD                                        -->
    <!-- ============================================================== -->
    <x-modal name="custom-field-modal" :show="$showFieldModal" focusable>
        <div class="p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-base text-gray-900 dark:text-white">Create Custom Variable Field</h3>
                <button wire:click="$set('showFieldModal', false)" x-on:click="$dispatch('close-modal', 'custom-field-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    <x-ph-icon name="x" weight="bold" class="text-lg" />
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
                    <x-button type="button" wire:click="$set('showFieldModal', false)" x-on:click="$dispatch('close-modal', 'custom-field-modal')" variant="default" size="sm">
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
                <button wire:click="$set('showBlacklistModal', false)" x-on:click="$dispatch('close-modal', 'blacklist-modal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                    <x-ph-icon name="x" weight="bold" class="text-lg" />
                </button>
            </div>

            <form wire:submit.prevent="addToBlacklist" class="space-y-4">
                <x-form-item label="Phone Number" :required="true" :error="$errors->first('newBlacklistNumber')">
                    <x-input wire:model="newBlacklistNumber" placeholder="e.g. 8801700000000" class="font-mono text-xs" :invalid="$errors->has('newBlacklistNumber')" />
                </x-form-item>

                <x-form-item label="Opt-Out Reason" :required="true" :error="$errors->first('newBlacklistReason')">
                    <x-input wire:model="newBlacklistReason" placeholder="e.g. Customer sent STOP on broadcast" :invalid="$errors->has('newBlacklistReason')" />
                </x-form-item>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button type="button" wire:click="$set('showBlacklistModal', false)" x-on:click="$dispatch('close-modal', 'blacklist-modal')" variant="default" size="sm">
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
