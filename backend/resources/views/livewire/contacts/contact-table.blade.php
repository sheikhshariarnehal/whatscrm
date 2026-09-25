<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Contacts & Phonebook</h1>
                <x-tag color="primary" class="font-bold">{{ $contacts->total() }} Total</x-tag>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage audience phone numbers, groups, and audience segmentation</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <x-button wire:click="$set('showPhonebookModal', true)" variant="default" size="sm">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>New Group</span>
            </x-button>

            <x-button wire:click="$set('showImportModal', true)" variant="default" size="sm">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import CSV</span>
            </x-button>

            <x-button wire:click="openCreateContactModal" variant="solid" size="sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Contact</span>
            </x-button>
        </div>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Contacts Table Card -->
    <x-card gutterless class="overflow-hidden">
        <!-- Integrated Table Toolbar (Search & Filter Header) -->
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
                           placeholder="Search contacts by name, mobile, email..." 
                           style="padding-left: 2.35rem; padding-right: 2rem;"
                           class="input input-sm w-full">
                    @if ($search)
                        <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 z-10" title="Clear search">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>

                <!-- Phonebook Group Filter Dropdown -->
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 z-10">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                    <select wire:model.live="phonebookFilter" 
                            style="padding-left: 2.25rem; padding-right: 2.25rem;"
                            class="select select-sm appearance-none">
                        <option value="">All Phonebooks ({{ $phonebooks->count() }})</option>
                        @foreach ($phonebooks as $pb)
                            <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400 z-10">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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
        <x-table hoverable>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>WhatsApp Phone</th>
                    <th>Email</th>
                    <th>Phonebook</th>
                    <th>Source</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contacts as $contact)
                    <tr>
                        <!-- Name & Initials Avatar -->
                        <td class="flex items-center gap-3">
                            <x-avatar :name="$contact->name ?? $contact->mobile" size="sm" />
                            <span class="font-semibold text-gray-900 dark:text-white truncate">
                                {{ $contact->name ?? 'Unnamed Contact' }}
                            </span>
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
                            <x-tag color="gray">
                                {{ $contact->source }}
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
                        <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
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

    <!-- Create / Edit Contact Modal -->
    @if ($showContactModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">
                        {{ $editingContactId ? 'Edit Contact' : 'Create New Contact' }}
                    </h3>
                    <button wire:click="$set('showContactModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveContact" class="space-y-4 pt-2">
                    <x-form-item label="WhatsApp Phone Number" :required="true" :error="$errors->first('mobile')">
                        <x-input wire:model="mobile" placeholder="+15551234567" prefix-icon="chat" :invalid="$errors->has('mobile')" />
                    </x-form-item>

                    <x-form-item label="Full Name" :error="$errors->first('name')">
                        <x-input wire:model="name" placeholder="e.g. Jane Doe" prefix-icon="contacts" :invalid="$errors->has('name')" />
                    </x-form-item>

                    <x-form-item label="Email Address" :error="$errors->first('email')">
                        <x-input wire:model="email" type="email" placeholder="jane@example.com" prefix-icon="broadcast" :invalid="$errors->has('email')" />
                    </x-form-item>

                    <x-form-item label="Phonebook Group">
                        <x-select wire:model="phonebook_id" placeholder="No Group">
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
                            Save Contact
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Create Phonebook Group Modal -->
    @if ($showPhonebookModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Create Phonebook Group</h3>
                    <button wire:click="$set('showPhonebookModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="createPhonebook" class="space-y-4 pt-2">
                    <x-form-item label="Group Name" :required="true" :error="$errors->first('newPhonebookName')">
                        <x-input wire:model="newPhonebookName" placeholder="e.g. VIP Clients, Summer Campaign" prefix-icon="broadcast" :invalid="$errors->has('newPhonebookName')" />
                    </x-form-item>

                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <x-button type="button" wire:click="$set('showPhonebookModal', false)" variant="default" size="sm">
                            Cancel
                        </x-button>
                        <x-button type="submit" variant="solid" size="sm">
                            Create Group
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Import CSV Modal -->
    @if ($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Import Contacts from CSV</h3>
                    <button wire:click="$set('showImportModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="importCsv" class="space-y-4 pt-2">
                    <x-form-item :error="$errors->first('csvFile')">
                        <x-upload 
                            wire:model="csvFile" 
                            accept=".csv,text/csv" 
                            title="Drop CSV file here or click to browse" 
                            description="CSV must contain name and mobile (phone) columns"
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
        </div>
    @endif
</div>
